@extends('layouts.admin')

@section('title', 'Add Category')

@section('header_title', 'New Category')
@section('header_subtitle', 'Register a new classification node in catalogue.')

@section('main_content')
<div class="card" style="max-width:640px; margin:0 auto;">
  <div class="card-head">
    <div>
      <h2><i class="fa-solid fa-folder-plus" style="color:var(--primary);"></i> Add Category</h2>
      <p>Fill in the classification details.</p>
    </div>
    <a class="btn btn-secondary btn-sm" href="{{ route('categories.index') }}">
      <i class="fa-solid fa-arrow-left"></i> Back to Categories
    </a>
  </div>

  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="post" action="{{ route('categories.store') }}">
      @csrf

      <div class="form-group">
        <label for="category_name">Category Name <span>*</span></label>
        <input type="text" class="form-control" id="category_name" name="category_name" value="{{ old('category_name') }}" placeholder="e.g. Minuman & Kopi" required autofocus maxlength="100">
        <div class="form-hint">Must be unique across the entire product catalog.</div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
        <a class="btn btn-secondary" href="{{ route('categories.index') }}">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> Save Category
        </button>
      </div>
    </form>
  </div>
</div>
@endsection