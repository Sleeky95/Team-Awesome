FROM alpine:latest

RUN apk --no-cache -U add php php-pgsql php-pdo php-pdo_pgsql

WORKDIR /app

COPY . .

CMD ["php", "-S", "localhost:8855", "-t", "."]
