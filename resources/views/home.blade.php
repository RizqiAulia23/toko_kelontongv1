<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Toko Kelontong — VLONIX</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    .storefront-wrap {
      background: linear-gradient(135deg, #0B0F17 0%, #0F1419 100%);
      color: #E0E0DC;
      min-height: 100vh;
      padding-bottom: 40px;
    }
    
    .storefront-header {
      background: rgba(15, 20, 25, 0.8);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 16px 20px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .storefront-nav {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }
    
    .storefront-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      font-size: 18px;
      color: #10B981;
      text-decoration: none;
    }
    
    .storefront-brand:hover {
      color: #34D399;
    }
    
    .storefront-nav-links {
      display: flex;
      gap: 16px;
      align-items: center;
    }
    
    .storefront-nav-links a {
      color: #A0A09A;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.2s;
    }
    
    .storefront-nav-links a:hover {
      color: #E0E0DC;
    }
    
    .storefront-nav-links .btn {
      margin: 0;
    }
    
    .storefront-hero {
      background: linear-gradient(180deg, rgba(16, 185, 129, 0.1) 0%, transparent 100%);
      padding: 60px 20px 40px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .storefront-hero h1 {
      font-size: 32px;
      font-weight: 600;
      margin-bottom: 12px;
      color: #FFFFFF;
    }
    
    .storefront-hero p {
      font-size: 16px;
      color: #A0A09A;
      margin-bottom: 24px;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .storefront-stats {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    
    .stat-item {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    
    .stat-number {
      font-size: 24px;
      font-weight: 600;
      color: #10B981;
    }
    
    .stat-label {
      font-size: 12px;
      color: #A0A09A;
      margin-top: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .storefront-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .storefront-filters {
      display: flex;
      gap: 16px;
      margin-bottom: 32px;
      flex-wrap: wrap;
      align-items: center;
    }
    
    .search-box {
      flex: 1;
      min-width: 250px;
      position: relative;
    }
    
    .search-box input {
      width: 100%;
      padding: 10px 16px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      color: #E0E0DC;
      font-size: 14px;
    }
    
    .search-box input::placeholder {
      color: #706F6C;
    }
    
    .search-box input:focus {
      outline: none;
      border-color: #10B981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .category-filter {
      padding: 10px 16px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 6px;
      color: #E0E0DC;
      font-size: 14px;
      cursor: pointer;
    }
    
    .category-filter:focus {
      outline: none;
      border-color: #10B981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .storefront-products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .product-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      overflow: hidden;
      transition: all 0.3s;
    }
    
    .product-card:hover {
      background: rgba(255, 255, 255, 0.08);
      border-color: rgba(16, 185, 129, 0.5);
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    }
    
    .product-image {
      width: 100%;
      height: 200px;
      background: rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: #10B981;
      overflow: hidden;
    }
    
    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .product-info {
      padding: 16px;
    }
    
    .product-category {
      font-size: 12px;
      color: #10B981;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }
    
    .product-name {
      font-size: 16px;
      font-weight: 600;
      color: #FFFFFF;
      margin-bottom: 8px;
      min-height: 40px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-description {
      font-size: 13px;
      color: #A0A09A;
      margin-bottom: 12px;
      min-height: 36px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-price {
      font-size: 20px;
      font-weight: 600;
      color: #10B981;
    }
    
    .empty-state {
      grid-column: 1 / -1;
      text-align: center;
      padding: 60px 20px;
    }
    
    .empty-state i {
      font-size: 48px;
      color: #706F6C;
      margin-bottom: 16px;
    }
    
    .empty-state h3 {
      color: #FFFFFF;
      margin-bottom: 8px;
    }
    
    .empty-state p {
      color: #A0A09A;
    }

    @media (max-width: 768px) {
      .storefront-hero h1 {
        font-size: 24px;
      }
      
      .storefront-stats {
        gap: 20px;
      }
      
      .storefront-filters {
        flex-direction: column;
      }
      
      .search-box {
        min-width: auto;
      }
      
      .storefront-products {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
      }
      
      .product-card {
        border-radius: 6px;
      }
      
      .product-image {
        height: 150px;
      }
      
      .product-info {
        padding: 12px;
      }
      
      .product-name {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="storefront-wrap">
    <header class="storefront-header">
      <nav class="storefront-nav">
        <a href="{{ url('/') }}" class="storefront-brand">
          <i class="fa-solid fa-store"></i>
          <span>Toko Kelontong</span>
        </a>
        <div class="storefront-nav-links">
          <a href="{{ url('/') }}">Home</a>
          <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>Admin</span>
          </a>
        </div>
      </nav>
    </header>

    <section class="storefront-hero">
      <h1>Katalog Produk</h1>
      <p>Jelajahi koleksi lengkap produk berkualitas di Toko Kelontong</p>
      <div class="storefront-stats">
        <div class="stat-item">
          <div class="stat-number">{{ $products->count() }}</div>
          <div class="stat-label">Produk Aktif</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">{{ $categories->count() }}</div>
          <div class="stat-label">Kategori</div>
        </div>
      </div>
    </section>

    <div class="storefront-content">
      <form method="get" class="storefront-filters">
        <div class="search-box">
          <input 
            type="text" 
            name="search" 
            placeholder="Cari produk..." 
            value="{{ $search }}"
          >
        </div>
        <select name="category" class="category-filter">
          <option value="">Semua Kategori</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->category_id }}" @if($categoryId === $cat->category_id) selected @endif>
              {{ $cat->category_name }}
            </option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="fa-solid fa-magnifying-glass"></i>
          <span>Cari</span>
        </button>
      </form>

      @if ($products->count() > 0)
        <div class="storefront-products">
          @foreach ($products as $product)
            <div class="product-card">
              <div class="product-image">
                @if ($product->product_image)
                  <img src="{{ asset('storage/products/' . $product->product_image) }}" alt="{{ $product->product_name }}">
                @else
                  <i class="fa-solid fa-box"></i>
                @endif
              </div>
              <div class="product-info">
                <div class="product-category">{{ $product->category->category_name ?? 'N/A' }}</div>
                <div class="product-name">{{ $product->product_name }}</div>
                <div class="product-description">{{ $product->product_description }}</div>
                <div class="product-price">
                  Rp {{ number_format($product->product_price, 0, ',', '.') }}
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="storefront-products">
          <div class="empty-state">
            <i class="fa-solid fa-inbox"></i>
            <h3>Produk tidak ditemukan</h3>
            <p>Coba ubah filter pencarian Anda</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</body>
</html>