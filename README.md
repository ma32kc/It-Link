# Car Ads API

REST API сервис для управления объявлениями о продаже автомобилей.

Стек: PHP 8, Yii2, PostgreSQL. Приложение построено по многослойной архитектуре
(Entity, DataMapper, Repository, Service) с внедрением зависимостей и следует
принципам SOLID.

## Архитектура

Доменная модель не зависит от фреймворка, доступ к данным реализован через
Yii2 DAO без ActiveRecord — сопоставление строк и объектов выполняет DataMapper.

```
src/
  Domain/
    Entity/            Car, CarOption — доменные сущности
    Repository/        CarRepositoryInterface — контракт хранилища
  Infrastructure/
    Persistence/       CarRepository (реализация), CarMapper (DataMapper)
  Application/
    Controller/        CarController — транспортный слой (REST)
    Service/           CarService — бизнес-сценарии
    Dto/               формы валидации и представления ответа
    Exception/         ValidationException
config/                конфигурация приложения и DI
migrations/            миграции базы данных
tests/                 unit-тесты
docker/                Dockerfile и конфигурация nginx
```

## Требования

- Docker и Docker Compose (рекомендуемый способ), либо
- PHP >= 8.1 с расширениями `pdo` и `pdo_pgsql`, Composer и PostgreSQL для
  локального запуска.

## Запуск через Docker (рекомендуется)

```bash
git clone git@github.com:ma32kc/It-Link.git
cd It-Link

cp .env.example .env

docker compose up -d --build
docker compose exec php composer install
docker compose exec php php yii migrate/up
```

Приложение будет доступно по адресу `http://localhost:8080`.

Полезные команды:

```bash
# Запуск unit-тестов
docker compose exec php ./vendor/bin/phpunit

# Остановка
docker compose down
```

## Локальный запуск

```bash
git clone git@github.com:ma32kc/It-Link.git
cd It-Link

composer install

# Настройте подключение к PostgreSQL через переменные окружения
# (см. .env.example) или переменные окружения оболочки.
cp .env.example .env

php yii migrate/up
php yii serve
```

По умолчанию `php yii serve` поднимает сервер на `http://localhost:8080`.

## Переменные окружения

| Переменная              | Назначение                       | По умолчанию |
|-------------------------|----------------------------------|--------------|
| `DB_HOST`               | Хост PostgreSQL                  | `127.0.0.1`  |
| `DB_PORT`               | Порт PostgreSQL                  | `5432`       |
| `DB_NAME`               | Имя базы данных                  | `car_ads`    |
| `DB_USER`               | Пользователь БД                  | `postgres`   |
| `DB_PASSWORD`           | Пароль БД                        | `postgres`   |
| `YII_DEBUG`             | Режим отладки                    | `false`      |
| `YII_ENV`               | Окружение (`dev`/`prod`)         | `prod`       |
| `COOKIE_VALIDATION_KEY` | Ключ валидации cookie            | —            |

## REST API

Все ответы возвращаются в формате JSON.

### `POST /car/create`

Создаёт объявление. Блок `options` необязателен: если он отсутствует или равен
`null`, характеристики не создаются; если передан объект — все его поля
обязательны.

Тело запроса:

```json
{
  "title": "BMW X5 2020",
  "description": "Отличное состояние, один владелец.",
  "price": 2500000,
  "photo_url": "https://example.com/car.jpg",
  "contacts": "+7 900 000-00-00",
  "options": {
    "brand": "BMW",
    "model": "X5",
    "year": 2020,
    "body": "SUV",
    "mileage": 65000
  }
}
```

Ответ `201 Created`:

```json
{
  "id": 1,
  "title": "BMW X5 2020",
  "description": "Отличное состояние, один владелец.",
  "price": 2500000,
  "photo_url": "https://example.com/car.jpg",
  "contacts": "+7 900 000-00-00",
  "created_at": "2026-09-10T22:00:00+00:00",
  "options": {
    "brand": "BMW",
    "model": "X5",
    "year": 2020,
    "body": "SUV",
    "mileage": 65000
  }
}
```

При ошибке валидации возвращается `422 Unprocessable Entity` с полем `errors`.

### `GET /car/{id}`

Возвращает одно объявление (`200 OK`) с техническими характеристиками, если они
есть. Если объявление не найдено — `404 Not Found`.

### `GET /car/list?page={page}`

Возвращает страницу объявлений (`200 OK`). Размер страницы задаётся параметром
`car.list.pageSize` (по умолчанию 20).

```json
{
  "items": [ /* объявления */ ],
  "pagination": {
    "page": 1,
    "pageSize": 20,
    "totalCount": 42,
    "pageCount": 3
  }
}
```

Пример запроса:

```bash
curl -X POST http://localhost:8080/car/create \
  -H "Content-Type: application/json" \
  -d '{"title":"BMW X5 2020","description":"Отличное состояние","price":2500000,"photo_url":"https://example.com/car.jpg","contacts":"+7 900 000-00-00"}'
```

## База данных

Схема создаётся миграциями:

- `car` — объявления (`id`, `title`, `description`, `price`, `photo_url`,
  `contacts`, `created_at`);
- `car_option` — технические характеристики (`id`, `car_id`, `brand`, `model`,
  `year`, `body`, `mileage`), связь has-one через внешний ключ и уникальный
  индекс по `car_id`.

Создание новой миграции:

```bash
php yii migrate/create create_example_table
```

## Тесты

```bash
composer test
# или
./vendor/bin/phpunit
```
