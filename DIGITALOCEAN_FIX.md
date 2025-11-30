# Исправление 404 ошибки на DigitalOcean App Platform

## Проблема
Админ-панель не открывается: `404 Not Found` при доступе к `/admin/`

## Причина
DigitalOcean App Platform использует Nginx, а не Apache, поэтому `.htaccess` не работает.

## Решение

### Вариант 1: Использовать встроенный PHP сервер (рекомендуется)

1. **Создайте файл `router.php`** в корне проекта (уже создан)
2. **Обновите конфигурацию App Platform**:
   - Зайдите в настройки App на DigitalOcean
   - В разделе "Run Command" укажите:
     ```
     php -S 0.0.0.0:8080 -t . router.php
     ```
   - Или используйте файл `.do/app.yaml` (уже создан)

### Вариант 2: Исправить через веб-интерфейс DigitalOcean

1. Зайдите в ваш App на DigitalOcean
2. Settings → Components → Web Service
3. В "Run Command" измените на:
   ```
   php -S 0.0.0.0:8080 -t . router.php
   ```
4. Сохраните и перезапустите приложение

### Вариант 3: Прямой доступ к файлам

Попробуйте открыть напрямую:
- `https://seahorse-app-fto76.ondigitalocean.app/admin/index.php`

Если это работает, значит проблема только в маршрутизации.

## Проверка

После применения исправлений:
1. Откройте: `https://seahorse-app-fto76.ondigitalocean.app/admin/`
2. Должна открыться страница входа в админку
3. Логин: `admin`, Пароль: `admin123`

## Альтернативное решение: Nginx конфигурация

Если у вас есть доступ к Nginx конфигурации, добавьте:

```nginx
location /admin {
    try_files $uri $uri/ /admin/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

