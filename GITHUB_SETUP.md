# Инструкция: Отправка проекта на GitHub

## Шаг 1: Создайте репозиторий на GitHub

1. Зайдите на [github.com](https://github.com)
2. Нажмите кнопку **"+"** в правом верхнем углу → **"New repository"**
3. Заполните:
   - **Repository name**: `gtr-travel` (или любое другое имя)
   - **Description**: "Gulliver Travel Website"
   - Выберите **Public** или **Private**
   - **НЕ** ставьте галочки на "Initialize with README"
4. Нажмите **"Create repository"**

## Шаг 2: Выполните команды в терминале

Откройте терминал в папке проекта и выполните:

```bash
# Перейдите в папку проекта (если еще не там)
cd /Users/sergejsmaliskins/Downloads/WWW/GTR-TRAVEL

# Инициализируйте Git репозиторий
git init

# Добавьте все файлы (кроме тех, что в .gitignore)
git add .

# Сделайте первый коммит
git commit -m "Initial commit: GTR Travel website with relative paths"

# Добавьте удаленный репозиторий (замените YOUR_USERNAME на ваш GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/gtr-travel.git

# Отправьте код на GitHub
git branch -M main
git push -u origin main
```

## Шаг 3: Если GitHub запросит авторизацию

Если GitHub попросит логин/пароль:
- Используйте **Personal Access Token** вместо пароля
- Создайте токен: GitHub → Settings → Developer settings → Personal access tokens → Generate new token
- Или используйте GitHub CLI: `gh auth login`

## Альтернатива: Использование SSH

Если настроен SSH ключ:

```bash
git remote add origin git@github.com:YOUR_USERNAME/gtr-travel.git
git push -u origin main
```

## Что будет загружено

✅ Все файлы проекта
❌ Файлы из `.gitignore` (логи, временные файлы) НЕ будут загружены

## После загрузки

Вы сможете:
- Клонировать проект: `git clone https://github.com/YOUR_USERNAME/gtr-travel.git`
- Загружать на DigitalOcean через Git
- Отслеживать изменения

