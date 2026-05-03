<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Product::where('status', 'active')
            ->distinct()
            ->pluck('category');

        return view('shop.index', compact('products', 'categories'));
    }

    public function category($category)
    {
        $products = Product::where('status', 'active')
            ->where('category', $category)
            ->paginate(12);

        $categories = Product::where('status', 'active')
            ->distinct()
            ->pluck('category');

        return view('shop.index', compact('products', 'categories', 'category'));
    }

    public function show(Product $product)
    {
        if ($product->status !== 'active') {
            abort(404);
        }

        $relatedProducts = Product::where('status', 'active')
            ->where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }

    public function create()
    {
        return view('shop.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products',
            'category' => 'required|string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,active,inactive',
        ]);

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $filename = time().'_'.$file->hashName();
            $file->move(public_path('uploads/products'), $filename);
            $validated['featured_image'] = 'uploads/products/'.$filename;
        }

        $validated['user_id'] = auth()->id();
        Product::create($validated);

        return redirect()->route('shop.index')->with('success', 'Produk berhasil dibuat!');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        return view('shop.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,'.$product->id,
            'category' => 'required|string|max:50',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,active,inactive',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image && file_exists(public_path($product->featured_image))) {
                unlink(public_path($product->featured_image));
            }
            $file = $request->file('featured_image');
            $filename = time().'_'.$file->hashName();
            $file->move(public_path('uploads/products'), $filename);
            $validated['featured_image'] = 'uploads/products/'.$filename;
        }

        $product->update($validated);

        return redirect()->route('shop.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->featured_image && file_exists(public_path($product->featured_image))) {
            unlink(public_path($product->featured_image));
        }

        $product->delete();

        return redirect()->route('shop.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function addToCart(Request $request, Product $product)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('message', 'Silakan login untuk menambah ke keranjang');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        CartItem::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'quantity' => $validated['quantity'],
                'price' => $product->price,
            ]
        );

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambah ke keranjang!');
    }
}
