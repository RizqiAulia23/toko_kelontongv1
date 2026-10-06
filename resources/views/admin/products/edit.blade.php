@extends('layouts.admin')

@section('title', 'Edit Product')

@section('header_title', 'Edit Product')
@section('header_subtitle', 'Update catalog parameters, price or imagery.')

@section('main_content')
<div class="card" style="max-width:760px; margin:0 auto;">
  <div class="card-head">
    <div>
      <h2><i class="fa-solid fa-pen-to-square" style="color:var(--secondary);"></i> Edit Product #{{ $product->product_id }}</h2>
      <p>Modify details for {{ $product->product_name }}</p>
    </div>
    <a class="btn btn-secondary btn-sm" href="{{ route('products.index') }}">
      <i class="fa-solid fa-arrow-left"></i> Back to Products
    </a>
  </div>

  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="post" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="product_name">Product Name <span>*</span></label>
        <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required autofocus maxlength="150">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="category_id">Category <span>*</span></label>
          <select class="form-control" id="category_id" name="category_id" required>
            <option value="">-- Choose Category --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->category_id }}" {{ (old('category_id', $product->category_id) ?? '') === (string)$cat->category_id ? 'selected' : '' }}>
                {{ $cat->category_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="product_price">Price (IDR) <span>*</span></label>
          <input type="number" step="100" min="0" class="form-control" id="product_price" name="product_price" value="{{ old('product_price', $product->product_price) }}" placeholder="e.g. 75000" required>
        </div>
      </div>

      <div class="form-group">
        <label for="product_description">Description <span>*</span></label>
        <textarea class="form-control" id="product_description" name="product_description" placeholder="Specify ingredients, net weight, expiry conditions, or manufacturer details..." required>{{ old('product_description', $product->product_description) }}</textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="product_image">Product Image</label>
          <input type="file" class="form-control" id="product_image" name="product_image" accept="image/jpeg,image/png,image/webp,image/gif">
          <div class="form-hint">Leave blank to keep current image. Formats: JPG, PNG, WEBP (max 5MB).</div>
          <div class="img-preview" id="imagePreview">
            @if(!empty($product->product_image))
              <img src="{{ asset('storage/products/' . $product->product_image) }}" alt="Current Image">
            @endif
          </div>
        </div>

        <div class="form-group">
          <label for="product_status">Publication Status</label>
          <select class="form-control" id="product_status" name="product_status">
            <option value="1" {{ (old('product_status', $product->product_status) == '1') ? 'selected' : '' }}>Active (Visible in Storefront)</option>
            <option value="0" {{ (old('product_status', $product->product_status) == '0') ? 'selected' : '' }}>Inactive (Hidden / Draft)</option>
          </select>
          <div class="form-hint">Current status: {{ $product->product_status == 1 ? 'Live on Store' : 'Draft / Off-shelf' }}</div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
        <a class="btn btn-secondary" href="{{ route('products.index') }}">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
@endsection