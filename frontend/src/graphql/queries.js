import { gql } from "@apollo/client";

// Query to get products with a category filter
export const GET_PRODUCTS_WITH_FILTER = gql`
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      inStock
      gallery
      price
    }
  }
`;

// Query to get all categories
export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      id
      name
    }
  }
`;

// Query to get all products
export const GET_PRODUCTS = gql`
  query GetProducts {
    products {
      id
      name
      attributes {
        name
        type
        items {
          id
          displayValue
          value
        }
      }
    }
  }
`;

// Query to get product details by ID
export const GET_PRODUCT_DETAILS = gql`
  query GetProductDetails($id: ID!) {
    product(id: $id) {
      id
      name
      description
      gallery
      price
      inStock
      category
      attributes {
        id
        name
        type
        items {
          id
          displayValue
          value
        }
      }
    }
  }
`;


export const GET_ORDERS = gql`
  query GetOrders {
    orders {
      id
      items {
        productId
        quantity
        price
      }
      total
    }
  }
`;
