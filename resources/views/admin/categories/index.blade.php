@extends('layouts.admin')

@section('title', 'Category Management')

@section('header_title', 'Categories')
@section('header_subtitle', 'Organize catalogue classification taxonomy.')

@section('main_content')
<div class="card">
  <div class="card-head">
    <div>
      <h2><i class="fa-solid fa-layer-group" style="color:var(--secondary);"></i> Category Directory</h2>
      <p>{{ $categories->count() }} total taxonomy nodes in catalog.</p>
    </div>
    <a class="btn btn-primary btn-sm" href="{{ route('categories.create') }}">
      <i class="fa-solid fa-plus"></i> Add Category
    </a>
  </div>

  @if($categories->isEmpty())
    <div class="empty">
      <div class="empty-icon"><i class="fa-solid fa-folder-open"></i></div>
      <h3>No categories found.</h3>
      <p>No taxonomy structures detected. Create your first category to classify inventory.</p>
      <a class="btn btn-primary btn-sm" href="{{ route('categories.create') }}"><i class="fa-solid fa-plus"></i> Add Category</a>
    </div>
  @else
    <div class="table-wrap">
      <table class="data">
        <thead>
          <tr>
            <th style="width:80px;">ID</th>
            <th>Category Name</th>
            <th style="width:160px;">Linked Products</th>
            <th style="width:120px; text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($categories as $cat)
            <tr>
              <td><code>#{{ $cat->category_id }}</code></td>
              <td><strong>{{ $cat->category_name }}</strong></td>
              <td>
                <span class="badge badge-count">
                  <i class="fa-solid fa-box-archive" style="font-size:11px; margin-right:4px;"></i>
                  {{ $cat->products_count }} item(s)
                </span>
              </td>
              <td>
                <div class="row-actions" style="justify-content:flex-end;">
                  <a class="icon-btn edit" href="{{ route('categories.edit', $cat) }}" title="Edit category">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <form method="post" action="{{ route('categories.destroy', $cat) }}" data-confirm-form data-confirm-message="Delete category '{{ addslashes($cat->category_name) }}'? This cannot be undone." style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn del" title="Delete category">
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