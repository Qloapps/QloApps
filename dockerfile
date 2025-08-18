# Dockerfile
FROM node:18

WORKDIR /app

# Install dependencies
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

# Copy source
COPY . .

# Build app
RUN yarn build

# Expose port
EXPOSE 3000

# Run app
CMD ["yarn", "start"]
