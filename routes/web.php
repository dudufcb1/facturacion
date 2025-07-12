<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
  return view('welcome');
});
/*Rutas*/
Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/dashboard', function () {
    return view('dashboard');
  })->name('dashboard');

  // Profile routes
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  // Resource routes
  Route::resource('companies', CompanyController::class);
  Route::resource('products', ProductController::class);
  Route::resource('categories', CategoryController::class);
  Route::resource('invoices', InvoiceController::class);
  Route::resource('clients', ClientController::class);
  Route::resource('payments', PaymentController::class);

  // Helper routes
  Route::get('/products/{product}/qr', [ProductController::class, 'generateQR'])->name('products.qr');
  Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePDF'])->name('invoices.pdf');

  // Rutas de anulación con middleware de autorización
  Route::patch('invoices/{invoice}/void', [InvoiceController::class, 'void'])
    ->name('invoices.void')
    ->middleware('can.void.invoices');
});

require __DIR__ . '/auth.php';
