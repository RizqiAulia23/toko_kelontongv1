import Link from 'next/link';

export default function ProductNotFound() {
  return (
    <main className="min-h-screen bg-zinc-950 flex items-center justify-center px-6">
      <div className="text-center">
        <p className="text-6xl mb-4">🔍</p>
        <h1 className="text-3xl font-bold text-white mb-2">
          Produk Tidak Ditemukan
        </h1>
        <p className="text-zinc-400 mb-8 max-w-md">
          Produk yang Anda cari tidak ada atau tidak tersedia untuk ditampilkan.
        </p>
        <Link
          href="/store"
          className="inline-block px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-zinc-950 font-semibold rounded-lg transition-colors"
        >
          Kembali ke Katalog
        </Link>
      </div>
    </main>
  );
}