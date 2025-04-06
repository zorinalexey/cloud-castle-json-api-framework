# Используем официальный образ Node.js
FROM node:16

RUN mkdir /opt/ws
RUN apt-get update && apt-get install -y htop && rm -rf /var/lib/apt/lists/*
COPY ./server/ /opt/ws
RUN chmod 777 -R /opt/ws

WORKDIR /opt/ws

RUN npm install

# Открываем порт
EXPOSE 3000

# Запускаем сервер
CMD ["node", "server.js"]