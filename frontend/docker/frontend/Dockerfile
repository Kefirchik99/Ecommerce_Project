# Use an official Node runtime as a parent image
FROM node:16

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install frontend dependencies
RUN npm install

# Expose port
EXPOSE 5173

# Start Vite development server
CMD ["npm", "run", "dev", "--", "--host"]
