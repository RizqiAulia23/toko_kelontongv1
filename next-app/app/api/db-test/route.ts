import { NextResponse } from 'next/server';
import { queryMany } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET() {
  try {
    // Safe read-only query: SELECT from tb_category LIMIT 5
    const categories = await queryMany(
      'SELECT category_id, category_name FROM tb_category LIMIT 5'
    );

    return NextResponse.json(
      {
        status: 'ok',
        message: 'Read-only database connection verified',
        data: categories,
      },
      { status: 200 }
    );
  } catch (error) {
    return NextResponse.json(
      {
        status: 'error',
        message: 'Database read test failed',
        error: (error as Error).message,
      },
      { status: 500 }
    );
  }
}