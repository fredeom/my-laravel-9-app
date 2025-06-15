<form hx-post="/orders/new" hx-target="#modal .modal-content .body" >
  @csrf
  <fieldset>
    <legend>Атрибуты заказа</legend>
    <p>
      <label for="fio">ФИО</label>
      <input name="fio" id="fio" value="{{ $request->has('fio') ? $request->input('fio') : '' }}"
        type="text" placeholder="ФИО заказчика...">

      @error('fio')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="product">Продукт</label>
      <select name="product_id" id="product">
            @foreach ($products as $product)
                <option value={{ $product->id }}>{{ $product->name }}</option>
            @endforeach
      </select>
      @error('product_id')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="amount">Количество</label>
      <input name="amount" id="amount" type="number" value="{{ $request->has('amount') ? $request->input('amount') : '' }}">
      @error('amount')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <p>
      <label for="comment">Комментарий</label>
      <textarea name="comment" id="comment" placeholder="Комментарий заказчика..."
      >{{ $request->has('comment') ? $request->input('comment') : '' }}</textarea>
      @error('comment')
        <div class="text-danger">{{ $message }}</div>
      @enderror
    </p>
    <button class="btn btn-success">Сохранить</button>
  </fieldset>
</form>
