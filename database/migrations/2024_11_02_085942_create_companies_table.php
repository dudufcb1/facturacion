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
      Schema::create('companies', function (Blueprint $table) {
        $table->id();
        // Datos básicos de la empresa
        $table->string('name');                    // Nombre del taller/empresa
        $table->boolean('default');                // Default company flag
        $table->string('owner_name')->nullable();  // Nombre del propietario/ingeniero

        // Información de contacto
        $table->string('phones')->nullable();      // Teléfonos (pueden ser varios)
        $table->string('address');                 // Dirección completa

        // Información legal
        $table->string('ruc')->unique();           // RUC de la empresa

        // Información adicional
        $table->text('services')->nullable();      // Descripción de servicios que ofrece
        $table->string('logo_path')->nullable();   // Ruta del logo de la empresa

        // Información de facturación
        $table->string('invoice_series')->nullable(); // Serie de facturas/proformas
        $table->integer('last_invoice_number')->default(0); // Último número de factura

        // Control de tiempo
        $table->timestamps();
        $table->softDeletes();
      });
    }

    public function down()
    {
      Schema::dropIfExists('companies');
    }
  };
