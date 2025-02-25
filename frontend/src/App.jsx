import React from 'react';
import { CartProvider } from './context/CartContext';
import { HeaderProvider } from './context/HeaderContext';
import Header from './components/Header';
import AppRoutes from './routes';
import './styles/main.scss';

const App = () => {
  return (
    <HeaderProvider>
      <CartProvider>
        <div className="container">
          <Header />
          <main>
            <AppRoutes />
          </main>
        </div>
      </CartProvider>
    </HeaderProvider>
  );
};

export default App;
