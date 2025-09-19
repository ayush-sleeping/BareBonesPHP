<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/src/controllers/TodoController.php

require_once __DIR__ . '/../models/Todo.php';

class TodoController {
    private $todoModel;
    private $db;

    public function __construct($database) {
        $this->db = $database;
        $this->todoModel = new Todo($database);
    }

    /**
     * Get all todos for a user with filtering
     */
    public function getTodos($userId, $filter = 'all', $page = 1, $perPage = 10) {
        if ($perPage > 0) {
            return $this->todoModel->getPaginatedByUserId($userId, $page, $perPage, $filter);
        } else {
            $todos = $this->todoModel->getByUserId($userId, $filter);
            return [
                'todos' => $todos,
                'pagination' => null
            ];
        }
    }

    /**
     * Get recent todos for dashboard
     */
    public function getRecentTodos($userId, $limit = 5) {
        return $this->todoModel->getRecentByUserId($userId, $limit);
    }

    /**
     * Get todo statistics for dashboard
     */
    public function getTodoStats($userId) {
        return $this->todoModel->getStatsByUserId($userId);
    }

    /**
     * Get todo counts for filters
     */
    public function getTodoCounts($userId) {
        return $this->todoModel->getCountsByUserId($userId);
    }

    /**
     * Get single todo by ID (with user security check)
     */
    public function getTodo($todoId, $userId) {
        $todo = $this->todoModel->findByIdAndUserId($todoId, $userId);

        if (!$todo) {
            return [
                'success' => false,
                'error' => 'Todo not found or you do not have permission to access it.'
            ];
        }

        return [
            'success' => true,
            'todo' => $todo
        ];
    }

    /**
     * Create new todo
     */
    public function createTodo($userId, $title, $description = '') {
        // Validate input
        $errors = $this->todoModel->validateTodo($title, $description);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Create todo
        $todoId = $this->todoModel->create($userId, $title, $description);

        if ($todoId) {
            return [
                'success' => true,
                'todo_id' => $todoId,
                'message' => 'Todo created successfully!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while creating the todo. Please try again.']
        ];
    }

    /**
     * Update existing todo
     */
    public function updateTodo($todoId, $userId, $title, $description = '', $isCompleted = 0) {
        // Check if todo exists and belongs to user
        $todoCheck = $this->getTodo($todoId, $userId);
        if (!$todoCheck['success']) {
            return $todoCheck;
        }

        // Validate input
        $errors = $this->todoModel->validateTodo($title, $description);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Update todo
        $result = $this->todoModel->update($todoId, $userId, $title, $description, $isCompleted);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Todo updated successfully!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while updating the todo. Please try again.']
        ];
    }

    /**
     * Delete todo
     */
    public function deleteTodo($todoId, $userId) {
        // Check if todo exists and belongs to user
        $todoCheck = $this->getTodo($todoId, $userId);
        if (!$todoCheck['success']) {
            return $todoCheck;
        }

        // Delete todo
        $result = $this->todoModel->delete($todoId, $userId);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Todo deleted successfully!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while deleting the todo. Please try again.']
        ];
    }

    /**
     * Toggle todo completion status
     */
    public function toggleTodo($todoId, $userId) {
        // Check if todo exists and belongs to user
        $todoCheck = $this->getTodo($todoId, $userId);
        if (!$todoCheck['success']) {
            return $todoCheck;
        }

        // Toggle completion
        $result = $this->todoModel->toggleCompletion($todoId, $userId);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Todo status updated successfully!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while updating the todo. Please try again.']
        ];
    }

    /**
     * Mark todo as completed
     */
    public function completeTodo($todoId, $userId) {
        // Check if todo exists and belongs to user
        $todoCheck = $this->getTodo($todoId, $userId);
        if (!$todoCheck['success']) {
            return $todoCheck;
        }

        // Mark as completed
        $result = $this->todoModel->markCompleted($todoId, $userId);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Todo marked as completed!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while updating the todo. Please try again.']
        ];
    }

    /**
     * Mark todo as pending
     */
    public function pendTodo($todoId, $userId) {
        // Check if todo exists and belongs to user
        $todoCheck = $this->getTodo($todoId, $userId);
        if (!$todoCheck['success']) {
            return $todoCheck;
        }

        // Mark as pending
        $result = $this->todoModel->markPending($todoId, $userId);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Todo marked as pending!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while updating the todo. Please try again.']
        ];
    }

    /**
     * Search todos
     */
    public function searchTodos($userId, $searchTerm, $limit = null) {
        if (empty($searchTerm)) {
            return [
                'success' => false,
                'errors' => ['Search term is required.']
            ];
        }

        $todos = $this->todoModel->searchByUserId($userId, $searchTerm, $limit);

        return [
            'success' => true,
            'todos' => $todos,
            'search_term' => $searchTerm
        ];
    }

    /**
     * Delete all completed todos
     */
    public function deleteCompletedTodos($userId) {
        // Get count of completed todos first
        $stats = $this->getTodoStats($userId);
        $completedCount = $stats['completed'];

        if ($completedCount === 0) {
            return [
                'success' => false,
                'errors' => ['No completed todos to delete.']
            ];
        }

        // Delete completed todos
        $result = $this->todoModel->deleteCompletedByUserId($userId);

        if ($result) {
            return [
                'success' => true,
                'message' => "Deleted $completedCount completed todo(s)!"
            ];
        }

        return [
            'success' => false,
            'errors' => ['An error occurred while deleting completed todos. Please try again.']
        ];
    }

    /**
     * Handle todo actions (toggle, delete, etc.)
     */
    public function handleAction($action, $todoId, $userId) {
        switch ($action) {
            case 'toggle':
                return $this->toggleTodo($todoId, $userId);

            case 'delete':
                return $this->deleteTodo($todoId, $userId);

            case 'complete':
                return $this->completeTodo($todoId, $userId);

            case 'pending':
                return $this->pendTodo($todoId, $userId);

            default:
                return [
                    'success' => false,
                    'errors' => ['Invalid action specified.']
                ];
        }
    }

    /**
     * Get dashboard data
     */
    public function getDashboardData($userId) {
        $stats = $this->getTodoStats($userId);
        $recentTodos = $this->getRecentTodos($userId, 5);

        return [
            'stats' => $stats,
            'recent_todos' => $recentTodos
        ];
    }

    /**
     * Get todos index data
     */
    public function getIndexData($userId, $filter = 'all', $page = 1, $perPage = 0) {
        $todos = $this->getTodos($userId, $filter, $page, $perPage);
        $counts = $this->getTodoCounts($userId);

        return [
            'todos' => $todos['todos'],
            'pagination' => $todos['pagination'],
            'counts' => $counts,
            'current_filter' => $filter
        ];
    }

    /**
     * Handle the complete index page logic - POST actions, data retrieval, flash messages
     */
    public function handleIndexPage($userId) {
        $result = [
            'success' => true,
            'redirect' => null,
            'error' => null,
            'success_message' => null,
            'todos' => [],
            'counts' => [],
            'filter' => 'all',
            'valid_filters' => ['all', 'pending', 'completed']
        ];

        // Handle POST actions first
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $todo_id = (int)($_POST['todo_id'] ?? 0);

            if ($todo_id > 0) {
                $actionResult = $this->handleAction($action, $todo_id, $userId);

                if ($actionResult['success']) {
                    // Set success message and redirect
                    $this->setFlashMessage('todo_success', $actionResult['message'] ?? 'Action completed successfully!');
                    $result['redirect'] = 'index.php';
                    return $result;
                } else {
                    $result['error'] = $actionResult['error'] ?? 'An error occurred. Please try again.';
                }
            }
        }

        // Get filter parameter
        $filter = $_GET['filter'] ?? 'all';
        if (!in_array($filter, $result['valid_filters'])) {
            $filter = 'all';
        }
        $result['filter'] = $filter;

        // Get flash messages
        $result['error'] = $result['error'] ?: $this->getFlashMessage('todo_error');
        $result['success_message'] = $this->getFlashMessage('todo_success');

        // Get todos and counts
        $indexData = $this->getIndexData($userId, $filter);
        $result['todos'] = $indexData['todos'];
        $result['counts'] = $indexData['counts'];

        return $result;
    }

    /**
     * Handle the complete create page logic - POST submission, validation, creation
     */
    public function handleCreatePage($userId) {
        $result = [
            'success' => true,
            'redirect' => null,
            'error' => null,
            'form_data' => [
                'title' => '',
                'description' => ''
            ]
        ];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Store form data for redisplay in case of error
            $result['form_data'] = [
                'title' => $title,
                'description' => $description
            ];

            $createResult = $this->createTodo($userId, $title, $description);

            if ($createResult['success']) {
                $this->setFlashMessage('todo_success', 'Todo created successfully!');
                $result['redirect'] = 'index.php';
                return $result;
            } else {
                $result['error'] = $createResult['error'] ?? 'An error occurred while creating the todo. Please try again.';
            }
        }

        return $result;
    }

    /**
     * Handle the complete edit page logic - fetching, POST submission, validation, updating
     */
    public function handleEditPage($userId, $todoId) {
        $result = [
            'success' => true,
            'redirect' => null,
            'error' => null,
            'todo' => []
        ];

        // Get todo data first
        $todoResult = $this->getTodo($todoId, $userId);

        if (!$todoResult['success']) {
            $this->setFlashMessage('todo_error', $todoResult['error'] ?? 'Todo not found or you do not have permission to edit it.');
            $result['redirect'] = 'index.php';
            return $result;
        }

        $result['todo'] = $todoResult['todo'];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $is_completed = isset($_POST['is_completed']) ? 1 : 0;

            $updateResult = $this->updateTodo($todoId, $userId, $title, $description, $is_completed);

            if ($updateResult['success']) {
                $this->setFlashMessage('todo_success', 'Todo updated successfully!');
                $result['redirect'] = 'index.php';
                return $result;
            } else {
                $result['error'] = $updateResult['error'] ?? 'An error occurred while updating the todo. Please try again.';

                // Update todo data with form values for redisplay
                $result['todo']['title'] = $title;
                $result['todo']['description'] = $description;
                $result['todo']['is_completed'] = $is_completed;
            }
        }

        return $result;
    }

    /**
     * Set flash message
     */
    public function setFlashMessage($key, $message) {
        $_SESSION[$key] = $message;
    }

    /**
     * Get and clear flash message
     */
    public function getFlashMessage($key) {
        if (isset($_SESSION[$key])) {
            $message = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $message;
        }
        return null;
    }
}
