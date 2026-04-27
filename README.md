# 📚 LibAlexandria - Book Management System

A simple Laravel-based Book Management System that allows users to manage a collection of books with full CRUD functionality, file uploads, and soft delete features.

---

## 🚀 Features

### ✅ Core Features
- Create, Read, Update, Delete (CRUD) books
- View detailed book information
- Upload and display book cover images
- Clean and responsive UI

### ⭐ Expanded Features (for higher grade)

#### 1. Soft Delete with Restore
- Books are not immediately deleted
- Moved to **Trash**
- Can be:
  - ♻️ Restored
  - ❌ Permanently deleted

#### 2. File Upload with Storage Management
- Upload cover images for books
- Stored using Laravel file storage
- Displayed in both active and trashed views

#### 3. Database Seeding with Faker
- Automatically generate sample books
- Uses Laravel Factory + Seeder
- Helps test UI and features quickly

---

## 🖼️ UI Features
- Modern card-based layout
- Styled tables and buttons
- Custom modal confirmations (no browser alerts)
- Responsive and centered layout

---

## 🛠️ Tech Stack

- **Framework:** Laravel 13
- **Language:** PHP 8.4
- **Database:** PostgreSQL
- **Frontend:** Blade + CSS
- **Tools:** Composer, Artisan CLI

---

## ⚙️ Installation Guide

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
cd YOUR_REPO_NAME
```

### 2. Install dependencies
```bash
composer install
```
### 3. Setup environment
- cp, .env.example, .env
```
php artisan key:generate
```
### 4. Configure database

#### Update .env:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=LibAlexandria
DB_USERNAME=postgres
DB_PASSWORD=your_password
### 5. Run migrations
php artisan migrate
```

### 6. Seed database (optional but recommended)
```
php artisan db:seed --class=BookSeeder
```
### 7. Run the server
```
php artisan serve
```

- Open in browser:
```
http://127.0.0.1:8000/books
```

## 📂 Project Structure (Important Files)

```
app/
 └── Models/
      └── Book.php

app/Http/Controllers/
 └── BookController.php

database/
 ├── factories/
 │    └── BookFactory.php
 └── seeders/
      └── BookSeeder.php

resources/views/
 ├── layouts/
 │    └── app.blade.php
 └── books/
      ├── index.blade.php
      ├── create.blade.php
      ├── edit.blade.php
      ├── show.blade.php
      ├── trashed.blade.php
      └── form.blade.php

routes/
 └── web.php
```

## 🔄 System Flow
### 📘 Book Management

```
Add → Edit → View → Delete (soft delete)
```

### 🗑️ Trash System
- Deleted books go to Trash
- Options:
1. Restore book
2. Permanently delete

### 🧪 Testing Features

You can test:

- Adding books with/without cover
- Editing book details
- Soft deleting books
- Restoring from Trash
- Permanent deletion
- Faker-generated data

---

## 📌 Notes
- Soft deletes use Laravel’s built-in SoftDeletes
- File uploads stored in storage/app/public
- Run this if images don’t show:
```
php artisan storage:link
```


---

## 👨‍💻 Author

Avillanosa, WK
CMSC129 – Laboratory 2

---

