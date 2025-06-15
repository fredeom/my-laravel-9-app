<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/modal', function (Request $request) {
    $state = $request->get('state');
    $categories = Category::all();
    switch ($state) {
        case 'newProduct': {
            $content = View::make('products.new', compact('request', 'categories'))->render();
            return view('modal', ['title' => 'Новый товар', 'content' => $content]);
        }
        case 'viewProduct' : {
            $id = $request->get('id');
            $product = Product::findOrFail($id);
            $content = View::make('products.view', compact('request', 'categories', 'product'))->render();
            return view('modal', ['title' => 'Просмотр товара', 'content' => $content]);
        }
        case 'editProduct' : {
            $id = $request->get('id');
            $product = Product::findOrFail($id);
            $content = View::make('products.edit', compact('request', 'categories', 'product'))->render();
            return view('modal', ['title' => 'Редактирование товара', 'content' => $content]);
        }
        case 'newOrder' : {
            $products = Product::all();
            $content = View::make('orders.new', compact('request', 'products'))->render();
            return view('modal', ['title' => 'Новый заказ', 'content' => $content]);
        }
        case 'viewOrder' : {
            $id = $request->get('id');
            $order = Order::findOrFail($id);
            $content = View::make('orders.view', compact('request', 'order'))->render();
            return view('modal', ['title' => 'Просмотр товара', 'content' => $content]);
        }
        default:
            return response('Invalid modal state.', 400);
    }
});


Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products/new', [ProductController::class, 'store'])->name('products.new');

Route::put('/products/{id}/update', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{id}/delete', [ProductController::class, 'delete'])->name('products.delete');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/new', [OrderController::class, 'store'])->name('orders.new');
Route::patch('/orders/{id}/done', [OrderController::class, 'done'])->name('orders.done');
