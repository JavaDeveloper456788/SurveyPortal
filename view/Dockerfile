FROM node:18.17.1-alpine

WORKDIR /app

COPY package.json .

COPY ./ ./

EXPOSE 3000

CMD sh -c "npm install && npm run build && npm run serve"