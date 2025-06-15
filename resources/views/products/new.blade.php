<form hx-post="/products/new" hx-target="#modal .modal-content .body" >
  @csrf
  <fieldset>
    <legend>Атрибуты товара</legend>
    <p>
      <label for="name">Название</label>
      <input name="name" id="name" value="{{ $request->has('name') ? $request->input('name') : '' }}"
        type="text" placeholder="Название">

      @error('name')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="category">Категория</label>
      <select name="category_id" id="category">
            @foreach ($categories as $category)
                <option value={{ $category->id }}>{{ $category->name }}</option>
            @endforeach
      </select>
    </p>
    <p>
      <label for="description">Описание</label>
      <textarea name="description" id="description" placeholder="Описание..."
      >{{ $request->has('description') ? $request->input('description') : '' }}</textarea>
      @error('description')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="price">Цена</label>
      <input name="price" id="price" type="text" value="{{ $request->has('price') ? $request->input('price') : '' }}">₽
      @error('price')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <button class="btn btn-success">Сохранить</button>
  </fieldset>
</form>
