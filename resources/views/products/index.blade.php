<h1>Товары</h1>

<p>
    <button class="btn btn-primary" hx-get="/modal" hx-target="#m"
		hx-vals='{"state":"newProduct"}'>Добавить товар</button>
</p>

<p>
    <table>
        <thead>
            <tr>
                <th>Название</th>
                <th>Категория</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ round($product->price, 2) }}₽</td>
                    <td>
                        <button class="btn btn-warning" hx-get="/modal" hx-target="#m"
                            hx-vals='{"state":"editProduct","id":"{{ $product->id }}"}'>Редактирование</button>
                        <button hx-get="/modal" hx-target="#m"
                            hx-vals='{"state":"viewProduct","id":"{{ $product->id }}"}'>Просмотр</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</p>
