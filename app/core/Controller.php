<?php
// app/core/Controller.php
class Controller {
    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }

    public function view($viewPath, $data = []) {
        // $viewPath like 'matieres/index'
        $file = 'app/views/' . $viewPath . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            echo "Vue introuvable: " . $file;
        }
    }
}
