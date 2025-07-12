<?php

use App\Models\User;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Company;

beforeEach(function () {
    // Crear datos de prueba
    $this->company = Company::factory()->create();
    $this->client = Client::factory()->create();
    $this->invoice = Invoice::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'status' => 'issued'
    ]);
});

test('administrator can void invoice', function () {
    $admin = User::factory()->create(['role' => 'administrador']);

    $response = $this->actingAs($admin)
        ->patch(route('invoices.void', $this->invoice), [
            'void_reason' => 'Factura emitida por error, cliente solicitó anulación'
        ]);

    $response->assertStatus(302); // Redirect after success

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('cancelled');
    expect($this->invoice->voided_at)->not->toBeNull();
    expect($this->invoice->voided_by)->toBe($admin->id);
});

test('owner can void invoice', function () {
    $owner = User::factory()->create(['role' => 'propietario']);

    $response = $this->actingAs($owner)
        ->patch(route('invoices.void', $this->invoice), [
            'void_reason' => 'Anulación solicitada por el propietario del negocio'
        ]);

    $response->assertStatus(302);

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('cancelled');
});

test('cashier cannot void invoice', function () {
    $cashier = User::factory()->create(['role' => 'cajero']);

    $response = $this->actingAs($cashier)
        ->patch(route('invoices.void', $this->invoice), [
            'void_reason' => 'Intento de anulación por cajero'
        ]);

    $response->assertStatus(403); // Forbidden

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('issued'); // No cambió
});

test('controller cannot void invoice', function () {
    $controller = User::factory()->create(['role' => 'contralor']);

    $response = $this->actingAs($controller)
        ->patch(route('invoices.void', $this->invoice), [
            'void_reason' => 'Intento de anulación por contralor'
        ]);

    $response->assertStatus(403);

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('issued');
});

test('accountant cannot void invoice', function () {
    $accountant = User::factory()->create(['role' => 'contador']);

    $response = $this->actingAs($accountant)
        ->patch(route('invoices.void', $this->invoice), [
            'void_reason' => 'Intento de anulación por contador'
        ]);

    $response->assertStatus(403);

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('issued');
});

test('unauthenticated user cannot void invoice', function () {
    $response = $this->patch(route('invoices.void', $this->invoice), [
        'void_reason' => 'Intento de anulación sin autenticación'
    ]);

    $response->assertStatus(302); // Redirect to login
    $response->assertRedirect(route('login'));

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('issued');
});
