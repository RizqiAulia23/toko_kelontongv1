@extends('layouts.admin')

@section('title', 'Add Product')

@section('header_title', 'New Product')
@section('header_subtitle', 'Register an inventory item into the retail catalog.')

@section('main_content')
<div class="card" style="max-width:760px; margin:0 auto;">
  <div class="card-head">
    <div>
      <h2><i class="fa-solid fa-plus-circle" style="color:var(--primary);"></i> Add Product</h2>
      <p>Configure product attributes, pricing, and visual assets.</p>
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

    <form method="post" action="{{ route('products.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label for="product_name">Product Name <span>*</span></label>
        <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name') }}" placeholder="e.g. Beras Pandan Wangi 5kg" required autofocus maxlength="150">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="category_id">Category <span>*</span></label>
          <select class="form-control" id="category_id" name="category_id" required>
            <option value="">-- Choose Category --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->category_id }}" {{ (old('category_id') ?? '') === (string)$cat->category_id ? 'selected' : '' }}>
                {{ $cat->category_name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="product_price">Price (IDR) <span>*</span></label>
          <input type="number" step="100" min="0" class="form-control" id="product_price" name="product_price" value="{{ old('product_price', '') }}" placeholder="e.g. 75000" required>
        </div>
      </div>

      <div class="form-group">
        <label for="product_description">Description <span>*</span></label>
        <textarea class="form-control" id="product_description" name="product_description" placeholder="Specify ingredients, net weight, expiry conditions, or manufacturer details..." required>{{ old('product_description') }}</textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="product_image">Product Image</label>
          <input type="file" class="form-control" id="product_image" name="product_image" accept="image/jpeg,image/png,image/webp,image/gif">
          <div class="form-hint">JPG, PNG, WEBP up to 5MB.</div>
          <div class="img-preview" id="imagePreview"></div>
        </div>

        <div class="form-group">
          <label for="product_status">Publication Status</label>
          <select class="form-control" id="product_status" name="product_status">
            <option value="1" {{ (old('product_status') == '1' || !old('product_status')) ? 'selected' : '' }}>Active (Visible in Storefront)</option>
            <option value="0" {{ old('product_status') == '0' ? 'selected' : '' }}>Inactive (Hidden / Draft)</option>
          </select>
          <div class="form-hint">Toggle visibility to end customers instantly.</div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
        <a class="btn btn-secondary" href="{{ route('products.index') }}">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> Save Product
        </button>
      </div>
    </form>
  </div>
</div>
@endsection