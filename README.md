# BareBonesPHP

Core PHP project with authentication and CRUD — built from scratch to understand the fundamentals beneath Laravel frameworks.

## 🎯 Project Purpose

This project is designed to understand the **underlying mechanics** that Laravel abstracts away. By building authentication and CRUD operations from scratch using raw PHP, I'll gain deep insights into:

- How requests and responses work at the HTTP level
- Session and cookie management without framework magic
- Raw SQL queries without Eloquent ORM
- Manual routing without Laravel's router
- Security implementation without middleware abstractions

## 📋 Project Scope (Intentionally Small)

### Core Features

1. **Authentication System**
   - User Registration (signup)
   - User Login with password hashing
   - Session management
   - Logout functionality

2. **Dashboard**
   - Protected route requiring authentication
   - Basic user interface after login

3. **CRUD Operations**
   - Single resource: **ToDo items**
   - Create new todos
   - Read/Display todos
   - Update existing todos
   - Delete todos

## Files Structure

```
BareBonesPHP/
│
├── public/                     # Publicly accessible files (DocumentRoot)
│   ├── index.php               # Entry point (handles routing or landing page)
│   ├── login.php               # Login form (view only)
│   ├── register.php            # Signup form (view only)
│   ├── dashboard.php           # Protected page
│   ├── todos/                  # Views for CRUD resource
│   │   ├── index.php           # List all todos
│   │   ├── create.php          # Add new todo
│   │   ├── edit.php            # Edit todo
│   │   └── show.php            # (Optional) view single todo
│   ├── assets/                 # Static files
│   │   ├── css/
│   │   └── js/
│   └── logout.php              # Kills session
│
├── src/                        # Application logic
│   ├── controllers/
│   │   ├── AuthController.php
│   │   └── TodoController.php
│   ├── models/
│   │   ├── User.php
│   │   └── Todo.php
│   └── helpers/
│       └── functions.php       # Common utility funcs (redirect, sanitize, etc.)
│
├── config/
│   ├── database.php            # PDO connection setup
│   └── constants.php           # Site-wide constants
│
├── storage/                    # Non-public storage
│   ├── logs/                   # Error logs
│   └── sessions/               # (Optional custom session storage)
│
├── database.sql                # Schema dump
├── .gitignore
├── README.md
└── composer.json               # Optional
```


## 🎓 Learning Outcomes

By the end of this project, I will understand:

- **Request/Response Cycle**: How HTTP requests are processed without framework routing
- **Session Management**: How PHP sessions and cookies work at the native level
- **Database Interactions**: Raw PDO/MySQLi queries instead of Eloquent ORM
- **Security**: Manual password hashing, input validation, and SQL injection prevention
- **MVC Pattern**: Implementing separation of concerns without framework structure
- **Authentication Flow**: Session-based auth without Laravel's built-in guards

## 🚫 What This Project Does NOT Include

- No Eloquent ORM (raw SQL queries only)
- No Laravel routing (manual URL handling)
- No Blade templating (basic PHP includes)
- No middleware system (manual auth checks)
- No validation classes (manual input validation)
- No complex features (keeping it minimal for learning)

## 🛠 Tech Stack

- **Backend**: Pure PHP 8+
- **Database**: MySQL
- **Frontend**: Basic HTML/CSS (minimal styling)
- **Server**: XAMPP/Apache

## 📚 Why This Approach

Even though I already know Laravel, building from scratch will:

- **Demystify Framework Magic**: See what Laravel does behind the scenes
- **Appreciate Abstractions**: Understand the complexity frameworks handle
- **Solid Foundation**: Better grasp of web fundamentals
- **Debugging Skills**: Know where to look when things break
- **Clear the Fog**: Remove dependency on "framework magic" before advancing

---

> "You can't truly understand something until you've built it from scratch."
