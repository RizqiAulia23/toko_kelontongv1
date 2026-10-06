<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login — VLONIX</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="brand">
      <div class="brand-mark">V</div>
      <div>
        <div class="brand-name">VLONIX</div>
        <div class="brand-sub">Retail Admin Cockpit</div>
      </div>
    </div>

    <h1>Sign in</h1>
    <p>Enter your administrator credentials to access the cockpit.</p>

    @if (session('status'))
      <div class="alert alert-success">
        <i class="fa-solid fa-circle-info"></i>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>{{ $errors->first('username') }}</span>
      </div>
    @endif

    <form method="post" action="{{ route('login') }}" novalidate>
      @csrf

      <div class="form-group">
        <label for="username">Username <span>*</span></label>
        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="e.g. admin" required autofocus autocomplete="username">
      </div>

      <div class="form-group">
        <label for="password">Password <span>*</span></label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required autocomplete="current-password">
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%; margin-top:8px;">
        <i class="fa-solid fa-right-to-bracket"></i>
        <span>Authenticate & Access Cockpit</span>
      </button>
    </form>

    <div class="auth-foot">
      <p>Default development credentials: <code style="color:var(--primary); background:rgba(16,185,129,.1); padding:2px 6px; border-radius:4px;">admin</code> / <code style="color:var(--primary); background:rgba(16,185,129,.1); padding:2px 6px; border-radius:4px;">admin123</code></p>
      <p style="margin-top:8px;"><a href="{{ url('/') }}"><i class="fa-solid fa-arrow-left"></i> Return to Storefront</a></p>
    </div>
  </div>
</div>
</body>
</html>
