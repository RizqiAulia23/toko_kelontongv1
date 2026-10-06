/* eslint-disable @next/next/no-img-element */
import Link from 'next/link';
import { getStorefrontProducts, getCategories } from '@/lib/storefront';
import StorefrontFilters from '@/components/StorefrontFilters';

export const dynamic = 'force-dynamic';

export default async function StorefrontPage({
  searchParams,
}: {
  searchParams: Promise<{ search?: string; category?: string }>;
}) {
  const params = await searchParams;
  const products = await getStorefrontProducts({
    search: params.search,
    category: params.category,
  });
  const categories = await getCategories();

  return (
    <main className="min-h-screen bg-zinc-950">
      <div className="mx-auto max-w-7xl px-6 py-12">
        <section className="mb-8">
          <h1 className="text-3xl font-bold text-white">Katalog Produk</h1>
          <p className="mt-2 text-zinc-400">
            Jelajahi koleksi lengkap produk berkualitas di Toko Kelontong
          </p>
        </section>

        <section className="mb-8">
          <div className="grid grid-cols-3 gap-4">
            <div className="rounded-xl bg-zinc-900 p-6 border border-zinc-800">
              <p className="text-sm text-zinc-400 uppercase tracking-wider">
                Produk Aktif
              </p>
              <p className="mt-2 text-2xl font-bold text-emerald-400">
                {products.length}
              </p>
            </div>
            <div className="rounded-xl bg-zinc-900 p-6 border border-zinc-800">
              <p className="text-sm text-zinc-400 uppercase tracking-wider">
                Kategori
              </p>
              <p className="mt-2 text-2xl font-bold text-emerald-400">
                {categories.length}
              </p>
            </div>
            <div className="rounded-xl bg-zinc-900 p-6 border border-zinc-800">
              <p className="text-sm text-zinc-400 uppercase tracking-wider">
                Ketersediaan
              </p>
              <p className="mt-2 text-2xl font-bold text-emerald-400">
                Tersedia
              </p>
            </div>
          </div>
        </section>

        <section>
          <StorefrontFilters
            categories={categories}
            initialSearch={params.search}
            initialCategory={params.category}
          />

          {products.length === 0 ? (
            <div className="rounded-xl bg-zinc-900 p-12 text-center border border-zinc-800">
              <p className="text-4xl mb-4">📦</p>
              <h2 className="text-xl font-semibold text-white mb-2">
                Produk tidak ditemukan
              </h2>
              <p className="text-zinc-400 mb-6">
                {params.search || params.category
                  ? 'Coba ubah filter pencarian Anda'
                  : 'Tidak ada produk tersedia saat ini'}
              </p>
              {(params.search || params.category) && (
                <Link
                  href="/store"
                  className="inline-block px-4 py-2 text-sm text-emerald-400 hover:text-emerald-300 underline"
                >
                  Lihat semua produk
                </Link>
              )}
            </div>
          ) : (
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {products.map((product) => (
                <Link
                  key={product.product_id}
                  href={`/store/${product.product_id}`}
                  className="group"
                >
                  <article className="rounded-xl bg-zinc-900 border border-zinc-800 overflow-hidden hover:border-emerald-500 transition-colors h-full flex flex-col cursor-pointer">
                    <div className="h-48 bg-zinc-800 flex items-center justify-center overflow-hidden group-hover:bg-zinc-700 transition-colors">
                      {product.product_image ? (
                        <img
                          src={`/storage/products/${product.product_image}`}
                          alt={product.product_name}
                          className="w-full h-full object-cover group-hover:scale-105 transition-transform"
                        />
                      ) : (
                        <span className="text-4xl">📦</span>
                      )}
                    </div>
                    <div className="p-6 flex-1 flex flex-col">
                      <p className="text-sm text-emerald-400 uppercase tracking-wider">
                        {product.category_name}
                      </p>
                      <h3 className="mt-2 text-lg font-semibold text-white group-hover:text-emerald-400 transition-colors line-clamp-2">
                        {product.product_name}
                      </h3>
                      <p className="mt-2 text-sm text-zinc-400 line-clamp-2 flex-1">
                        {product.product_description}
                      </p>
                      <div className="mt-4 flex items-center justify-between">
                        <span className="text-xl font-bold text-emerald-400">
                          Rp {Number(product.product_price).toLocaleString('id-ID')}
                        </span>
                        <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400">
                          Aktif
                        </span>
                      </div>
                    </div>
                  </article>
                </Link>
              ))}
            </div>
          )}
        </section>
      </div>
    </main>
  );
}