#!/bin/bash

echo "Развертывание проекта..."

echo "Установка Composer зависимостей..."
composer install

echo "Установка Npm зависимостей..."
npm install
npm run build

echo "Настройка файла окружения..."
cp .env.example .env

echo "Генерация ключа приложения..."
php artisan key:generate

echo "Очистка кэша..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Выполнение миграций и заполнение базы данных..."
php artisan migrate --seed

echo "Создание ссылки для storage..."
php artisan storage:link

echo "Развертывание завершено!" 