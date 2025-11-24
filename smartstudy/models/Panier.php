<?php
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/formation.php";

class Panier {

    /**
     * Get current user ID from session
     */
    private static function getUserId() {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            // If no user logged in, return null (cart won't work without login)
            return null;
        }
        return intval($_SESSION['user_id']);
    }

    /**
     * Add formation to user's cart
     */
    public static function addFormation($formationId) {
        $userId = self::getUserId();
        
        if ($userId === null) {
            // User not logged in - redirect to login
            error_log("Panier::addFormation - User not logged in");
            return false;
        }
        
        $formationId = intval($formationId);
        
        if ($formationId <= 0) {
            error_log("Panier::addFormation - Invalid formation ID: " . $formationId);
            return false;
        }
        
        try {
            $pdo = Database::connect();
            
            // Check if panier table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
            
            if ($tableExists) {
                // Check if already in cart
                $stmt = $pdo->prepare("SELECT id FROM panier WHERE user_id = ? AND formation_id = ?");
                $stmt->execute([$userId, $formationId]);
                
                if ($stmt->fetch()) {
                    error_log("Panier::addFormation - Formation already in cart: user_id=$userId, formation_id=$formationId");
                    return false; // Already in cart
                }
                
                // Add to cart
                $stmt = $pdo->prepare("INSERT INTO panier (user_id, formation_id) VALUES (?, ?)");
                $result = $stmt->execute([$userId, $formationId]);
                
                if ($result) {
                    error_log("Panier::addFormation - Successfully added: user_id=$userId, formation_id=$formationId");
                } else {
                    error_log("Panier::addFormation - Failed to insert: user_id=$userId, formation_id=$formationId");
                }
                
                return $result;
            } else {
                error_log("Panier::addFormation - Table 'panier' does not exist, using session fallback");
            }
        } catch (PDOException $e) {
            error_log("Panier::addFormation - Database error: " . $e->getMessage());
            // Fallback to session if table doesn't exist
        }
        
        // Fallback: use session-based cart
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        if (!in_array($formationId, $_SESSION['panier'])) {
            $_SESSION['panier'][] = $formationId;
            error_log("Panier::addFormation - Added to session cart: formation_id=$formationId");
        }
        return true;
    }

    /**
     * Get all items in user's cart
     */
    public static function getItems() {
        $userId = self::getUserId();
        
        if ($userId === null) {
            error_log("Panier::getItems - User not logged in");
            return [];
        }
        
        error_log("Panier::getItems - User ID: " . $userId);
        
        try {
            $pdo = Database::connect();
            
            // Check if panier table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
            
            if ($tableExists) {
                error_log("Panier::getItems - Table 'panier' exists");
                $stmt = $pdo->prepare("SELECT formation_id FROM panier WHERE user_id = ?");
                $stmt->execute([$userId]);
                $formationIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                error_log("Panier::getItems - Found " . count($formationIds) . " formation IDs in database");
                
                $items = [];
                foreach ($formationIds as $id) {
                    if (empty($id)) {
                        error_log("Panier::getItems - Empty formation ID found");
                        continue;
                    }
                    
                    $formation = Formation::getById($id);
                    if ($formation === false || empty($formation)) {
                        error_log("Panier::getItems - Formation not found: ID=" . $id);
                        continue;
                    }
                    
                    if (!empty($formation['titre'])) {
                        $items[] = $formation;
                        error_log("Panier::getItems - Added formation: ID=" . $id . ", Titre=" . $formation['titre']);
                    } else {
                        error_log("Panier::getItems - Formation has no titre: ID=" . $id);
                    }
                }
                
                error_log("Panier::getItems - Returning " . count($items) . " items from database");
                return $items;
            } else {
                error_log("Panier::getItems - Table 'panier' does not exist, using session fallback");
            }
        } catch (PDOException $e) {
            error_log("Panier::getItems - Database error: " . $e->getMessage());
            // Fallback to session
        }
        
        // Fallback to session-based cart
        if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
            error_log("Panier::getItems - No items in session cart");
            return [];
        }

        error_log("Panier::getItems - Using session cart with " . count($_SESSION['panier']) . " items");
        $items = [];
        foreach ($_SESSION['panier'] as $id) {
            if (empty($id)) {
                continue;
            }
            
            $formation = Formation::getById($id);
            if ($formation !== false && !empty($formation) && !empty($formation['titre'])) {
                $items[] = $formation;
            }
        }
        
        error_log("Panier::getItems - Returning " . count($items) . " items from session");
        return $items;
    }

    /**
     * Remove formation from cart
     */
    public static function removeFormation($formationId) {
        $userId = self::getUserId();
        
        if ($userId === null) {
            return false;
        }
        
        $formationId = intval($formationId);
        
        try {
            $pdo = Database::connect();
            $tableExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
            
            if ($tableExists) {
                $stmt = $pdo->prepare("DELETE FROM panier WHERE user_id = ? AND formation_id = ?");
                $stmt->execute([$userId, $formationId]);
                return true;
            }
        } catch (PDOException $e) {
            // Fallback to session
        }
        
        // Fallback to session
        if (isset($_SESSION['panier'])) {
            $_SESSION['panier'] = array_filter($_SESSION['panier'], function($id) use ($formationId) {
                return $id != $formationId;
            });
        }
        return true;
    }

    /**
     * Clear user's cart
     */
    public static function clear() {
        $userId = self::getUserId();
        
        if ($userId === null) {
            return;
        }
        
        try {
            $pdo = Database::connect();
            $tableExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
            
            if ($tableExists) {
                $stmt = $pdo->prepare("DELETE FROM panier WHERE user_id = ?");
                $stmt->execute([$userId]);
            }
        } catch (PDOException $e) {
            // Fallback to session
        }
        
        // Also clear session cart
        $_SESSION['panier'] = [];
    }

    /**
     * Get cart count
     */
    public static function getCount() {
        $items = self::getItems();
        return count($items);
    }
}
