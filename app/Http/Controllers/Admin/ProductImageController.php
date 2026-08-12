<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        if (str_starts_with($image->image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image->image));
        }

        $image->delete();

        return back()->with('success', 'Image removed.');
    }
}
