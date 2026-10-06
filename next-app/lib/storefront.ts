import { queryMany } from '@/lib/db';
import { Product, Category } from '@/types/storefront';

export interface StorefrontProduct extends Product {
  category_name: string;
}

export async function getStorefrontProducts(filters: { search?: string; category?: string } = {}): Promise<StorefrontProduct[]> {
  let queryText = `
    SELECT 
      p.product_id, 
      p.category_id, 
      p.product_name, 
      p.product_price, 
      p.product_description, 
      p.product_image, 
      p.product_status, 
      p.date_created, 
      c.category_name 
    FROM tb_product p 
    LEFT JOIN tb_category c ON p.category_id = c.category_id 
    WHERE p.product_status = 1
  `;

  const params: (string | number | null)[] = [];
  let paramIndex = 1;

  if (filters.search && filters.search.trim() !== '') {
    queryText += ` AND LOWER(p.product_name) LIKE $${paramIndex++}`;
    params.push(`%${filters.search.trim().toLowerCase()}%`);
  }

  if (filters.category && filters.category !== '' && !isNaN(Number(filters.category)) && Number(filters.category) > 0) {
    queryText += ` AND p.category_id = $${paramIndex++}`;
    params.push(Number(filters.category));
  }

  queryText += ' ORDER BY p.product_id DESC LIMIT 100';

  return await queryMany<StorefrontProduct>(queryText, params);
}

export async function getCategories(): Promise<Category[]> {
  return await queryMany<Category>(
    'SELECT category_id, category_name FROM tb_category ORDER BY category_name ASC'
  );
}

export async function getProductById(productId: number): Promise<StorefrontProduct | null> {
  if (!Number.isInteger(productId) || productId <= 0) {
    return null;
  }

  const query = `
    SELECT 
      p.product_id, 
      p.category_id, 
      p.product_name, 
      p.product_price, 
      p.product_description, 
      p.product_image, 
      p.product_status, 
      p.date_created, 
      c.category_name 
    FROM tb_product p 
    LEFT JOIN tb_category c ON p.category_id = c.category_id 
    WHERE p.product_id = $1 AND p.product_status = 1
    LIMIT 1
  `;

  const result = await queryMany<StorefrontProduct>(query, [productId]);
  return result[0] || null;
}