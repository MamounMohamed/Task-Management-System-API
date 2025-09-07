# Task Management API

This is the Task Management System API, built with Laravel 12 and Sanctum authentication. This API provides endpoints for user authentication, task management, and task dependencies. The project is designed to be robust, maintainable, and developer-friendly.

## Table of Contents

- [Project Overview](#project-overview)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Running the Project](#running-the-project)
- [Approach](#approach)
- [API Documentation](#api-documentation)
- [Postman Collection](#postman-collection)
- [Tests](#tests)
- [Error Handling](#error-handling)
- [Reviewer Quick Start Guide](#reviewer-quick-start-guide)
- [License](#license)

---

## Project Overview

This API allows users to:
- Register and login with roles (manager, user)
- Perform CRUD operations on tasks
- Assign tasks to users
- Track task dependencies
- Filter tasks by status or due date range

The API uses Laravel Policies for authorization and Sanctum for API token-based authentication. Responses follow a consistent JSON structure:

```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {...}
}
````

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

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Set your environment variables:

```
APP_NAME=TaskManagementAPI
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=

Or 

DB_CONNECTION=sqlite


SANCTUM_STATEFUL_DOMAINS=localhost:8000
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

Start the Laravel development server:

```bash
php artisan serve
```

Your API will be available at `http://localhost:8000/api`.

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


---

## API Documentation


### Seeded Users ###

| Email            | Password | Role    |
| ---------------- | -------- | ------- |
| test_manager@example.com | password | manager |
| test_user@example.com | password | user |
| test_user2@example.com | password | user |

### Seeded Tasks ###

10 Seeded Tasks are created for these users with various statuses and due dates and dependencies.

### Auth

| Endpoint         | Method | Description             |
| ---------------- | ------ | ----------------------- |
| `/auth/register` | POST   | Register a new user     |
| `/auth/login`    | POST   | Login and receive token |
| `/auth/logout`   | POST   | Logout and revoke token |

### Tasks

| Endpoint                   | Method | Description                    |
| -------------------------- | ------ | ------------------------------ |
| `/tasks`                   | GET    | List all tasks                 |
| `/tasks`                   | POST   | Create a new task              |
| `/tasks/{id}`              | GET    | Get task by ID                 |
| `/tasks/{id}`              | PUT    | Update a task                  |
| `/tasks/{id}/dependencies` | POST   | Add dependencies to a task     |
| `/tasks?status=completed`  | GET    | Filter tasks by status         |
| `/tasks?due_from=&due_to=` | GET    | Filter tasks by due date range |

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

```bash
php artisan test
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
  "message": "Error message",
}
```

**Common Exceptions:**

| Exception Type            | HTTP Code | Message                                      |
| ------------------------- | --------- | -------------------------------------------- |
| ValidationException       | 422       | Validation failed                            |
| AuthorizationException    | 403       | Forbidden: You do not have permission        |
| UnauthorizedHttpException | 401       | Unauthenticated: You must be logged in       |
| NotFoundHttpException     | 404       | Resource not found                           |
| HttpException             | Variable  | Custom HTTP exception messages               |
| Other exceptions          | 500       | Something went wrong (detailed in local env) |

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

> Skip this if a test user already exists.

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
  "assignee_id": 2
}
```

### 5. Update a Task

**Endpoint:** `PUT /tasks/{id}`
**Body Example:**

```json
{
  "status": "completed"
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

