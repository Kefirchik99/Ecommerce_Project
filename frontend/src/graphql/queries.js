import { gql } from "@apollo/client";


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


export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      id
      name
    }
  }
`;


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
