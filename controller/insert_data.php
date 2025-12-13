<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';

echo "<pre>POST data received:\n";
print_r($_POST);
echo "</pre>";

if (isset($_POST['add_user']) && $_POST['add_user'] == "1" && $_POST['js_validation'] == "1") {
    echo "add_user field: OK<br>";

    try {
        
        $userModel = new User($pdo);
        
        
        $userData = [
            'nom' => trim($_POST["nom"]),
            'email' => trim($_POST["email"]), 
            'role' => $_POST["role"],
            'password' => $_POST["password"]  
        ];
        
        echo "Données préparées pour le modèle:<br>";
        print_r($userData);
        
       
        $result = $userModel->create($userData);
        
        if ($result) {
            echo " SUCCÈS - Utilisateur créé avec le modèle User (PDO)<br>";
            header("Location: ../view/back_office/user.php?insert_msg=Utilisateur ajouté avec succès !");
            exit();
        } else {
            echo " ÉCHEC - La méthode create() a retourné false<br>";
           
            error_log("User::create() returned false for data: " . json_encode($userData));
        }
        
    } catch (Exception $e) {
        echo " ERREUR PDO: " . $e->getMessage() . "<br>";
        error_log("PDO Error in insert_data.php: " . $e->getMessage());
    }
} else {
    echo " Validation échouée - add_user: " . ($_POST['add_user'] ?? 'non défini') . 
         ", js_validation: " . ($_POST['js_validation'] ?? 'non défini') . "<br>";
}
?>