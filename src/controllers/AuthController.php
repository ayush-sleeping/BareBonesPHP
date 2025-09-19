<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/src/controllers/AuthController.php

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    private $db;

    public function __construct($database) {
        $this->db = $database;
        $this->userModel = new User($database);
    }

    /**
     * Handle user login
     */
    public function login($username, $password) {
        // Validate input
        $errors = $this->userModel->validateLogin($username, $password);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Find user
        $user = $this->userModel->findByUsernameOrEmail($username);

        if (!$user) {
            return [
                'success' => false,
                'errors' => ['Invalid username/email or password.']
            ];
        }

        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return [
                'success' => false,
                'errors' => ['Invalid username/email or password.']
            ];
        }

        // Login successful - create session
        $this->createUserSession($user);

        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ];
    }

    /**
     * Handle user registration
     */
    public function register($username, $email, $password, $confirmPassword) {
        // Validate input
        $errors = $this->userModel->validateRegistration($username, $email, $password, $confirmPassword);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Create user
        $userId = $this->userModel->create($username, $email, $password);

        if (!$userId) {
            return [
                'success' => false,
                'errors' => ['An error occurred while creating your account. Please try again.']
            ];
        }

        return [
            'success' => true,
            'user_id' => $userId,
            'message' => 'Account created successfully!'
        ];
    }

    /**
     * Handle user logout
     */
    public function logout() {
        // Store username for display before destroying session
        $username = $_SESSION['username'] ?? 'User';
        $wasLoggedIn = isset($_SESSION['user_id']);

        // Destroy session
        session_unset();
        session_destroy();

        // Start new session for logout message
        session_start();

        return [
            'success' => true,
            'was_logged_in' => $wasLoggedIn,
            'username' => $username
        ];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];
    }

    /**
     * Require authentication (redirect if not logged in)
     */
    public function requireAuth($redirectPath = 'login.php') {
        if (!$this->isLoggedIn()) {
            header("Location: $redirectPath");
            exit;
        }
    }

    /**
     * Redirect if already logged in
     */
    public function redirectIfLoggedIn($redirectPath = 'backend/dashboard.php') {
        if ($this->isLoggedIn()) {
            header("Location: $redirectPath");
            exit;
        }
    }

    /**
     * Create user session
     */
    private function createUserSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        // Only allow updating specific fields
        $allowedFields = ['username', 'email'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return [
                'success' => false,
                'errors' => ['No valid fields to update.']
            ];
        }

        // Validate username if being updated
        if (isset($updateData['username'])) {
            if (strlen($updateData['username']) < 3 || strlen($updateData['username']) > 50) {
                return [
                    'success' => false,
                    'errors' => ['Username must be between 3 and 50 characters.']
                ];
            }

            // Check if username is taken by another user
            $existingUser = $this->userModel->findByUsernameOrEmail($updateData['username']);
            if ($existingUser && $existingUser['id'] != $userId) {
                return [
                    'success' => false,
                    'errors' => ['Username already taken.']
                ];
            }
        }

        // Validate email if being updated
        if (isset($updateData['email'])) {
            if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'errors' => ['Please enter a valid email address.']
                ];
            }

            // Check if email is taken by another user
            $existingUser = $this->userModel->findByUsernameOrEmail($updateData['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                return [
                    'success' => false,
                    'errors' => ['Email already registered.']
                ];
            }
        }

        // Update user
        $result = $this->userModel->update($userId, $updateData);

        if ($result) {
            // Update session data if needed
            if (isset($updateData['username'])) {
                $_SESSION['username'] = $updateData['username'];
            }
            if (isset($updateData['email'])) {
                $_SESSION['email'] = $updateData['email'];
            }

            return [
                'success' => true,
                'message' => 'Profile updated successfully!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['Failed to update profile. Please try again.']
        ];
    }

    /**
     * Change user password
     */
    public function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        // Get user data
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return [
                'success' => false,
                'errors' => ['User not found.']
            ];
        }

        // Get full user data including password
        $userWithPassword = $this->userModel->findByUsernameOrEmail($user['username']);

        // Verify current password
        if (!$this->userModel->verifyPassword($currentPassword, $userWithPassword['password'])) {
            return [
                'success' => false,
                'errors' => ['Current password is incorrect.']
            ];
        }

        // Validate new password
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return [
                'success' => false,
                'errors' => ['New password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.']
            ];
        }

        if ($newPassword !== $confirmPassword) {
            return [
                'success' => false,
                'errors' => ['New passwords do not match.']
            ];
        }

        // Update password
        $result = $this->userModel->update($userId, ['password' => $newPassword]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Password changed successfully!'
            ];
        }

        return [
            'success' => false,
            'errors' => ['Failed to change password. Please try again.']
        ];
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        return $this->userModel->findById($userId);
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
