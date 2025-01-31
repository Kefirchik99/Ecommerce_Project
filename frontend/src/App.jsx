import React from 'react';
import { BrowserRouter } from 'react-router-dom';
import { CartProvider } from './context/CartContext';
import { HeaderProvider } from './context/HeaderContext';
import Header from './components/Header';
import AppRoutes from './routes';
import './styles/main.scss';

const App = () => {
  return (
    <HeaderProvider>
      <CartProvider>
        <BrowserRouter>
          <div className="container">
            <Header />
            <main>
              <AppRoutes />
            </main>
          </div>
        </BrowserRouter>
      </CartProvider>
    </HeaderProvider>
  );
};

export default App;
