export default function Home() {
  return (
    <main className="min-h-screen bg-zinc-950 text-zinc-100">
      <div className="mx-auto max-w-3xl px-6 py-16">
        <div className="mb-12">
          <p className="mb-3 text-sm font-semibold uppercase tracking-widest text-emerald-400">
            Phase 1 - Foundation
          </p>
          <h1 className="text-4xl font-bold tracking-tight sm:text-5xl">
            Toko Kelontong - Next.js Migration
          </h1>
        </div>

        <section className="mb-8 rounded-xl border border-zinc-800 bg-zinc-900 p-6">
          <h2 className="mb-4 text-xl font-semibold text-emerald-400">
            Next.js Foundation Status
          </h2>
          <ul className="space-y-3 text-zinc-300">
            <li className="flex items-start gap-3">
              <span className="mt-1 text-emerald-400">OK</span>
              Next.js foundation is running
            </li>
            <li className="flex items-start gap-3">
              <span className="mt-1 text-emerald-400">OK</span>
              Laravel remains the existing baseline
            </li>
            <li className="flex items-start gap-3">
              <span className="mt-1 text-emerald-400">OK</span>
              Database migration has NOT started
            </li>
          </ul>
        </section>

        <section className="rounded-xl border border-zinc-800 bg-zinc-900 p-6">
          <h2 className="mb-4 text-xl font-semibold text-zinc-100">
            Current Phase
          </h2>
          <p className="text-zinc-400">
            This page is a temporary placeholder for the Next.js foundation.
            No CRUD, authentication, dashboard logic, products, categories,
            or database access has been migrated yet.
          </p>
        </section>
      </div>
    </main>
  );
}