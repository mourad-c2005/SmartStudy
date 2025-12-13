<?php
// app/core/Model.php - UPDATED WITH BETTER ERROR HANDLING

// First, load Database class if not already loaded
if (!class_exists('Database')) {
    require_once __DIR__ . '/Database.php';
}

class Model {
    protected $db;
    protected $table; // Optional: for automatic table operations

    public function __construct() {
        try {
            $this->db = Database::getInstance();
        } catch (Exception $e) {
            die("Failed to get database instance: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            // Better error message with context
            $errorMsg = "Query failed: " . $e->getMessage() . "\n";
            $errorMsg .= "SQL: " . $sql . "\n";
            $errorMsg .= "Params: " . print_r($params, true);
            
            error_log($errorMsg);
            
            // For development, show detailed error
            if (defined('DEBUG') && DEBUG) {
                die($errorMsg);
            }
            
            throw $e;
        }
    }
    
    // Optional helper methods
    public function findAll($conditions = [], $orderBy = 'id DESC') {
        $sql = "SELECT * FROM " . $this->getTableName();
        
        if (!empty($conditions)) {
            $where = [];
            $params = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY " . $orderBy;
        
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function findById($id) {
        return $this->query("SELECT * FROM " . $this->getTableName() . " WHERE id = ?", [$id])->fetch();
    }
    
    protected function getTableName() {
        if ($this->table) {
            return $this->table;
        }
        
        // Auto-detect table name from class name
        $className = get_class($this);
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
        return $tableName . 's'; // Pluralize
    }
}