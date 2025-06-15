<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Validator;



class ProductController extends Controller
{
    public function index() {
        $products = Product::all();
        return view('/products/index', compact('products'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'price' => 'required|regex:/^\d*(\.\d{2})?$/|min:0.01',
        ]);

        if ($validator->fails()) {
            $categories = Category::all();

            $errors = new ViewErrorBag();
            $errors->put('default', $validator->errors());

            return view('/products/new', compact(['request', 'categories', 'errors']));
        }

        Product::create($validator->validated());

        return view('products.success', ['message' => 'Товар успешно создан!']);
    }

    public function update(Request $request, Product $product) {
        if (!$product->id) {
            $product = Product::findOrFail($request->get('id'));
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable',
            'price' => 'required|regex:/^\d*(\.\d{2})?$/|min:0.01',
        ]);

        if ($validator->fails()) {
            $categories = Category::all();

            $errors = new ViewErrorBag();
            $errors->put('default', $validator->errors());

            return view('products.edit', [
                'product' => $product,
                'categories' => $categories,
                'errors' => $errors,
                'old' => $request->all(),
            ]);
        }

        $product->update($validator->validated());

        return view('products.success', ['message' => 'Товар успешно обновлён!']);
    }

    public function delete($id) {

        Product::findOrFail($id)->delete();

        return view('products.success', ['message' => 'Товар успешно удалён!']);
    }
}
