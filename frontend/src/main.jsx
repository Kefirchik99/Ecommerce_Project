import React from 'react';
import ReactDOM from 'react-dom/client';
import { ApolloProvider } from '@apollo/client';
import client from './graphql/graphql';
import App from './App';
import { CartProvider } from './context/CartContext';

ReactDOM.createRoot(document.getElementById('root')).render(
  <ApolloProvider client={client}>
    <CartProvider>
      <App />
    </CartProvider>
  </ApolloProvider>
);
