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
    Schema::create('clients', function (Blueprint $table) {
      $table->id();
      $table->enum('document_type', ['DNI', 'RUC']);
      $table->string('document_number')->unique();
      $table->string('name')->nullable(); // For individual clients
      $table->string('business_name')->nullable(); // For business clients
      $table->string('address')->nullable();
      $table->string('phone')->nullable();
      $table->string('email')->unique()->nullable();
      $table->enum('status', ['active', 'inactive'])->default('active');
      $table->text('notes')->nullable();
      $table->enum('customer_type', ['regular', 'premium', 'vip'])->default('regular');
      $table->string('custom_field_1')->nullable();
      $table->string('custom_field_2')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('clients');
  }
};
