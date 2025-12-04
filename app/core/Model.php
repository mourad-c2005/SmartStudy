<?php
// app/core/Model.php
class Model {
    protected $db;
    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
    }
}
