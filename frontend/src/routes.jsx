import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import Header from './components/Header/Header';
import ProductListPage from './product-pages/ProductListPage/ProductListPage';
import ProductDetailPage from './product-pages/ProductDetailPage/ProductDetailPage';

const AppRoutes = () => {
    return (
        <Router>
            <Header />
            <Routes>
                <Route path="/" element={<Navigate to="/category/all" replace />} />
                <Route path="/category/:categoryName" element={<ProductListPage />} />
                <Route path="/product/:id" element={<ProductDetailPage />} />
                <Route path="*" element={<h2>404 - Not Found</h2>} />
            </Routes>
        </Router>
    );
};

export default AppRoutes;
