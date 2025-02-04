FROM node:18

WORKDIR /var/www/html

COPY package.json ./

RUN npm cache clean --force && npm install --legacy-peer-deps

RUN npm install @rollup/rollup-linux-x64-gnu --legacy-peer-deps

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev"]
