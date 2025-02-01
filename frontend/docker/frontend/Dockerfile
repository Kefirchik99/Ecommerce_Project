# Use an official Node runtime as a parent image
FROM node:21

# Set working directory inside the container
WORKDIR /var/www/html

# Copy package.json first, then install dependencies
COPY package.json package-lock.json ./

# Clear npm cache and force clean install to prevent missing modules
RUN npm cache clean --force && npm install --legacy-peer-deps

# Copy the rest of the app AFTER installing dependencies
COPY . .

# Expose the development server port
EXPOSE 5173

# Start Vite
CMD ["npm", "run", "dev"]
