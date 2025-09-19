<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/src/models/Todo.php

class Todo {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all todos for a user with optional filtering
     */
    public function getByUserId($userId, $filter = 'all', $limit = null, $offset = 0) {
        try {
            $whereClause = "WHERE user_id = ?";
            $params = [$userId];

            if ($filter === 'pending') {
                $whereClause .= " AND is_completed = 0";
            } elseif ($filter === 'completed') {
                $whereClause .= " AND is_completed = 1";
            }

            $sql = "SELECT id, title, description, is_completed, created_at, updated_at FROM todos $whereClause ORDER BY created_at DESC";

            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Todo getByUserId Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent todos for a user
     */
    public function getRecentByUserId($userId, $limit = 5) {
        return $this->getByUserId($userId, 'all', $limit);
    }

    /**
     * Find todo by ID and user ID (for security)
     */
    public function findByIdAndUserId($id, $userId) {
        try {
            $stmt = $this->db->prepare("SELECT id, title, description, is_completed, created_at, updated_at FROM todos WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Todo findByIdAndUserId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new todo
     */
    public function create($userId, $title, $description = '') {
        try {
            $stmt = $this->db->prepare("INSERT INTO todos (user_id, title, description) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $title, $description]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Todo create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update todo
     */
    public function update($id, $userId, $title, $description = '', $isCompleted = 0) {
        try {
            $stmt = $this->db->prepare("UPDATE todos SET title = ?, description = ?, is_completed = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
            return $stmt->execute([$title, $description, $isCompleted, $id, $userId]);
        } catch (PDOException $e) {
            error_log("Todo update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle todo completion status
     */
    public function toggleCompletion($id, $userId) {
        try {
            $stmt = $this->db->prepare("UPDATE todos SET is_completed = NOT is_completed, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (PDOException $e) {
            error_log("Todo toggleCompletion Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete todo
     */
    public function delete($id, $userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (PDOException $e) {
            error_log("Todo delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get todo statistics for a user
     */
    public function getStatsByUserId($userId) {
        try {
            // Get total todos
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM todos WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();

            // Get completed todos
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM todos WHERE user_id = ? AND is_completed = 1");
            $stmt->execute([$userId]);
            $completed = $stmt->fetchColumn();

            // Get pending todos
            $pending = $total - $completed;

            return [
                'total' => $total,
                'completed' => $completed,
                'pending' => $pending,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0
            ];

        } catch (PDOException $e) {
            error_log("Todo getStatsByUserId Error: " . $e->getMessage());
            return [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'completion_rate' => 0
            ];
        }
    }

    /**
     * Get todo counts by filter for a user
     */
    public function getCountsByUserId($userId) {
        try {
            $stats = $this->getStatsByUserId($userId);

            return [
                'all' => $stats['total'],
                'pending' => $stats['pending'],
                'completed' => $stats['completed']
            ];

        } catch (PDOException $e) {
            error_log("Todo getCountsByUserId Error: " . $e->getMessage());
            return [
                'all' => 0,
                'pending' => 0,
                'completed' => 0
            ];
        }
    }

    /**
     * Mark todo as completed
     */
    public function markCompleted($id, $userId) {
        try {
            $stmt = $this->db->prepare("UPDATE todos SET is_completed = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (PDOException $e) {
            error_log("Todo markCompleted Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark todo as pending
     */
    public function markPending($id, $userId) {
        try {
            $stmt = $this->db->prepare("UPDATE todos SET is_completed = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
            return $stmt->execute([$id, $userId]);
        } catch (PDOException $e) {
            error_log("Todo markPending Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all completed todos for a user
     */
    public function deleteCompletedByUserId($userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM todos WHERE user_id = ? AND is_completed = 1");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Todo deleteCompletedByUserId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search todos by title or description
     */
    public function searchByUserId($userId, $searchTerm, $limit = null) {
        try {
            $searchTerm = "%$searchTerm%";
            $sql = "SELECT id, title, description, is_completed, created_at, updated_at FROM todos WHERE user_id = ? AND (title LIKE ? OR description LIKE ?) ORDER BY created_at DESC";

            if ($limit) {
                $sql .= " LIMIT ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $searchTerm, $searchTerm, $limit]);
            } else {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $searchTerm, $searchTerm]);
            }

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Todo searchByUserId Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Validate todo input
     */
    public function validateTodo($title, $description = '') {
        $errors = [];

        if (empty($title)) {
            $errors[] = 'Title is required.';
        } elseif (strlen($title) > 255) {
            $errors[] = 'Title must be less than 255 characters.';
        }

        if (strlen($description) > 1000) {
            $errors[] = 'Description must be less than 1000 characters.';
        }

        return $errors;
    }

    /**
     * Get todos with pagination
     */
    public function getPaginatedByUserId($userId, $page = 1, $perPage = 10, $filter = 'all') {
        $offset = ($page - 1) * $perPage;

        $todos = $this->getByUserId($userId, $filter, $perPage, $offset);
        $counts = $this->getCountsByUserId($userId);

        $totalCount = $counts[$filter] ?? 0;
        $totalPages = ceil($totalCount / $perPage);

        return [
            'todos' => $todos,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_count' => $totalCount,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
    }
}
