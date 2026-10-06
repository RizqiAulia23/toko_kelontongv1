import { Pool, QueryResult, QueryResultRow } from 'pg';

// Server-side only: do not import on client
if (typeof window !== 'undefined') {
  throw new Error('db.ts must only be used on the server');
}

// Connection pool configuration - handle missing DATABASE_URL gracefully for build time
let pool: Pool | null = null;

if (process.env.DATABASE_URL) {
  pool = new Pool({
    connectionString: process.env.DATABASE_URL,
    max: 10, // Connection pool size
    idleTimeoutMillis: 30000,
    connectionTimeoutMillis: 2000,
  });

  pool.on('error', (err: Error) => {
    console.error('Unexpected error on idle client', err);
  });
} else {
  // Mock pool for build-time: throws if queried without real DATABASE_URL
  pool = {
    query: () => {
      throw new Error('DATABASE_URL not configured. Set DATABASE_URL environment variable for database queries.');
    },
    on: () => {}, // Mock event listener
  } as unknown as Pool;
}

/**
 * Execute a read-only SELECT query
 */
export async function query<T extends QueryResultRow = QueryResultRow>(
  queryText: string,
  values?: (string | number | null)[]
): Promise<QueryResult<T>> {
  // Safety check: only allow SELECT queries
  const trimmedQuery = queryText.trim().toUpperCase();
  if (!trimmedQuery.startsWith('SELECT')) {
    throw new Error('Only SELECT queries are allowed in read-only mode');
  }

  // Runtime check for pool (should always be set when function is called in runtime)
  if (!pool) {
    throw new Error('Database pool not initialized. Check DATABASE_URL environment variable.');
  }

  try {
    const result = await pool.query<T>(queryText, values);
    return result;
  } catch (error) {
    console.error('Database query error:', error);
    throw error;
  }
}

/**
 * Get a single row from a query
 */
export async function queryOne<T extends QueryResultRow = QueryResultRow>(
  queryText: string,
  values?: (string | number | null)[]
): Promise<T | null> {
  const result = await query<T>(queryText, values);
  return result.rows[0] || null;
}

/**
 * Get all rows from a query
 */
export async function queryMany<T extends QueryResultRow = QueryResultRow>(
  queryText: string,
  values?: (string | number | null)[]
): Promise<T[]> {
  const result = await query<T>(queryText, values);
  return result.rows;
}

export default pool;