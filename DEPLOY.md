# Инструкция по деплою на DigitalOcean

## Что было исправлено

1. **Все абсолютные пути заменены на относительные**
   - Изображения теперь используют `./img/` вместо `https://travel.gtr.lv/img/`
   - Все ссылки работают на любом домене

2. **Создан конфигурационный файл `config.php`**
   - Автоматически определяет базовый URL
   - Работает на любом домене (включая временный DigitalOcean)

3. **Обновлены PHP-скрипты**
   - `newsletter.php` использует конфигурацию
   - Все пути относительные

4. **Создан `.htaccess`**
   - Правильная маршрутизация
   - Защита админ-панели

## Структура файлов

```
/
├── index.html          # Главная страница (работает на любом домене)
├── config.php          # Конфигурация сайта
├── .htaccess          # Настройки Apache
├── newsletter.php      # Обработка подписки
├── log.php            # Логирование
├── load-admin-data.php # Загрузка данных админки
├── img/               # Изображения
└── admin/             # Админ-панель
    ├── index.php      # Вход в админку
    ├── dashboard.php  # Панель управления
    └── data/          # Данные (меню, страницы, переводы)
```

## Деплой на DigitalOcean

### 1. Загрузка файлов

Загрузите все файлы на сервер через SFTP или Git:

```bash
# Если используете Git
git clone <your-repo>
cd GTR-TRAVEL

# Или загрузите через SFTP/FileZilla
```

### 2. Настройка прав доступа

```bash
# Установите права на запись для директорий с данными
chmod 755 admin/data
chmod 644 admin/data/*.json

# Убедитесь, что PHP может создавать файлы
chmod 755 .
```

### 3. Настройка веб-сервера

#### Apache

Убедитесь, что включен модуль `mod_rewrite`:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Файл `.htaccess` уже создан и настроен.

#### Nginx

Если используете Nginx, добавьте в конфигурацию:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/GTR-TRAVEL;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Защита админ-панели
    location /admin/data {
        deny all;
    }
}
```

### 4. Проверка работы

1. **Главная страница**: Откройте `http://your-domain.com/` или временный DigitalOcean URL
2. **Админ-панель**: Откройте `http://your-domain.com/admin/`
   - Логин: `admin`
   - Пароль: `admin123` (⚠️ **ОБЯЗАТЕЛЬНО измените!**)

### 5. Изменение пароля админки

Откройте `admin/config.php` и измените:

```php
define('ADMIN_USERNAME', 'your-username');
define('ADMIN_PASSWORD_HASH', password_hash('your-secure-password', PASSWORD_DEFAULT));
```

Для генерации хеша пароля:

```php
<?php
echo password_hash('your-password', PASSWORD_DEFAULT);
?>
```

### 6. Настройка домена

После того, как вы пропишете новый домен:

1. **Ничего менять не нужно!** Все пути относительные
2. Сайт автоматически будет работать на новом домене
3. Проверьте, что все изображения загружаются
4. Проверьте работу админ-панели

## Проверка после деплоя

- [ ] Главная страница открывается
- [ ] Изображения загружаются
- [ ] Админ-панель доступна по `/admin/`
- [ ] Форма подписки на новости работает
- [ ] WhatsApp кнопка работает
- [ ] Переключение языков работает
- [ ] После смены домена все работает без изменений

## Решение проблем

### Изображения не загружаются

Проверьте права доступа:
```bash
chmod 644 img/*
```

### Админ-панель не открывается

1. Проверьте, что PHP работает
2. Проверьте права на `admin/data/`
3. Проверьте логи ошибок: `tail -f /var/log/apache2/error.log`

### 404 ошибки

1. Убедитесь, что `mod_rewrite` включен
2. Проверьте `.htaccess` файл
3. Проверьте настройки веб-сервера

## Безопасность

⚠️ **ВАЖНО:**

1. Измените пароль админки сразу после деплоя
2. Убедитесь, что `admin/data/` защищена (уже настроено в `.htaccess`)
3. Регулярно делайте резервные копии `admin/data/*.json`
4. Не храните пароли в открытом виде

## Поддержка

При возникновении проблем проверьте:
- Логи PHP: `/var/log/php/error.log`
- Логи Apache: `/var/log/apache2/error.log`
- Консоль браузера (F12)

