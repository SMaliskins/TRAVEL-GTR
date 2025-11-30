#!/bin/bash
# Скрипт для отправки проекта на GitHub
# Замените YOUR_USERNAME и REPO_NAME на ваши данные

echo "Введите ваш GitHub username:"
read GITHUB_USERNAME

echo "Введите название репозитория (например: gtr-travel):"
read REPO_NAME

# Добавляем remote
git remote add origin https://github.com/$GITHUB_USERNAME/$REPO_NAME.git

# Переименовываем ветку в main (если нужно)
git branch -M main

# Отправляем на GitHub
echo "Отправка на GitHub..."
git push -u origin main

echo "Готово! Проект загружен на GitHub: https://github.com/$GITHUB_USERNAME/$REPO_NAME"

