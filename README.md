# Task Management API

This is the Task Management System API, built with Laravel 12 and Sanctum authentication. This API provides endpoints for user authentication, task management, and task dependencies. The project is designed to be robust, maintainable, and developer-friendly.

## Table of Contents

* [Project Overview](#project-overview)
* [Installation](#installation)
* [Environment Setup](#environment-setup)

  * [Local Environment](#local-environment)
  * [Docker Environment](#docker-environment)
* [Running the Project](#running-the-project)

  * [Option 1: Laravel Artisan (Local)](#option-1-laravel-artisan-local)
  * [Option 2: Docker with Make](#option-2-docker-with-make)
  * [Option 3: Docker without Make](#option-3-docker-without-make)
* [Approach](#approach)
* [API Documentation](#api-documentation)
* [Postman Collection](#postman-collection)
* [Tests](#tests)
* [Error Handling](#error-handling)
* [Reviewer Quick Start Guide](#reviewer-quick-start-guide)
* [License](#license)

---

## Project Overview

This API allows users to:

* Register and login with roles (manager, user)
* Perform CRUD operations on tasks
* Assign tasks to users
* Track task dependencies
* Filter tasks by status or due date range

The API uses Laravel Policies for authorization and Sanctum for API token-based authentication. Responses follow a consistent JSON structure:

```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {...}
}
```

---

## Installation

Clone the repository:

```bash
git clone https://github.com/MamounMohamed/Task-Management-System-API.git
cd Task-Management-System-API
```

Install dependencies via Composer:

```bash
composer install
```

---

## Environment Setup

### Local Environment

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Example local configuration:

```env
APP_NAME=TaskManagementAPI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=

# or use sqlite
# DB_CONNECTION=sqlite
```

### Docker Environment

When running with Docker, update `.env` with container-based settings:

```env
APP_NAME=TaskManagementAPI
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=laravel
DB_PASSWORD=root

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

Generate the application key:

```bash
php artisan key:generate
```

Run database migrations and seeders:

```bash
php artisan migrate --seed
```

---

## Running the Project

### Option 1: Laravel Artisan (Local)

```bash
php artisan serve
```


---

### Option 2: Docker with Make

If your system supports `make`, you can use the provided `Makefile`.

Start containers:

```bash
make up
```

Stop containers:

```bash
make down
```

Run migrations with seeders:

```bash
make migrate
```

Generate app key:

```bash
make key
```

Link storage:

```bash
make storage
```

View logs:

```bash
make logs
```

Run tests:

```bash
make test
```

Open tinker shell:

```bash
make tinker
```

---

### Option 3: Docker without Make

If you don’t have `make` installed, use Docker Compose directly:

Start containers:

```bash
docker compose up -d --build
```

Stop containers:

```bash
docker compose down
```

Run migrations with seeders:

```bash
docker exec -it task-management-app php artisan migrate --seed
```

Generate app key:

```bash
docker exec -it task-management-app php artisan key:generate
```

Link storage:

```bash
docker exec -it task-management-app php artisan storage:link
```

Run tests:

```bash
docker exec -it task-management-app php artisan test
```

---

Your API will be available at:
👉 `http://localhost:8000/api`

---


## Approach

* **Clean Architecture**: Controllers handle requests, Services handle business logic, and Resources handle API responses.
* **Policies**: Laravel policies protect endpoints based on user roles.
* **Validation**: Requests are validated using custom FormRequest classes.
* **Seedeed Data**: Database seeders populate the database with sample data.
* **Consistent Responses**: All API responses are standardized with `success`, `message`, and `data`.
* **Exception Handling**: Centralized exception handler for validation, authentication, authorization, and general errors.
* **Testing**: Unit and feature tests for API functionality.
* **Security**: Authentication and authorization using Laravel Sanctum.
* **Documentation**: README.md file with API documentation.
* **Reviewer Quick Start Guide**: Steps to quickly test the API.
* **Erd**: Entity-Relationship Diagram (ERD) for the database. at ./erd.svg

---

## API Documentation

### Seeded Users

| Email                                                        | Password | Role    |
| ------------------------------------------------------------ | -------- | ------- |
| [test\_manager@example.com](mailto:test_manager@example.com) | password | manager |
| [test\_user@example.com](mailto:test_user@example.com)       | password | user    |
| [test\_user2@example.com](mailto:test_user2@example.com)     | password | user    |

### Seeded Tasks

10 Seeded Tasks are created for these users with various statuses, due dates, and dependencies.

### Auth

| Endpoint         | Method | Description             |
| ---------------- | ------ | ----------------------- |
| `/auth/register` | POST   | Register a new user     |
| `/auth/login`    | POST   | Login and receive token |
| `/auth/logout`   | POST   | Logout and revoke token |

### Tasks

| Endpoint                   | Method | Description                                  |
| -------------------------- | ------ | -------------------------------------------- |
| `/tasks`                   | GET    | List all tasks                               |
| `/tasks`                   | POST   | Create a new task (can include dependencies) |
| `/tasks/{id}`              | GET    | Get task by ID                               |
| `/tasks/{id}`              | PUT    | Update a task (can include dependencies)     |
| `/tasks/{id}/dependencies` | POST   | Add dependencies to a task                   |
| `/tasks?status=completed`  | GET    | Filter tasks by status                       |
| `/tasks?due_from=&due_to=` | GET    | Filter tasks by due date range               |
| `/tasks?assignee_id={id}`  | GET    | Filter tasks by assignee                     |

---

### Example: Create a Task with Dependencies

```json
{
  "title": "New Feature Task",
  "description": "Implement feature X",
  "due_date": "2025-09-15",
  "assignee_id": 2,
  "dependencies": [1, 3, 5]
}
```

### Example: Update a Task with Dependencies

```json
{
  "status": "in_progress",
  "dependencies": [2, 4]
}
```

---

## Postman Collection

The Postman collection is included as `TaskManagementAPI.postman_collection.json`.

**Features:**

* Organized by `Auth` and `Tasks` folders.
* Automatically sets bearer token after login.
* Includes test scripts to capture IDs for dynamic requests.
* Supports filtering tasks and managing dependencies.

**How to Use:**

1. Import `TaskManagementAPI.postman_collection.json` into Postman.
2. Set `{{base_url}}` variable to `http://localhost:8000/api`.
3. Login with a user to populate `{{token}}`.
4. Run API requests sequentially or use the collection runner.

---

## Tests

The project uses PHPUnit for testing:

Run tests locally:

```bash
php artisan test
```

Run tests in Docker:

```bash
make test
```

or

```bash
docker exec -it task-management-app php artisan test
```
**Tests Cover:**

* Authentication (register, login, logout)
* Task CRUD operations
* Task filtering

---

## Error Handling

All API errors return a consistent JSON structure:

```json
{
  "success": false,
  "message": "Error message"
}
```

**Common Exceptions:**

| Exception Type            | HTTP Code | Message                                |
| ------------------------- | --------- | -------------------------------------- |
| ValidationException       | 422       | Validation failed                      |
| AuthorizationException    | 403       | Forbidden: You do not have permission  |
| UnauthorizedHttpException | 401       | Unauthenticated: You must be logged in |
| NotFoundHttpException     | 404       | Resource not found                     |
| HttpException             | Variable  | Custom HTTP exception messages         |
| Other exceptions          | 500       | Something went wrong (local only)      |

---

## Reviewer Quick Start Guide

### 1. Set Up Postman

* Import `TaskManagementAPI.postman_collection.json`.
* Set `{{base_url}}` variable to `http://localhost:8000/api`.

### 2. Register a User (Optional)

**Endpoint:** `POST /auth/register`
**Body Example:**

```json
{
  "name": "John Manager",
  "email": "manager@example.com",
  "password": "password",
  "password_confirmation": "password",
  "role": "manager"
}
```

### 3. Login

**Endpoint:** `POST /auth/login`
**Body Example:**

```json
{
  "email": "manager@example.com",
  "password": "password"
}
```

Postman will automatically save the token in `{{token}}`.

### 4. Create a Task

**Endpoint:** `POST /tasks`
**Body Example:**

```json
{
  "title": "New Task",
  "description": "Task description",
  "due_date": "2025-09-15",
  "assignee_id": 2,
  "dependencies": [1, 2]
}
```

### 5. Update a Task

**Endpoint:** `PUT /tasks/{id}`
**Body Example:**

```json
{
  "status": "completed",
  "dependencies": [3, 4]
}
```

### 6. Add Task Dependencies

**Endpoint:** `POST /tasks/{id}/dependencies`
**Body Example:**

```json
{
  "dependencies": [1, 2]
}
```

### 7. View & Filter Tasks

* **Get All Tasks:** `GET /tasks`
* **Filter by Status:** `GET /tasks?status=completed`
* **Filter by Due Date:** `GET /tasks?due_from=2025-09-01&due_to=2025-09-30`
* **Filter by Assignee:** `GET /tasks?assignee_id={id}`
* **Get Task by ID:** `GET /tasks/{id}`

### 8. Logout

**Endpoint:** `POST /auth/logout`

This will revoke your token.

---

### Notes

* All requests require `Authorization: Bearer {{token}}` header (except register/login).
* All responses are JSON with consistent `success`, `message`, and `data`.
* Validation errors return `422` with field-level messages.
* Forbidden actions return `403` with explanatory messages.

---

