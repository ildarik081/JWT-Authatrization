# Авторизация

## Пример работы регистрации и авторизации

### Функционал

- Авторизация/Регистрация пользователей;
- Выдача/Обновление JWT (json web token);
- Редактирование учетных данных пользователей;

### Технологический стэк

- php 8.1
- postgres 13
- symfony 5.4
- redis 6.2.6

### Install

1. Запуск
```bash
docker-compose up
```
2. Зайти в контейнер
```bash
docker exec -it app /bin/bash
```
3. Установить зависимости
```bash
composer ins
```
4. Накатить миграции
```bash
php bin/console doctrine:migrations:migrate
```
5. Сгенерировать приватный ключ
```bash
openssl genrsa -out "config/.keys/private.pem" 256
```
6. Сгенерировать публичный ключ
```bash
openssl rsa -in "config/.keys/private.pem" -pubout -out "config/.keys/public.pem"
```
7. Зайти в swagger по адресу http://127.0.0.1/api/doc

### X-DEBUG

1. Установить расширение [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug) (VSCode)

2. Раскоментировать *client_host* в зависимости от OS
```Dockerfile
# Путь .docker/dev/php/Dockerfile
# Для Windows
&& echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
```
```Dockerfile
# Путь .docker/dev/php/Dockerfile
# Для Linux
&& echo "xdebug.client_host=172.17.0.1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
```

### Тесты

1. Зайти в контейнер
```bash
docker exec -it app /bin/bash
```
2. Накатить миграции (первый запуск)
```bash
php bin/console doctrine:migrations:migrate --env=test
```
3. Накатить фикстуры (первый запуск)
```bash
php bin/console doctrine:fixtures:load --env=test
```
4. Запустить unit тесты
```bash
make test
```

### PHPSTAN

1. Зайти в контейнер
```bash
docker exec -it app /bin/bash
```
2. Запустить phpstan
```bash
make phpstan
```

### PHPMD

1. Зайти в контейнер
```bash
docker exec -it app /bin/bash
```
2. Запустить phpstan
```bash
make phpmd
```
