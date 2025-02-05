# test_task
Тестовое задание "Лаборатория Интернет"

## 🚀 Установка и запуск
### 1. Клонирование репозитория
```bash
git clone https://github.com/vladison27/test_task.git
cd test_task
```

### 2. Запуск Docker-compose
#### Для Windows
```bash
docker compose build --no-cache
docker compose up --pull always -d --wait
```
#### Для Linux/MacOS
```bash
make build
make up
```
### 3. Генерация ключа и миграция базы данных
```bash
docker compose exec php bin/console doctrine:database:create
docker compose exec php bin/console doctrine:migrations:migrate
```
Сервер запустится на `http://127.0.0.1:8000`

## 📡 REST API

- **`POST /api/user/create`** – Создание пользователя
- **`PATCH /api/user/{id}`** – Редактирование полей пользователя
- **`POST /api/user/auth`** – Авторизация по логину и паролю
- **`GET /api/user/{id}`** – Получение публичной инфорации о пользователе
