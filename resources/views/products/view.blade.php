<fieldset>
    <legend>Атрибуты товара</legend>
    Название: {{ $product->name }}<br>
    Категория: {{ $product->category->name }}
    <label for="description">Описание:</label>
    <textarea id="description" readonly>{{ $product->description }}</textarea>
    Цена: {{ $product->price }}₽
</fieldset>
