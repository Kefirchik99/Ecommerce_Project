import React, { useContext, useState } from "react";
import { useMutation } from "@apollo/client";
import { CREATE_ORDER } from "../../graphql/mutations";
import { CartContext } from "../../context/CartContext";
import "./CartOverlay.scss";

const CartOverlay = ({ onClose }) => {
    const { cartItems, totalPrice, updateQuantity, clearCart } = useContext(CartContext);
    const [createOrder] = useMutation(CREATE_ORDER);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleCheckout = async () => {
        if (isSubmitting || cartItems.length === 0) return;
        setIsSubmitting(true);
        try {
            const products = cartItems.map((item) => ({
                productId: item.id.toString(),
                quantity: item.quantity,
                price: parseFloat(item.price),
            }));
            const { data } = await createOrder({ variables: { products } });
            if (data?.createOrder) {
                clearCart();
                onClose();
            } else {
                throw new Error("Failed to create order.");
            }
        } catch (error) {
            console.error("Error placing order:", error);
            alert("Failed to place order. Please try again.");
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <>
            <div className="cart-overlay__background" onClick={onClose} />
            <div className="cart-overlay" data-testid="cart-overlay">
                <div className="cart-overlay__content">
                    <h2 className="cart-overlay__title">
                        My Bag, {cartItems.length} {cartItems.length === 1 ? "item" : "items"}
                    </h2>
                    {cartItems.length > 0 ? (
                        <div className="cart-overlay__items">
                            {cartItems.map((item, index) => (
                                <div key={`${item.id}-instance-${index}`} className="cart-overlay__item">
                                    <div className="cart-overlay__info">
                                        <p className="cart-overlay__name">{item.name}</p>
                                        <p className="cart-overlay__price">${item.price.toFixed(2)}</p>
                                        <div className="cart-overlay__attributes">
                                            {item.attributes?.map((attr) => {
                                                const attrKebab = attr.name.toLowerCase().replace(/\s+/g, "-");
                                                return (
                                                    <div
                                                        key={attr.name}
                                                        className="cart-overlay__attribute"
                                                        data-testid={`cart-item-attribute-${attrKebab}`}
                                                    >
                                                        <span className="cart-overlay__attribute-label">{attr.name}:</span>
                                                        <div className="cart-overlay__attribute-options">
                                                            {attr.options.map((option) => {
                                                                const optionKebab = option.toLowerCase().replace(/\s+/g, "-");
                                                                const isSelected = attr.selectedOption === option;
                                                                const isColor = attr.type === "swatch";
                                                                return (
                                                                    <span
                                                                        key={option}
                                                                        className={`cart-overlay__attribute-option ${isSelected ? "selected" : ""
                                                                            } ${isColor ? "cart-overlay__color-swatch" : ""}`}
                                                                        style={isColor ? { backgroundColor: option } : {}}
                                                                        data-testid={`cart-item-attribute-${attrKebab}-${optionKebab}${isSelected ? "-selected" : ""
                                                                            }`}
                                                                    >
                                                                        {!isColor && option}
                                                                    </span>
                                                                );
                                                            })}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                    <div className="cart-overlay__quantity">
                                        <button
                                            onClick={() => updateQuantity(item.id, item.attributes, item.quantity + 1)}
                                            type="button"
                                            data-testid="cart-item-amount-increase"
                                        >
                                            +
                                        </button>
                                        <span data-testid="cart-item-amount">{item.quantity}</span>
                                        <button
                                            onClick={() => updateQuantity(item.id, item.attributes, item.quantity - 1)}
                                            type="button"
                                            data-testid="cart-item-amount-decrease"
                                        >
                                            -
                                        </button>
                                    </div>
                                    <img
                                        className="cart-overlay__image"
                                        src={item.gallery?.[0] || "https://via.placeholder.com/300"}
                                        alt={item.name}
                                    />
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="cart-overlay__empty">
                            <p>Your cart is empty.</p>
                        </div>
                    )}
                    {cartItems.length > 0 && (
                        <div className="cart-overlay__summary">
                            <div className="cart-overlay__total-line" data-testid="cart-total">
                                <span>Total</span>
                                <span>${totalPrice.toFixed(2)}</span>
                            </div>
                            <button
                                className="cart-overlay__checkout"
                                onClick={handleCheckout}
                                disabled={cartItems.length === 0 || isSubmitting}
                                type="button"
                            >
                                {isSubmitting ? "PLACING ORDER..." : "PLACE ORDER"}
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

export default CartOverlay;
