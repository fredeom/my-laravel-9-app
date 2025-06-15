<form hx-put={{ route('products.update', ['id' => $product->id ]) }}
    hx-target="#modal .modal-content .body"
>
  @csrf
  @method('PUT')
  <fieldset>
    <legend>Атрибуты товара</legend>

    <input name="id" value="{{ $old['id'] ?? $product->id }}" type="hidden">

    <p>
      <label for="name">Название</label>
      <input name="name" id="name" value="{{ $old['name'] ?? $product->name }}"
        type="text" placeholder="Название">

      @error('name')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="category">Категория</label>
      <select name="category_id" id="category">
            @foreach ($categories as $category)
                <option value={{ $category->id }} {{ ($old['category_id'] ?? $product->category_id) == $category->id ?
                                                     "selected" : "" }}>{{ $category->name }}</option>
            @endforeach
      </select>
    </p>
    <p>
      <label for="description">Описание</label>
      <textarea name="description" id="description" placeholder="Описание..."
      >{{ $old['description'] ?? $product->description }}</textarea>
      @error('description')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="price">Цена</label>
      <input name="price" id="price" type="text" value="{{ $old['price'] ?? $product->price }}">₽
      @error('price')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <div class="form-group">
        <button class="btn btn-success">Сохранить</button>
        <button class="btn btn-danger" hx-delete="{{ route('products.delete', $product->id )}}"
                hx-target="#modal .modal-content .body" hx-confirm="Вы действительно хотите удалить товар?">Удалить</button>
    </div>
  </fieldset>
</form>

