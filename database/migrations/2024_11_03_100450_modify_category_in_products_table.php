<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('products', function (Blueprint $table) {
      // Primero eliminamos la columna existente
      $table->dropColumn('category');

      // Luego añadimos la nueva columna como foreign key
      $table->foreignId('category_id')
        ->nullable()
        ->constrained('categories')
        ->onDelete('set null');
    });
  }

  public function down(): void
  {
    Schema::table('products', function (Blueprint $table) {
      // Eliminamos la foreign key y la columna
      $table->dropForeign(['category_id']);
      $table->dropColumn('category_id');

      // Restauramos la columna original
      $table->string('category')->nullable();
    });
  }
};
