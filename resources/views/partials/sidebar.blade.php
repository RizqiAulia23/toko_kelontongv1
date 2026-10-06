<aside class="sidebar" id="sidebar">
  <div class="brand">
    <div class="brand-mark">V</div>
    <div>
      <div class="brand-name">VLONIX</div>
      <div class="brand-sub">Retail OS</div>
    </div>
    <button id="closeSidebar" class="menu-btn" aria-label="Close sidebar">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <nav class="nav">
    <div class="nav-label">Management</div>
    <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
      <i class="fa-solid fa-table-columns"></i>
      <span>Dashboard</span>
    </a>
    <a class="nav-link {{ Route::is('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
      <i class="fa-solid fa-box-archive"></i>
      <span>Products</span>
    </a>
    <a class="nav-link {{ Route::is('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
      <i class="fa-solid fa-layer-group"></i>
      <span>Categories</span>
    </a>
  </nav>

  <div class="nav-footer">
    @if(Auth::check())
      <div class="admin-mini">
        <div class="avatar">{{ strtoupper(substr(Auth::user()->admin_name ?? 'A', 0, 1)) }}</div>
        <div style="min-width:0; overflow:hidden;">
          <strong style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ Auth::user()->admin_name }}</strong>
          <small>{{ Auth::user()->level }}</small>
        </div>
      </div>
    @endif
    <form method="POST" action="{{ route('logout') }}" style="display:contents;">
      @csrf
      <button type="submit" class="nav-link" style="background:none; border:none; width:100%; text-align:left; padding:10px 12px; cursor:pointer;">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
      </button>
    </form>
  </div>
</aside>
