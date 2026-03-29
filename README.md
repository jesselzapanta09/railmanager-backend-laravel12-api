# MSIT 114 - Activity 23 - Improved Laravel API with Front-End

This project is the **Laravel 12 backend API** for the RailManager system, built using Flask, flask-mysqldb, PyJWT, and flask-cors to handle data processing, authentication, email verification, and image management for users and train entities.

## Requirements

Before running the project, install:

- Python 3.10 or newer
- MySQL server
- `pip`

## Installation & Setup

### Step 1: Clone the Repository

```bash
git clone https://github.com/jesselzapanta09/railmanager-backend-laravel12-api.git
cd railmanager-backend-laravel12-api
```

### Step 2: Install Dependencies

```cmd
composer install
```

### Step 3: Run migration or Import the Database

# Option 1: Run migration

```cmd
php artisan migrate:fresh
```
# Option 2: Import the Database

1. Open your MySQL client (e.g., MySQL Workbench, phpMyAdmin, or CLI).
2. Create a new database:
```sql
   CREATE DATABASE trainappdb_laravel;
```
3. Import the provided SQL file(**trainappdb_laravel.sql**)


### Step 4: Run the Server (port )

```bash
php artisan serve --port=5000
```

The API should now be running at `http://localhost:5000` or `http://127.0.0.1:5000`.

---

# System Feafure using Laravel 12 API

1. CRUD with image - assigned entity.
2. User profile — Can modify user information with picture.
3. User registration using email — Send verification link through email.
4. Verify user using email address — Cannot log in if account is not verified.
5. Forgot password using email — Send a reset password link through email.
6. Log in and log out using authorization.


## Author

**Jessel Zapanta** — MSIT 114