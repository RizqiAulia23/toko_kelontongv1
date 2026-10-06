export default function StoreLoading() {
  return (
    <main className="min-h-screen bg-zinc-950">
      <div className="mx-auto max-w-7xl px-6 py-12">
        <div className="animate-pulse space-y-8">
          <div className="h-8 w-48 bg-zinc-800 rounded"></div>
          <div className="grid grid-cols-3 gap-4">
            {[1, 2, 3].map((item) => (
              <div key={item} className="h-24 bg-zinc-800 rounded-xl"></div>
            ))}
          </div>
          <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {[1, 2, 3, 4, 5, 6].map((item) => (
              <div key={item} className="h-96 bg-zinc-800 rounded-xl"></div>
            ))}
          </div>
        </div>
      </div>
    </main>
  );
}