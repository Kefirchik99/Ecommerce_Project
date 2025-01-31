import React, { createContext, useState } from "react";

export const CartContext = createContext();

export const CartProvider = ({ children }) => {
    const [cartItems, setCartItems] = useState([]);
    const [isCartOpen, setIsCartOpen] = useState(false);

    const totalItems = cartItems.reduce((total, item) => total + item.quantity, 0);
    const totalPrice = cartItems.reduce((total, item) => total + item.quantity * item.price, 0);

    const addItem = (product, shouldOpenCart = true) => {
        setCartItems((prevItems) => {
            const existingItem = prevItems.find(
                (item) =>
                    item.id === product.id &&
                    JSON.stringify(item.attributes) === JSON.stringify(product.attributes)
            );

            if (existingItem) {
                return prevItems.map((item) =>
                    item.id === product.id &&
                        JSON.stringify(item.attributes) === JSON.stringify(product.attributes)
                        ? { ...item, quantity: item.quantity + 1 }
                        : item
                );
            }

            return [...prevItems, { ...product, quantity: 1 }];
        });

        if (shouldOpenCart) {
            setIsCartOpen(true);
        }
    };

    const updateQuantity = (productId, attributes, newQuantity) => {
        setCartItems((prevItems) => {
            if (newQuantity < 1) {
                return prevItems.filter(
                    (item) =>
                        !(item.id === productId && JSON.stringify(item.attributes) === JSON.stringify(attributes))
                );
            }

            return prevItems.map((item) =>
                item.id === productId && JSON.stringify(item.attributes) === JSON.stringify(attributes)
                    ? { ...item, quantity: newQuantity }
                    : item
            );
        });
    };

    const removeItem = (productId, attributes) => {
        setCartItems((prevItems) =>
            prevItems.filter(
                (item) =>
                    !(item.id === productId && JSON.stringify(item.attributes) === JSON.stringify(attributes))
            )
        );
    };

    const clearCart = () => {
        setCartItems([]);
        setIsCartOpen(false);
    };

    return (
        <CartContext.Provider
            value={{
                cartItems,
                totalItems,
                totalPrice,
                addItem,
                updateQuantity,
                removeItem,
                clearCart,
                isCartOpen,
                setIsCartOpen,
            }}
        >
            {children}
        </CartContext.Provider>
    );
};
