import React, { createContext, useState, useContext } from 'react';

export const HeaderContext = createContext();

export const HeaderProvider = ({ children }) => {
    const [category, setCategory] = useState('');

    return (
        <HeaderContext.Provider value={{ category, setCategory }}>
            {children}
        </HeaderContext.Provider>
    );
};

export const useHeader = () => useContext(HeaderContext);
