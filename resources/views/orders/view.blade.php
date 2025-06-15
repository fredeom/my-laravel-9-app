<fieldset>
    <legend>Атрибуты Заказа</legend>
    ФИО покупателя: {{ $order->fio }}<br>
    Дата создания: {{ $order->created_at }}<br>
    Статус заказа: <span id='currentStatus'>{{ $order->status }}
        @if ($order->status == App\Models\Order::NEW)
            <button hx-patch="{{ route('orders.done', $order->id) }}"
                    hx-target="#currentStatus"
                >Сменить на {{ App\Models\Order::DONE }}</button>
        @endif</span><br>
    Продукт: {{ $order->product->name }}<br>
    Количество: {{ $order->amount }}<br>
    <label for="comment">Комментарий покупателя:</label>
    <textarea id="comment" readonly>{{ $order->comment }}</textarea>
</fieldset>
