<?php
class Controller {
    // Load model
    public function model($model) {
        // Try multiple possible model locations
        $possiblePaths = [
            "model/{$model}.php",
            "models/{$model}.php",
            "app/model/{$model}.php",
            "app/models/{$model}.php"
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return new $model();
            }
        }
        
        // If model not found
        die("Model '$model' not found. Tried paths: " . implode(', ', $possiblePaths));
    }
    
    // Load view
    public function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Try multiple possible view locations
        $possiblePaths = [
            "view/{$view}.php",
            "views/{$view}.php",
            "view/{$view}.php",
            "views/{$view}.php"
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                return;
            }
        }
        
        // If view not found
        die("View '$view' not found. Tried paths: " . implode(', ', $possiblePaths));
    }
}