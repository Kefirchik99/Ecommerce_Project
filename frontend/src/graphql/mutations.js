import { gql } from "@apollo/client";

export const CREATE_ORDER = gql`
  mutation CreateOrder($products: [OrderInput!]!) {
    createOrder(products: $products)
  }
`;
