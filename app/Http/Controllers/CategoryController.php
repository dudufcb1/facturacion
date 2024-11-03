<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
  public function index(Request $request)
  {
    $query = Category::query();

    if ($request->has('search')) {
      $searchTerm = $request->search;
      $query->where('name', 'LIKE', "%{$searchTerm}%");
    }

    if ($request->has('status') && $request->status !== '') {
      $query->where('status', $request->status);
    }

    $categories = $query->paginate(10)->withQueryString();
    return view('categories.index', compact('categories'));
  }

  public function create()
  {
    return view('categories.create');
  }

  public function store(CategoryRequest $request)
  {
    Category::create($request->validated());
    return redirect()->route('categories.index')
      ->with('success', 'Categoría creada exitosamente.');
  }

  public function show(Category $category)
  {
    return view('categories.show', compact('category'));
  }

  public function edit(Category $category)
  {
    return view('categories.edit', compact('category'));
  }

  public function update(CategoryRequest $request, Category $category)
  {
    $category->update($request->validated());
    return redirect()->route('categories.index')
      ->with('success', 'Categoría actualizada exitosamente.');
  }

  public function destroy(Category $category)
  {
    $category->delete();
    return redirect()->route('categories.index')
      ->with('success', 'Categoría eliminada exitosamente.');
  }
}
