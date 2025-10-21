<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function editExamProduct()
    {
        $products = Product::all();
        $mandatoryProductId = Setting::get('mandatory_exam_product_id');

        return view('settings.settings_exam_product', compact('products', 'mandatoryProductId'));
    }

    public function updateExamProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        Setting::set('mandatory_exam_product_id', $request->product_id);

        return redirect()->back()->with('success', 'تنظیمات آزمون با موفقیت ذخیره شد.');
    }
}
