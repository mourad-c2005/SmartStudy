<?php
// controller/CookieController.php
class CookieController {
    
    /**
     * Définir un cookie
     */
    public static function set($name, $value, $expire = 86400 * 30) { // 30 jours par défaut
        if (!headers_sent()) {
            setcookie($name, $value, time() + $expire, '/', '', false, true); // HttpOnly & Secure
            $_COOKIE[$name] = $value; // Mettre à jour le tableau global immédiatement
            return true;
        }
        return false;
    }
    
    /**
     * Récupérer un cookie
     */
    public static function get($name, $default = null) {
        return $_COOKIE[$name] ?? $default;
    }
    
    /**
     * Vérifier si un cookie existe
     */
    public static function has($name) {
        return isset($_COOKIE[$name]);
    }
    
    /**
     * Supprimer un cookie
     */
    public static function delete($name) {
        if (self::has($name)) {
            setcookie($name, '', time() - 3600, '/', '', false, true);
            unset($_COOKIE[$name]);
            return true;
        }
        return false;
    }
    
    /**
     * Cookie pour se souvenir de l'utilisateur
     */
    public static function rememberUser($userId, $username) {
        $data = [
            'user_id' => $userId,
            'username' => $username,
            'remember_token' => bin2hex(random_bytes(32))
        ];
        
        // Stocker dans la base de données
        require_once 'config/database.php';
        require_once 'model/User.php';
        
        $pdo = new PDO("mysql:host=localhost;dbname=smartstudy;charset=utf8", "root", "");
        $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmt->execute([$data['remember_token'], $userId]);
        
        // Créer le cookie
        self::set('remember_me', json_encode($data), 86400 * 30); // 30 jours
    }
    
    /**
     * Vérifier le cookie "remember me"
     */
    public static function checkRememberMe() {
        if (self::has('remember_me') && !isset($_SESSION['user'])) {
            $data = json_decode(self::get('remember_me'), true);
            
            if ($data && isset($data['user_id'], $data['remember_token'])) {
                require_once 'config/database.php';
                require_once 'model/User.php';
                
                $pdo = new PDO("mysql:host=localhost;dbname=smartstudy;charset=utf8", "root", "");
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND remember_token = ? AND autorisation = 1");
                $stmt->execute([$data['user_id'], $data['remember_token']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Cookie pour le thème (dark/light mode)
     */
    public static function setTheme($theme) {
        self::set('theme', $theme);
    }
    
    /**
     * Récupérer le thème
     */
    public static function getTheme($default = 'light') {
        return self::get('theme', $default);
    }
    
    /**
     * Cookie pour la langue
     */
    public static function setLanguage($lang) {
        self::set('language', $lang);
    }
    
    /**
     * Récupérer la langue
     */
    public static function getLanguage($default = 'fr') {
        return self::get('language', $default);
    }
    
    /**
     * Accepter les cookies
     */
    public static function acceptCookies() {
        self::set('cookies_accepted', 'true', 86400 * 5); // 5jours
    }
    
    /**
     * Vérifier si les cookies sont acceptés
     */
    public static function areCookiesAccepted() {
        return self::get('cookies_accepted') === 'true';
    }
}