<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $query = Product::query();

    // Búsqueda por término
    if ($request->has('search')) {
      $searchTerm = $request->search;
      $query->where(function ($q) use ($searchTerm) {
        $q->where('name', 'LIKE', "%{$searchTerm}%")
          ->orWhere('code', 'LIKE', "%{$searchTerm}%")
          ->orWhere('category', 'LIKE', "%{$searchTerm}%");
      });
    }

    // Filtro por estado
    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    // Obtener productos con paginación
    $products = $query->paginate(10)
      ->withQueryString(); // Mantiene los parámetros de búsqueda en la paginación

    return view('products.index', compact('products'));
  }

  public function create()
  {
    $categories = Category::where('status', 'active')->get();
    return view('products.create', compact('categories'));
  }

  public function store(ProductRequest $request)
  {
    Product::create($request->validated());
    return redirect()->route('products.index')
      ->with('success', 'Producto creado exitosamente.');
  }

  public function show(Product $product)
  {
    return view('products.show', compact('product'));
  }

  public function edit(Product $product)
  {
    $categories = Category::where('status', 'active')->get();
    return view('products.edit', compact('product', 'categories'));
  }

  public function update(ProductRequest $request, Product $product)
  {
    $product->update($request->validated());
    return redirect()->route('products.index')
      ->with('success', 'Producto actualizado exitosamente.');
  }

  public function destroy(Product $product)
  {
    $product->delete();
    return redirect()->route('products.index')
      ->with('success', 'Producto eliminado exitosamente.');
  }
  public function generateQR(Product $product)
  {
    $productInfo = [
      'Código: ' . $product->code,
      'Nombre: ' . $product->name,
      'Precio: ' . $product->currency . ' ' . number_format($product->unit_price, 2),
      'Moneda: ' . $product->currency  // Aquí estaba el error
    ];

    $qrText = implode("\n", $productInfo);

    $qrCode = QrCode::size(250)
      ->format('svg')
      ->errorCorrection('H')
      ->generate($qrText);

    $info = "
          <div class='space-y-2'>
              <p class='font-bold text-lg'>{$product->name}</p>
              <p class='text-gray-600'>Código: {$product->code}</p>
              <p class='text-gray-600'>Precio: {$product->currency} " . number_format($product->unit_price, 2) . "</p>
              <p class='text-gray-600'>Moneda: {$product->currency}</p>
          </div>
      ";

    return response()->json([
      'qr' => base64_encode($qrCode),
      'info' => $info
    ]);
  }
}
