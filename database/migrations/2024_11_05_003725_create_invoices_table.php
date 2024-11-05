<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained();
      $table->foreignId('client_id')->constrained();
      $table->string('invoice_series');
      $table->integer('invoice_number');
      $table->enum('status', ['issued', 'debt', 'paid', 'cancelled']);
      $table->date('due_date')->nullable();
      $table->enum('payment_type', ['cash', 'credit']);
      $table->integer('credit_days')->nullable();
      $table->text('notes')->nullable();
      $table->decimal('subtotal', 10, 2);
      $table->decimal('tax', 10, 2);
      $table->decimal('total', 10, 2);
      $table->enum('currency', ['NIO', 'USD']);
      $table->decimal('exchange_rate', 10, 4)->nullable();
      $table->string('reference_number')->nullable();
      $table->timestamp('issued_at');
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('invoices');
  }
};
