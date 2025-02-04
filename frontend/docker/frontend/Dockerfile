# Use an official Node LTS runtime as a parent image
FROM node:18

# Set working directory inside the container
WORKDIR /var/www/html

# Copy package.json and package-lock.json first
COPY package.json package-lock.json ./

# Remove the package-lock so that npm installs a fresh, Linux-appropriate dependency tree,
# then clear cache and install dependencies (using legacy-peer-deps)
RUN rm -f package-lock.json && npm cache clean --force && npm install --legacy-peer-deps

# Explicitly install the missing Linux-specific Rollup module
RUN npm install @rollup/rollup-linux-x64-gnu --legacy-peer-deps

# Copy the rest of the app AFTER installing dependencies
COPY . .

# Expose the development server port
EXPOSE 5173

# Start Vite
CMD ["npm", "run", "dev"]
