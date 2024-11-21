FROM nginx:1.27
COPY nginx/conf.d /etc/nginx/conf.d/
COPY public /var/www/public