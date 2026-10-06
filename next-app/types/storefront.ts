export interface Category {
  category_id: number;
  category_name: string;
}

export interface Product {
  product_id: number;
  category_id: number;
  product_name: string;
  product_price: string | number;
  product_description: string;
  product_image: string;
  product_status: number;
  date_created: string;
}

export interface StorefrontFilters {
  search?: string;
  category?: string;
}