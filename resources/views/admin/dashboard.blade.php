@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header_title', 'Dashboard')
@section('header_subtitle', 'Ringkasan data produk dan kategori toko.')

@section('main_content')
<div class="kpis">
    <div class="kpi green">
        <div class="kpi-icon"><i class="fa-solid fa-box-archive"></i></div>
        <div class="kpi-label">Total Produk</div>
        <div class="kpi-value">{{ $totalProducts }}</div>
        <div class="kpi-sub">Semua produk terdaftar</div>
    </div>
    <div class="kpi cyan">
        <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-label">Produk Aktif</div>
        <div class="kpi-value">{{ $activeProducts }}</div>
        <div class="kpi-sub">Tampil di katalog</div>
    </div>
    <div class="kpi teal">
        <div class="kpi-icon"><i class="fa-solid fa-layer-group"></i></div>
        <div class="kpi-label">Total Kategori</div>
        <div class="kpi-value">{{ $totalCategories }}</div>
        <div class="kpi-sub">Kategori produk</div>
    </div>
    <div class="kpi amber">
        <div class="kpi-icon"><i class="fa-solid fa-eye-slash"></i></div>
        <div class="kpi-label">Produk Nonaktif</div>
        <div class="kpi-value">{{ $totalProducts - $activeProducts }}</div>
        <div class="kpi-sub">Disembunyikan</div>
    </div>
</div>

<div class="grid-2">
    <!-- Produk Terbaru -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-box-archive" style="color:var(--primary); margin-right:6px;"></i> Produk Terbaru</h2>
                <p>5 produk yang baru ditambahkan</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                <span>Lihat Semua</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrap">
            @if($recentProducts->count() > 0)
                <table class="data">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProducts as $p)
                            <tr>
                                <td>
                                    <div class="cell-main">
                                        @if($p->product_image)
                                            <img src="{{ asset('storage/products/' . $p->product_image) }}" alt="{{ $p->product_name }}">
                                        @else
                                            <div style="width:48px;height:48px;border-radius:6px;background:var(--s2);display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0;">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $p->product_name }}</strong>
                                            <small>{{ $p->category->category_name ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="price">Rp {{ number_format($p->product_price, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($p->product_status == 1)
                                        <span class="badge badge-active"><span class="dot"></span>Aktif</span>
                                    @else
                                        <span class="badge badge-inactive"><span class="dot"></span>Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">
                    <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                    <h3>Belum ada produk</h3>
                    <p>Mulai tambahkan produk ke toko Anda.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Produk
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Ringkasan Kategori -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-layer-group" style="color:var(--secondary); margin-right:6px;"></i> Kategori Produk</h2>
                <p>Daftar kategori dan jumlah produk</p>
            </div>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">
                <span>Kelola Kategori</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrap">
            @if($categories->count() > 0)
                <table class="data">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th style="text-align:right;">Jumlah Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                            <tr>
                                <td>
                                    <strong>{{ $cat->category_name }}</strong>
                                </td>
                                <td style="text-align:right;">
                                    <span class="badge badge-count">{{ $cat->products_count }} produk</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">
                    <div class="empty-icon"><i class="fa-solid fa-inbox"></i></div>
                    <h3>Belum ada kategori</h3>
                    <p>Buat kategori untuk mengelompokkan produk.</p>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-plus"></i> Tambah Kategori
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

