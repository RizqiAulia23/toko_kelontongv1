<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'VLONIX Admin') — VLONIX</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="app">
  <div class="overlay" id="overlay"></div>

  @include('partials.sidebar')

  <main class="main">
    <header class="topbar">
      <div style="display:flex; align-items:center; gap:14px;">
        <button class="menu-btn" id="menuBtn" type="button" aria-label="Toggle navigation">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div>
          <h1>@yield('header_title', 'Dashboard')</h1>
          <p>@yield('header_subtitle', 'VLONIX Retail Admin Cockpit')</p>
        </div>
      </div>
      <div class="topbar-actions">
        <div class="badge badge-active" title="System Status Online">
          <span class="dot"></span>
          <span>Online</span>
        </div>
        <a class="btn btn-secondary btn-sm" href="{{ url('/') }}" target="_blank" rel="noopener">
          <i class="fa-solid fa-globe"></i>
          <span>Storefront</span>
        </a>
      </div>
    </header>

    <div class="content">
      @if(Session::has('success'))
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i>
          <span>{{ Session::get('success') }}</span>
        </div>
      @endif
      @if(Session::has('error'))
        <div class="alert alert-error">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>{{ Session::get('error') }}</span>
        </div>
      @endif
      @if(Session::has('status'))
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i>
          <span>{{ Session::get('status') }}</span>
        </div>
      @endif

      @yield('main_content')
    </div>
  </main>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>