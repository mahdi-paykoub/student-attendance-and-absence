<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentProductController extends Controller
{
    public function create()
    {
        $students = Student::all();
        $products = Product::all();
        return view('student_product.create', compact('students', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'product_id' => 'required|exists:products,id',
            'payment_type' => 'required|in:cash,installment',
            'pos_type' => 'nullable|string|max:255',
            'card_type' => 'nullable|string|max:255',
            'check_owner' => 'nullable|string|max:255',
            'check_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'check_phone' => 'nullable|string|max:15',
        ]);

        // آپلود عکس چک در صورت وجود
        if ($request->hasFile('check_image')) {
            $file = $request->file('check_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('student_checks', $filename, 'private');
            $validated['check_image'] = $path;
        }

        $student = Student::findOrFail($request->student_id);
        $student->products()->attach($request->product_id, $validated);

        return redirect()->back()->with('success', 'محصول با موفقیت به دانش‌آموز تخصیص داده شد.');
    }
}
