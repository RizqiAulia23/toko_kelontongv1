'use client';

import { useRouter } from 'next/navigation';
import { useState } from 'react';

export interface StorefrontFiltersProps {
  categories: Array<{ category_id: number; category_name: string }>;
  initialSearch?: string;
  initialCategory?: string;
}

export default function StorefrontFilters({
  categories,
  initialSearch = '',
  initialCategory = '',
}: StorefrontFiltersProps) {
  const router = useRouter();
  const [search, setSearch] = useState(initialSearch);
  const [category, setCategory] = useState(initialCategory);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    const params = new URLSearchParams();
    if (search.trim()) params.set('search', search.trim());
    if (category) params.set('category', category);
    router.push(`/store?${params.toString()}`);
  };

  const handleReset = () => {
    setSearch('');
    setCategory('');
    router.push('/store');
  };

  return (
    <form onSubmit={handleSearch} className="mb-8 flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
      <div className="flex-1 min-w-[250px]">
        <label htmlFor="search" className="block text-sm font-medium mb-2 text-zinc-300">
          Cari Produk
        </label>
        <input
          type="text"
          id="search"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Cari berdasarkan nama produk..."
          className="w-full px-4 py-2 border border-zinc-700 rounded-lg bg-zinc-900 text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-emerald-400"
        />
      </div>

      <div className="w-full sm:w-auto min-w-[180px]">
        <label htmlFor="category" className="block text-sm font-medium mb-2 text-zinc-300">
          Kategori
        </label>
        <select
          id="category"
          value={category}
          onChange={(e) => setCategory(e.target.value)}
          className="w-full px-4 py-2 border border-zinc-700 rounded-lg bg-zinc-900 text-zinc-100 focus:outline-none focus:border-emerald-400"
        >
          <option value="">Semua Kategori</option>
          {categories.map((cat) => (
            <option key={cat.category_id} value={cat.category_id}>
              {cat.category_name}
            </option>
          ))}
        </select>
      </div>

      <div className="flex gap-2">
        <button
          type="submit"
          className="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-zinc-950 font-semibold rounded-lg transition-colors cursor-pointer"
        >
          Cari
        </button>
        {(search || category) && (
          <button
            type="button"
            onClick={handleReset}
            className="px-4 py-2 border border-zinc-700 hover:bg-zinc-800 text-zinc-300 font-medium rounded-lg transition-colors cursor-pointer"
          >
            Reset
          </button>
        )}
      </div>
    </form>
  );
}