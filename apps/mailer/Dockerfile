FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install --omit=dev --ignore-scripts --no-audit --no-fund

COPY . .

EXPOSE 4001

HEALTHCHECK --interval=15s --timeout=5s --retries=3 \
    CMD curl -f http://localhost:4001/health || exit 1

CMD ["node", "src/index.js"]
