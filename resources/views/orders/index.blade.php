<h1>Заказы</h1>

<p>
    <button class="btn btn-primary" hx-get="/modal" hx-target="#m"
		hx-vals='{"state":"newOrder"}'>Добавить заказ</button>
    <button class="btn btn-secondary"
            hx-get="/orders/export-word"
            hx-on:htmx:after-request="window.open(event.detail.xhr.responseURL, '_blank')"
            hx-target="#temp-container"
            >Экспорт заказов в word</button>
    <button class="btn btn-secondary"
            hx-get="/orders/export-pdf"
            hx-on:htmx:after-request="window.open(event.detail.xhr.responseURL, '_blank')"
            hx-target="#temp-container"
            >Экспорт заказов в pdf</button>
    <div id="temp-container" style="display: none;"></div>
</p>

<p>
    <table>
        <thead>
            <tr>
                <th>Номер заказа</th>
                <th>Дата создания</th>
                <th>ФИО покупателя</th>
                <th>Статус заказа</th>
                <th>Итоговая цена, ₽</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>{{ $order->fio }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ round($order->product->price * $order->amount, 2) }}</td>
                    <td>
                        <button hx-get="/modal" hx-target="#m"
                            hx-vals='{"state":"viewOrder","id":"{{ $order->id }}"}'>Просмотр</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</p>
