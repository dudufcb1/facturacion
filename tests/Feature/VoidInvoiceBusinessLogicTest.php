<?php

use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Company;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'administrador']);
    $this->company = Company::factory()->create();
    $this->client = Client::factory()->create();
});

test('can void invoice without payments', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'status' => 'issued',
        'total' => 100.00
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('invoices.void', $invoice), [
            'void_reason' => 'Factura emitida por error'
        ]);

    $response->assertStatus(302);

    $invoice->refresh();
    expect($invoice->status)->toBe('cancelled');
    expect($invoice->voided_at)->not->toBeNull();
    expect($invoice->voided_by)->toBe($this->admin->id);
    expect($invoice->void_reason)->toBe('Factura emitida por error');
});

test('can void invoice with payments and payments are also voided', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'status' => 'paid',
        'total' => 100.00
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'user_id' => $this->admin->id,
        'amount_paid' => 100.00,
        'converted_amount' => 100.00,
        'status' => 'completed'
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('invoices.void', $invoice), [
            'void_reason' => 'Anulación de factura con pagos'
        ]);

    $response->assertStatus(302);

    $invoice->refresh();
    $payment->refresh();

    expect($invoice->status)->toBe('cancelled');
    expect($payment->status)->toBe('cancelled');
    expect($payment->voided_at)->not->toBeNull();
});

test('cannot void already voided invoice', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'status' => 'cancelled',
        'voided_at' => now(),
        'voided_by' => $this->admin->id
    ]);

    $response = $this->actingAs($this->admin)
        ->patch(route('invoices.void', $invoice), [
            'void_reason' => 'Intento de anular factura ya anulada'
        ]);

    $response->assertStatus(422); // Unprocessable Entity
});

test('void reason is required and validated', function () {
    $invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'status' => 'issued'
    ]);

    // Test sin razón
    $response = $this->actingAs($this->admin)
        ->patch(route('invoices.void', $invoice), []);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('void_reason');

    // Test con razón muy corta
    $response = $this->actingAs($this->admin)
        ->patch(route('invoices.void', $invoice), [
            'void_reason' => 'corta'
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('void_reason');

    // Test con razón muy larga
    $response = $this->actingAs($this->admin)
        ->patch(route('invoices.void', $invoice), [
            'void_reason' => str_repeat('a', 501)
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('void_reason');
});

test('invoice model helper methods work correctly', function () {
    // Test canBeVoided
    $issuedInvoice = Invoice::factory()->create(['status' => 'issued']);
    $debtInvoice = Invoice::factory()->create(['status' => 'debt']);
    $paidInvoice = Invoice::factory()->create(['status' => 'paid']);
    $voidedInvoice = Invoice::factory()->create([
        'status' => 'cancelled',
        'voided_at' => now()
    ]);

    expect($issuedInvoice->canBeVoided())->toBeTrue();
    expect($debtInvoice->canBeVoided())->toBeTrue();
    expect($paidInvoice->canBeVoided())->toBeFalse();
    expect($voidedInvoice->canBeVoided())->toBeFalse();

    // Test isVoided
    expect($voidedInvoice->isVoided())->toBeTrue();
    expect($issuedInvoice->isVoided())->toBeFalse();
});
