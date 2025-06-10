FROM node:16-alpine

WORKDIR /usr/src/app

# Install dependencies
COPY app/package*.json ./
RUN npm install

# Copy app source
COPY app/ .

EXPOSE 3000

CMD [ "node", "server.js" ]