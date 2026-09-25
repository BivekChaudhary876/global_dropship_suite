<label>Name</label>
<input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>

<label>Description</label>
<textarea name="description" required>{{ old('description', $product->description ?? '') }}</textarea>

<div class="form-grid-2">
    <div>
        <label>Price ($)</label>
        <input type="number" step="0.01" min="0.01" name="price" value="{{ old('price', $product->price ?? '') }}" required>
    </div>
    <div>
        <label>Stock quantity</label>
        <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>
    </div>
</div>

<div class="form-grid-2">
    <div>
        <label>Category</label>
        <select name="category_id" required>
            <option value="">Select...</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label>Supplier</label>
        <select name="supplier_id" required>
            <option value="">Select...</option>
            @foreach ($suppliers as $sup)
                <option value="{{ $sup->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $sup->id)>{{ $sup->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<label>Image</label>
<input type="file" name="image" accept="image/*">
@if(isset($product) && $product->image_path)
    <img src="{{ asset('storage/'.$product->image_path) }}" class="image-preview">
@endif

<label>Tags</label>
@php $selectedTags = isset($product) ? $product->tags->pluck('id')->toArray() : []; @endphp
<div class="checkbox-group">
    @foreach ($tags as $tag)
        <label class="checkbox-label">
            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                @checked(in_array($tag->id, old('tags', $selectedTags)))>
            {{ $tag->name }}
        </label>
    @endforeach
</div>