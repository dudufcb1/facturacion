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
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->string('code')->unique(); // SKU
      $table->string('name');
      $table->text('description')->nullable();
      $table->text('notes')->nullable();
      $table->decimal('unit_price', 10, 2);
      $table->enum('currency', ['NIO', 'USD'])->default('NIO'); // Agregamos esta línea
      $table->integer('stock');
      $table->enum('status', ['active', 'inactive'])->default('active');
      $table->string('category')->nullable();
      $table->decimal('tax_rate', 5, 2);
      $table->string('unit_of_measure');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
