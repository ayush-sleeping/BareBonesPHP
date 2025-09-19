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

## 🔧 Complete Setup & Architecture Guide


### 🚀 Initial Setup

1. **Database Setup**
   ```sql
   -- Run database.sql to create tables
   CREATE DATABASE barebone_php;
   USE barebone_php;
   -- Creates 'users' and 'todos' tables with proper relationships
   ```

2. **Configuration**
   ```php
   // config/database.php - PDO connection
   $pdo = new PDO("mysql:host=localhost;dbname=barebone_php", $username, $password);

   // config/constants.php - Site-wide settings
   define('SITE_URL', 'http://localhost/BareBonesPHP/public/');
   define('PASSWORD_SALT', 'your-secret-salt');
   ```

### 🏗 MVC Architecture Flow

#### **Models Layer** (Data & Business Logic)
```php
// src/models/User.php
- findByUsernameOrEmail() // Login verification
- create() // User registration
- validateRegistration() // Input validation
- verifyPassword() // Password checking

// src/models/Todo.php
- getByUserId() // Fetch user's todos with filtering
- create() // Add new todo
- update() // Modify existing todo
- delete() // Remove todo
- getStatsByUserId() // Dashboard statistics
```

#### **Controllers Layer** (Request Handling)
```php
// src/controllers/AuthController.php
- login() // Process login form
- register() // Process signup form
- logout() // Destroy session
- requireAuth() // Protect routes
- getCurrentUser() // Get logged-in user data

// src/controllers/TodoController.php
- getTodos() // Fetch todos with filtering
- createTodo() // Add new todo
- updateTodo() // Modify todo
- deleteTodo() // Remove todo
- getDashboardData() // Stats for dashboard
```

#### **Views Layer** (Presentation Only)
```php
// Views just display data - no business logic
$authController = new AuthController($pdo);
$currentUser = $authController->getCurrentUser();
// Then just echo htmlspecialchars($currentUser['username'])
```

### 🔐 Authentication Workflow

#### **1. User Registration (signup.php)**
```php
POST /signup.php
├── AuthController->register()
│   ├── User->validateRegistration() // Check input
│   ├── User->findByUsernameOrEmail() // Check duplicates
│   ├── password_hash() // Hash password
│   └── User->create() // Save to database
└── Redirect to login.php
```

#### **2. User Login (login.php)**
```php
POST /login.php
├── AuthController->login()
│   ├── User->findByUsernameOrEmail() // Find user
│   ├── User->verifyPassword() // Check password
│   ├── $_SESSION['user_id'] = $user['id'] // Create session
│   └── $_SESSION['username'] = $user['username']
└── Redirect to dashboard.php
```

#### **3. Route Protection**
```php
// Every protected page calls:
$authController->requireAuth()
├── Check if $_SESSION['user_id'] exists
├── If not: redirect to login.php
└── If yes: continue loading page
```

#### **4. User Logout (logout.php)**
```php
GET /logout.php
├── AuthController->logout()
│   ├── Store username for goodbye message
│   ├── session_unset() // Clear session data
│   ├── session_destroy() // Destroy session
│   └── session_start() // New session for flash message
└── Show logout confirmation page
```

### 🏠 Dashboard Workflow

```php
GET /backend/dashboard.php
├── AuthController->requireAuth() // Ensure logged in
├── AuthController->getCurrentUser() // Get user data
├── TodoController->getDashboardData() // Get stats & recent todos
│   ├── Todo->getStatsByUserId() // Count: total, pending, completed
│   └── Todo->getRecentByUserId() // Last 5 todos
└── Display welcome message + stats + recent todos
```

### 📝 CRUD Operations Workflow

#### **1. List Todos (todos/index.php)**
```php
GET /todos/index.php?filter=all
├── AuthController->requireAuth()
├── TodoController->getTodos($userId, $filter)
│   └── Todo->getByUserId() // SQL: WHERE user_id = ? AND filter
├── TodoController->getTodoCounts($userId)
│   └── Todo->getCountsByUserId() // Count all, pending, completed
└── Display todos list with filter tabs

POST /todos/index.php (Toggle/Delete actions)
├── TodoController->handleAction($action, $todoId, $userId)
│   ├── 'toggle' → Todo->toggleCompletion()
│   └── 'delete' → Todo->delete()
└── Redirect back to index.php
```

#### **2. Create Todo (todos/create.php)**
```php
GET /todos/create.php
└── Show empty form

POST /todos/create.php
├── TodoController->createTodo($userId, $title, $description)
│   ├── Todo->validateTodo() // Check title length, etc.
│   └── Todo->create() // SQL: INSERT INTO todos
├── Flash message: "Todo created successfully!"
└── Redirect to todos/index.php
```

#### **3. Edit Todo (todos/edit.php)**
```php
GET /todos/edit.php?id=123
├── TodoController->getTodo($todoId, $userId)
│   └── Todo->findByIdAndUserId() // Security: only user's todos
└── Show form pre-filled with todo data

POST /todos/edit.php?id=123
├── TodoController->updateTodo($todoId, $userId, $title, $description, $isCompleted)
│   ├── Todo->validateTodo() // Validate input
│   └── Todo->update() // SQL: UPDATE todos SET ... WHERE id = ? AND user_id = ?
├── Flash message: "Todo updated successfully!"
└── Redirect to todos/index.php
```

### 🔒 Security Measures

#### **Input Security**
```php
// All user input is sanitized
$title = trim($_POST['title'] ?? '');
echo htmlspecialchars($title); // XSS prevention

// SQL Injection Prevention
$stmt = $pdo->prepare("SELECT * FROM todos WHERE user_id = ?");
$stmt->execute([$userId]); // Prepared statements
```

#### **Authentication Security**
```php
// Password hashing
password_hash($password, PASSWORD_DEFAULT);
password_verify($inputPassword, $hashedPassword);

// Session security
session_start();
$_SESSION['user_id'] = $user['id']; // Store minimal data
```

#### **Authorization Security**
```php
// Every operation checks ownership
$stmt = $pdo->prepare("SELECT * FROM todos WHERE id = ? AND user_id = ?");
// Users can only access their own data
```

### 🔄 Request Flow Example

**Complete flow for "Mark Todo as Complete":**

1. **User clicks "Mark Complete" button**
   ```html
   <form method="POST">
       <input type="hidden" name="todo_id" value="123">
       <input type="hidden" name="action" value="toggle">
       <button type="submit">Mark Complete</button>
   </form>
   ```

2. **Browser sends POST to todos/index.php**
   ```
   POST /todos/index.php
   Body: todo_id=123&action=toggle
   ```

3. **PHP processes the request**
   ```php
   // todos/index.php
   $authController->requireAuth(); // Check login
   $currentUser = $authController->getCurrentUser(); // Get user

   if ($_POST) {
       $result = $todoController->handleAction('toggle', 123, $currentUser['id']);
   }
   ```

4. **Controller handles the action**
   ```php
   // TodoController->handleAction()
   case 'toggle':
       return $this->toggleTodo(123, $currentUser['id']);
   ```

5. **Model updates database**
   ```php
   // Todo->toggleCompletion()
   UPDATE todos SET is_completed = NOT is_completed
   WHERE id = 123 AND user_id = $currentUser['id']
   ```

6. **Success response**
   ```php
   $_SESSION['todo_success'] = 'Todo status updated!';
   header('Location: index.php'); // Redirect to prevent form resubmission
   ```

7. **User sees updated page**
   - Todo now shows as "Completed"
   - Success message displays
   - Filter counts updated

### 💡 Key Design Decisions

- **Session-based Auth**: Simple, server-side session storage
- **PDO Prepared Statements**: SQL injection prevention
- **MVC Separation**: Models handle data, Controllers handle logic, Views handle display
- **Flash Messages**: Success/error messages stored in session
- **User Ownership**: All operations verify user owns the data
- **Input Validation**: Both client-side (HTML) and server-side (PHP)


---

> "You can't truly understand something until you've built it from scratch."
