<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up()
  {
    Schema::create('payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');

      // Monto recibido en la moneda original
      $table->decimal('amount_paid', 12, 2);
      // Moneda en la que se recibió el pago
      $table->string('payment_currency', 3);
      // Tipo de cambio al momento del pago
      $table->decimal('exchange_rate', 10, 4);

      // Monto equivalente en la moneda de la factura
      $table->decimal('converted_amount', 12, 2);
      // Moneda de la factura (referencia)
      $table->string('invoice_currency', 3);

      $table->string('payment_method');
      $table->string('reference_number')->nullable();
      $table->string('reference_document')->nullable();
      $table->string('partial_token')->unique();
      $table->string('final_token')->nullable();
      $table->text('notes')->nullable();
      $table->string('status')->default('completed');
      $table->datetime('payment_date');
      $table->string('bank_name')->nullable();
      $table->string('bank_account')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down()
  {
    Schema::dropIfExists('payments');
  }
};
