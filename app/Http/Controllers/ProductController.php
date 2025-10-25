<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'محصول با موفقیت ثبت شد.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'محصول با موفقیت ویرایش شد.');
    }

    public function destroy(Product $product)
    {
        // اگر محصول به دانش‌آموزی اختصاص داده شده باشد
        if ($product->students()->exists()) {
            return redirect()
                ->route('products.index')
                ->with('error', 'این محصول به دانش‌آموز(ان) اختصاص داده شده است و قابل حذف نیست.');
        }

        // در غیر این صورت حذف انجام می‌شود
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'محصول با موفقیت حذف شد.');
    }

    public function students(Product $product)
    {
        $students = $product->students()
            ->with('grade', 'major') // اگر روابطشون وجود داره
            ->get();

        return view('products.students', compact('product', 'students'));
    }
}
