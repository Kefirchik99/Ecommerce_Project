import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import { ApolloProvider } from '@apollo/client';
import client from './graphql/graphql';
import App from './App';
import { CartProvider } from './context/CartContext';

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <BrowserRouter>
      <ApolloProvider client={client}>
        <CartProvider>
          <App />
        </CartProvider>
      </ApolloProvider>
    </BrowserRouter>
  </React.StrictMode>
);
