<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Student;
use Illuminate\Http\Request;

class ProductAssignmentController extends Controller
{
    public function create(Student $student)
    {
        $products = Product::all();

        return view('students.assign-products', compact('student', 'products'));
    }

    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'products' => 'required|array',
        ]);

        $student->products()->sync($validated['products']);

        return redirect()->route('students.index')->with('success', 'محصولات با موفقیت تخصیص یافتند.');
    }
}
