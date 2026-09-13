<label>Name</label>
<input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>

<label>Description</label>
<textarea name="description" required style="width:100%;padding:0.5rem;margin-top:0.25rem;border:1px solid #d1d5db;border-radius:6px;">{{ old('description', $product->description ?? '') }}</textarea>

<label>Price</label>
<input type="number" step="0.01" min="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required>

<label>Stock quantity</label>
<input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>

<label>Category</label>
<select name="category_id" required style="width:100%;padding:0.5rem;margin-top:0.25rem;">
    <option value="">Select...</option>
    @foreach ($categories as $cat)
        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
    @endforeach
</select>

<label>Supplier</label>
<select name="supplier_id" required style="width:100%;padding:0.5rem;margin-top:0.25rem;">
    <option value="">Select...</option>
    @foreach ($suppliers as $sup)
        <option value="{{ $sup->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $sup->id)>{{ $sup->name }}</option>
    @endforeach
</select>

<label>Image</label>
<input type="file" name="image" accept="image/*">
<label>Tags</label>
@php $selectedTags = isset($product) ? $product->tags->pluck('id')->toArray() : []; @endphp
@foreach ($tags as $tag)
    <label style="font-weight:normal;display:inline-block;margin-right:1rem;">
        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" style="width:auto;display:inline;"
            @checked(in_array($tag->id, old('tags', $selectedTags)))>
        {{ $tag->name }}
    </label>
@endforeach