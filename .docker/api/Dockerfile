FROM zorinalexey/ubuntu_22_04_php_fpm8.4:latest

RUN apt update && apt install -y htop && rm -rf /var/lib/apt/lists/*

RUN chmod 777 -R /var/www/html \
    && chmod 777 -R /var/log \
    && echo "" >> /etc/crontab \
    && echo "" >> /etc/crontab \
    && echo "* * * * * root /var/www/html/cron.sh >> /var/log/cron.sh.log 2>&1 " >> /etc/crontab \
    && echo "#" >> /etc/crontab