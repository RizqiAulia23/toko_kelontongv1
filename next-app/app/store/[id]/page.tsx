/* eslint-disable @next/next/no-img-element */
import { notFound } from 'next/navigation';
import Link from 'next/link';
import { getProductById } from '@/lib/storefront';

export const dynamic = 'force-dynamic';

interface ProductDetailPageProps {
  params: Promise<{ id: string }>;
}

export default async function ProductDetailPage({ params }: ProductDetailPageProps) {
  const { id } = await params;
  const productId = Number(id);

  if (!Number.isInteger(productId) || productId <= 0) {
    notFound();
  }

  const product = await getProductById(productId);

  if (!product) {
    notFound();
  }

  const price = Number(product.product_price);
  const formattedPrice = isNaN(price)
    ? product.product_price
    : price.toLocaleString('id-ID');

  const date = new Date(product.date_created);
  const formattedDate = isNaN(date.getTime())
    ? product.date_created
    : date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      });

  return (
    <main className="min-h-screen bg-zinc-950">
      <div className="mx-auto max-w-5xl px-6 py-12">
        <Link
          href="/store"
          className="inline-flex items-center gap-2 text-sm text-zinc-400 hover:text-emerald-400 transition-colors mb-8"
        >
          <svg
            className="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M15 19l-7-7 7-7"
            />
          </svg>
          Kembali ke Katalog
        </Link>

        <div className="grid grid-cols-1 gap-8 lg:grid-cols-2">
          <div className="rounded-xl bg-zinc-900 border border-zinc-800 overflow-hidden">
            <div className="h-96 bg-zinc-800 flex items-center justify-center">
              {product.product_image ? (
                <img
                  src={`/storage/products/${product.product_image}`}
                  alt={product.product_name}
                  className="w-full h-full object-cover"
                />
              ) : (
                <span className="text-8xl">📦</span>
              )}
            </div>
          </div>

          <div className="flex flex-col">
            <span className="text-sm text-emerald-400 uppercase tracking-wider">
              {product.category_name}
            </span>
            <h1 className="mt-2 text-3xl font-bold text-white">
              {product.product_name}
            </h1>
            <p className="mt-4 text-2xl font-bold text-emerald-400">
              Rp {formattedPrice}
            </p>
            <div className="mt-6 flex flex-wrap gap-2">
              <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400">
                Aktif
              </span>
              <span className="rounded-full bg-zinc-800 px-3 py-1 text-xs text-zinc-400">
                Diperbarui: {formattedDate}
              </span>
            </div>
            <div className="mt-6 border-t border-zinc-800 pt-6">
              <h2 className="text-lg font-semibold text-white mb-3">
                Deskripsi
              </h2>
              <p className="text-zinc-300 leading-relaxed whitespace-pre-line">
                {product.product_description || 'Deskripsi tidak tersedia.'}
              </p>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}