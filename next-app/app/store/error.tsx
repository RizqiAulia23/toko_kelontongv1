'use client';

import Link from 'next/link';

export default function StoreError({
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  return (
    <main className="min-h-screen bg-zinc-950 flex items-center justify-center px-6">
      <div className="text-center">
        <p className="text-6xl mb-4">⚠️</p>
        <h1 className="text-3xl font-bold text-white mb-2">
          Terjadi Kesalahan
        </h1>
        <p className="text-zinc-400 mb-8 max-w-md">
          Gagal memuat data katalog. Silakan coba beberapa saat lagi.
        </p>
        <div className="flex justify-center gap-4">
          <button
            onClick={() => reset()}
            className="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-zinc-950 font-semibold rounded-lg transition-colors cursor-pointer"
          >
            Coba Lagi
          </button>
          <Link
            href="/"
            className="px-6 py-3 border border-zinc-700 hover:bg-zinc-900 text-zinc-300 font-semibold rounded-lg transition-colors"
          >
            Beranda
          </Link>
        </div>
      </div>
    </main>
  );
}