FROM redis:7

RUN apt-get update && apt-get install -y htop && rm -rf /var/lib/apt/lists/*

EXPOSE 6379

CMD ["redis-server"]