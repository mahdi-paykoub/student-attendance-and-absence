<?php

namespace App\Http\Controllers;

use App\Models\Check;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function showImage(Check $check)
    {
        if (!$check->check_image || !Storage::disk('private')->exists($check->check_image)) {
            abort(404, 'عکس چک یافت نشد.');
        }

        return response()->file(
            Storage::disk('private')->path($check->check_image)
        );
    }
    public function clear(Check $check)
    {
        $check->update([
            'is_cleared' => 1
        ]);

        return back()->with('success', 'چک با موفقیت وصول شد');
    }
}
