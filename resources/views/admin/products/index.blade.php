@extends('layouts.admin')

@section('title', 'Product Management')

@section('header_title', 'Products')
@section('header_subtitle', 'Catalog, inventory status, and item properties.')

@section('main_content')
<div class="card">
  <div class="card-head">
    <div>
      <h2><i class="fa-solid fa-box-archive" style="color:var(--primary);"></i> Catalog Inventory</h2>
      <p>{{ $products->count() }} items match current query parameters.</p>
    </div>
    <div class="toolbar">
      <form method="get" action="{{ route('products.index') }}" style="display:flex; gap:8px; flex-wrap:wrap;">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="search" name="search" class="form-control" placeholder="Search product name..." value="{{ $search }}">
        </div>

        <select name="category" class="form-control" style="width:auto;" onchange="this.form.submit()">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->category_id }}" {{ (string)$categoryId === (string)$cat->category_id ? 'selected' : '' }}>
              {{ $cat->category_name }}
            </option>
          @endforeach
        </select>

        <select name="status" class="form-control" style="width:auto;" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="1" {{ (string)$status === '1' ? 'selected' : '' }}>Active</option>
          <option value="0" {{ (string)$status === '0' ? 'selected' : '' }}>Inactive</option>
        </select>

        @if($search || $categoryId || $status !== '')
          <a class="btn btn-ghost btn-sm" href="{{ route('products.index') }}" title="Clear filters">
            <i class="fa-solid fa-xmark"></i> Reset
          </a>
        @endif
      </form>

      <a class="btn btn-primary btn-sm" href="{{ route('products.create') }}">
        <i class="fa-solid fa-plus"></i> Add Product
      </a>
    </div>
  </div>

  @if($products->isEmpty())
    <div class="empty">
      <div class="empty-icon"><i class="fa-solid fa-box-open"></i></div>
      <h3>No products found.</h3>
      <p>
        @if($search || $categoryId || $status !== '')
          No items match your filter criteria. Try resetting search filters.
        @else
          No items registered in database yet.
        @endif
      </p>
      @if($search || $categoryId || $status !== '')
        <a class="btn btn-secondary btn-sm" href="{{ route('products.index') }}">Reset Filters</a>
      @else
        <a class="btn btn-primary btn-sm" href="{{ route('products.create') }}"><i class="fa-solid fa-plus"></i> Add Product</a>
      @endif
    </div>
  @else
    <div class="table-wrap">
      <table class="data">
        <thead>
          <tr>
            <th style="width:70px;">Image</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Status</th>
            <th style="text-align:right; width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $p)
            <tr>
              <td>
                @if(!empty($p->product_image))
                  <img src="{{ asset('storage/products/' . $p->product_image) }}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid var(--border);background:var(--s2);">
                @else
                  <div style="width:48px;height:48px;border-radius:6px;background:var(--s2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);"><i class="fa-solid fa-image"></i></div>
                @endif
              </td>
              <td>
                <div style="max-width:320px;">
                  <strong>{{ $p->product_name }}</strong>
                  <small style="color:var(--muted);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->product_description }}</small>
                </div>
              </td>
              <td>
                <span class="badge badge-cyan">{{ $p->category?->category_name ?? 'Uncategorized' }}</span>
              </td>
              <td>
                <span class="price">Rp {{ number_format($p->product_price, 0, ',', '.') }}</span>
              </td>
              <td>
                <form method="post" action="{{ route('products.toggle-status', $p) }}" style="display:inline;">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="badge {{ $p->product_status == 1 ? 'badge-active' : 'badge-inactive' }}" style="border:none; cursor:pointer;" title="Click to toggle status">
                    <span class="dot"></span>
                    <span>{{ $p->product_status == 1 ? 'Active' : 'Inactive' }}</span>
                  </button>
                </form>
              </td>
              <td>
                <div class="row-actions" style="justify-content:flex-end;">
                  <a class="icon-btn edit" href="{{ route('products.edit', $p) }}" title="Edit product">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <form method="post" action="{{ route('products.destroy', $p) }}" data-confirm-form data-confirm-message="Delete product '{{ addslashes($p->product_name) }}'? This will permanently delete the item." style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn del" title="Delete product">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection