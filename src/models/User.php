<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/src/models/User.php

class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail($identifier) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$identifier, $identifier]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User findByUsernameOrEmail Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find user by ID
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User findById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("User usernameExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email exists
     */
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("User emailExists Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new user
     */
    public function create($username, $email, $password) {
        try {
            $hashedPassword = password_hash($password, HASH_ALGO);
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify user password
     */
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    /**
     * Update user information
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $values = [];

            foreach ($data as $key => $value) {
                if (in_array($key, ['username', 'email', 'password'])) {
                    if ($key === 'password') {
                        $value = password_hash($value, HASH_ALGO);
                    }
                    $fields[] = "$key = ?";
                    $values[] = $value;
                }
            }

            if (empty($fields)) {
                return false;
            }

            $values[] = $id;
            $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);

        } catch (PDOException $e) {
            error_log("User update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("User delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all users (for admin purposes)
     */
    public function getAll($limit = null, $offset = 0) {
        try {
            $sql = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC";

            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$limit, $offset]);
            } else {
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
            }

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("User getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user count
     */
    public function getCount() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("User getCount Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Validate user input
     */
    public function validateRegistration($username, $email, $password, $confirmPassword) {
        $errors = [];

        // Username validation
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters long.';
        } elseif (strlen($username) > 50) {
            $errors[] = 'Username must be less than 50 characters.';
        } elseif ($this->usernameExists($username)) {
            $errors[] = 'Username already exists. Please choose a different one.';
        }

        // Email validation
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif ($this->emailExists($email)) {
            $errors[] = 'Email already registered. Please use a different email.';
        }

        // Password validation
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
        }

        // Confirm password validation
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        return $errors;
    }

    /**
     * Validate login input
     */
    public function validateLogin($username, $password) {
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username or email is required.';
        }

        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        return $errors;
    }
}
