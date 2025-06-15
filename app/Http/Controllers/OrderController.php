<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Validator;

class OrderController extends Controller
{
    public function index() {
        $orders = Order::orderBy("created_at","desc")->get();
        return view("orders.index", compact("orders"));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'fio' => 'required|min:3',
            'product_id' => 'required|exists:products,id',
            'comment' => 'nullable',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $products = Product::all();

            $errors = new ViewErrorBag();
            $errors->put('default', $validator->errors());

            return view('/orders/new', compact(['request', 'products', 'errors']));
        }

        $validated = $validator->validated();
        $validated['status'] = Order::NEW;

        Order::create($validated);

        return view('orders.success', ['message' => 'Заказ успешно создан!']);
    }

    public function done($id) {
        $order = Order::findOrFail($id);
        $order->status = Order::DONE;
        $order->save();
        return view('orders.success', ['message' => 'Выполнен. Статус успешно изменён!']);
    }
}
