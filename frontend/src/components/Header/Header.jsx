import React, { useState, useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import { useQuery } from "@apollo/client";
import { GET_CATEGORIES } from "../../graphql/queries";
import "./Header.scss";
import CartOverlay from "../CartOverlay";
import { useContext } from "react";
import { CartContext } from "../../context/CartContext";
import { useHeader } from "../../context/HeaderContext";
import logo from "../../assets/a-logo.png";

const Header = () => {
    const { totalItems } = useContext(CartContext);
    const [isCartOpen, setIsCartOpen] = useState(false);
    const location = useLocation();
    const { category } = useHeader();

    const { loading, error, data } = useQuery(GET_CATEGORIES);

    const categories = data?.categories || [];

    if (loading) return <p>Loading categories...</p>;
    if (error) return <p>Error loading categories</p>;

    return (
        <header className="header">
            <nav className="header__nav">
                <ul className="header__categories">
                    <li className="header__menu">
                        <Link
                            to="/all"
                            className={`header__category ${location.pathname === "/all" ? "header__category--active" : ""}`}
                            data-testid="category-link"
                        >
                            ALL
                        </Link>
                    </li>
                    {categories.map((categoryItem) => {
                        const toPath = `/category/${categoryItem.name.toLowerCase()}`;
                        const isActive =
                            location.pathname === toPath ||
                            (location.pathname.startsWith("/product/") &&
                                category === categoryItem.name.toLowerCase());

                        return (
                            <li className="header__menu" key={categoryItem.id}>
                                <Link
                                    to={toPath}
                                    className={`header__category ${isActive ? "header__category--active" : ""}`}
                                    data-testid={isActive ? "active-category-link" : "category-link"}
                                >
                                    {categoryItem.name.toUpperCase()}
                                </Link>
                            </li>
                        );
                    })}
                </ul>
            </nav>

            <div className="header__logo">
                <img src={logo} alt="Logo" />
            </div>

            <div className="header__cart-container">
                <div className="header__cart">
                    <button
                        className="header__cart-btn"
                        onClick={() => setIsCartOpen((prev) => !prev)}
                        data-testid="cart-btn"
                    >
                        <i className="bi bi-cart"></i>
                        {totalItems > 0 && (
                            <span className="header__cart-count">{totalItems}</span>
                        )}
                    </button>

                    {isCartOpen && (
                        <CartOverlay isOpen={isCartOpen} onClose={() => setIsCartOpen(false)} />
                    )}
                </div>
            </div>
        </header>
    );
};

export default Header;
