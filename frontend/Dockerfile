# Use Node.js LTS version
FROM node:18

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json (if exists)
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the frontend code
COPY . .

# Expose Vite's default port
EXPOSE 3000

# Start Vite development server
CMD ["npm", "run", "dev", "--", "--host"]