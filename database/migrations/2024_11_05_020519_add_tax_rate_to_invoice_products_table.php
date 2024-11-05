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
    Schema::table('invoice_products', function (Blueprint $table) {
      $table->decimal('tax_rate', 8, 2)->default(0)->after('price'); // Puedes ajustar la posición usando after()
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('invoice_products', function (Blueprint $table) {
      $table->dropColumn('tax_rate');
    });
  }
};
