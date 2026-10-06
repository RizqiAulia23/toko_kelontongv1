@extends('layouts.admin')

@section('title', 'Edit Category')

@section('header_title', 'Edit Category')
@section('header_subtitle', 'Update classification details.')

@section('main_content')
<div class="card" style="max-width:640px; margin:0 auto;">
  <div class="card-head">
    <div>
      <h2><i class="fa-solid fa-pen-to-square" style="color:var(--secondary);"></i> Edit Category #{{ $category->category_id }}</h2>
      <p>Modify details for {{ $category->category_name }}</p>
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

    <form method="post" action="{{ route('categories.update', $category) }}">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="category_name">Category Name <span>*</span></label>
        <input type="text" class="form-control" id="category_name" name="category_name" value="{{ old('category_name', $category->category_name) }}" required autofocus maxlength="100">
        <div class="form-hint">Must be unique across the entire product catalog.</div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px;">
        <a class="btn btn-secondary" href="{{ route('categories.index') }}">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-check"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
@endsection