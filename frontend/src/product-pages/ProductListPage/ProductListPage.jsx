import React, { useEffect, useContext } from "react";
import "./ProductListPage.scss";
import { useParams } from "react-router-dom";
import { useQuery } from "@apollo/client";
import { GET_PRODUCTS_WITH_FILTER } from "../../graphql/queries";
import ProductCard from "../../components/ProductCard";
import { CartContext } from "../../context/CartContext";
import { useHeader } from "../../context/HeaderContext";

const ProductListPage = () => {
  const { categoryName } = useParams();
  const { addItem } = useContext(CartContext);
  const { setCategory } = useHeader();

  useEffect(() => {
    if (categoryName) {
      setCategory(categoryName.toLowerCase());
    }
  }, [categoryName, setCategory]);

  const { loading, error, data } = useQuery(GET_PRODUCTS_WITH_FILTER, {
    variables: { category: categoryName.toLowerCase() === "all" ? null : categoryName.toLowerCase() },
  });

  const pageTitle = categoryName.charAt(0).toUpperCase() + categoryName.slice(1).toLowerCase();

  const handleQuickShop = (product) => {
    const defaultAttributes = product.attributes?.map((attr) => ({
      name: attr.name,
      type: attr.type,
      selectedOption: attr.items[0]?.value,
      options: attr.items.map((i) => i.value),
    })) || [];

    addItem({
      id: product.id,
      name: product.name,
      price: product.price,
      gallery: product.gallery,
      attributes: defaultAttributes,
    });
  };

  if (loading) return <p>Loading products...</p>;
  if (error) return <p>Error loading products: {error.message}</p>;

  return (
    <div className="product-list-page">
      <div className="product-list-page__content">
        <h2 className="product-list-page__title">{pageTitle}</h2>

        <div className="product-list-page__products">
          {data?.products.map((product) => (
            <ProductCard
              key={product.id}
              product={product}
              onQuickShop={() => handleQuickShop(product)}
            />
          ))}
        </div>
      </div>
    </div>
  );
};

export default ProductListPage;
