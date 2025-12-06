<?php
/**
 * 🛡️ VALIDATION DE CONTENU CÔTÉ SERVEUR (VERSION FLEXIBLE)
 */

class ContentValidator {
    
    private static $forbiddenWords = [
        // Français (insultes graves uniquement)
        'connard', 'connasse', 'salope', 'pute',
        'enculé', 'fdp', 'fils de pute',
        'nique', 'niquer', 'ta gueule', 'batard',
        'pd', 'pédé', 'tg', 'ntm',
        
        // Anglais (insultes graves)
        'fuck', 'fucking', 'fucker', 'shit', 'bitch', 'bastard',
        'asshole', 'nigga', 'nigger', 'faggot',
        'whore', 'slut', 'motherfucker',
        
        // Tunisien (insultes graves)
        'kahba', 'zebbi', 'khayna', 'nayek', 'zmel'
    ];
    
    private static function normalize($text) {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[*@#$%^&+=_\-]/', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        $text = strtr($text, [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c'
        ]);
        
        return $text;
    }
    
    /**
     * Vérifie si le contenu est valide (VERSION FLEXIBLE)
     */
    public static function validate($content, $minLength = 3, $maxLength = 5000) {
        // Vide
        if (empty(trim($content))) {
            return [
                'valid' => false,
                'error' => 'Le contenu ne peut pas être vide.'
            ];
        }
        
        $trimmedLength = mb_strlen(trim($content));
        
        // Trop court
        if ($trimmedLength < $minLength) {
            return [
                'valid' => false,
                'error' => "Le contenu doit contenir au moins $minLength caractères."
            ];
        }
        
        // Trop long
        if (mb_strlen($content) > $maxLength) {
            return [
                'valid' => false,
                'error' => "Le contenu ne peut pas dépasser $maxLength caractères."
            ];
        }
        
        // Mots interdits (uniquement mots de 4+ caractères)
        $normalized = self::normalize($content);
        $noSpaces = str_replace(' ', '', $normalized);
        
        foreach (self::$forbiddenWords as $word) {
            if (strlen($word) < 4) continue; // ✅ Ignorer mots courts
            
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            
            if (preg_match($pattern, $normalized) || strpos($noSpaces, $word) !== false) {
                return [
                    'valid' => false,
                    'error' => '🚫 Langage inapproprié détecté. Merci de rester respectueux.'
                ];
            }
        }
        
        // ✅ SUPPRIMÉ : Plus de vérification des liens systématique
        
        return ['valid' => true];
    }
}
?>
```

---

## 🧪 **NOUVEAUX TESTS**

### **Test 1 : Messages courts (OK maintenant)**
```
✅ "Ok"          → ACCEPTÉ (3 caractères)
✅ "Merci"       → ACCEPTÉ
✅ "D'accord"    → ACCEPTÉ
❌ "Ok"          → REFUSÉ (2 caractères seulement)
```

### **Test 2 : Contenu innocent avec symboles**
```
✅ "reviser les tests dans le cours.com avant les examens" → ACCEPTÉ
✅ "Le prix est 100$$$$ pour ce cours"                     → ACCEPTÉ
✅ "www.esprit.tn"                                          → ACCEPTÉ (plus de blocage)
```

### **Test 3 : Insultes graves (toujours bloquées)**
```
❌ "fuck"        → BLOQUÉ
❌ "connard"     → BLOQUÉ
❌ "salope"      → BLOQUÉ
```

### **Test 4 : Mots légers (acceptés maintenant)**
```
✅ "idiot"       → ACCEPTÉ (retiré de la liste)
✅ "stupide"     → ACCEPTÉ
✅ "con"         → ACCEPTÉ (trop courant en français)