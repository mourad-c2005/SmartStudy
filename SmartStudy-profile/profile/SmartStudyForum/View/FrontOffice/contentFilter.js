/**
 *  SYSTÈME DE FILTRAGE DE CONTENU INAPPROPRIÉ
 * SmartStudy+ Forum - Content Moderation
 * 
 * Bloque automatiquement les messages contenant :
 * - Insultes et vulgarités
 * - Discours haineux
 * - Spam excessif
 */

// ========================================
//  LISTE DES MOTS INTERDITS
// ========================================

// ========================================
//  LISTE DES MOTS INTERDITS (VERSION AMÉLIORÉE)
// ========================================

const FORBIDDEN_WORDS = {
    // Insultes courantes (Français)
    french: [
        'connard', 'connasse', 'salope', 'pute',
        'merde', 'chier', 'enculé', 'enculer', 'fdp', 'fils de pute',
        'ta gueule', 'ferme ta gueule', 'nique', 'niquer',
        'ta mere', 'ta mère', 'batard', 'bâtard', 'casse toi',
        'pd', 'pédé', 'tapette', 'tg', 'ntm', 'vas te faire',
        'salaud', 'pourriture', 'ordure', 'déchet',
        // Retiré : 'con', 'putain' (trop courants en français)
        // Retiré : 'idiot', 'imbécile', 'crétin', 'débile', 'abruti' (trop légers)
    ],
    
    // Insultes courantes (Anglais)
    english: [
        'fuck', 'fucking', 'fucker', 'fck', 'f*ck', 'f**k',
        'shit', 'shitty', 'bullshit', 'bitch', 'bastard',
        'asshole', 'damn it', 'goddamn',
        'nigga', 'nigger', 'negro', 'faggot', 'fag',
        'whore', 'slut', 'dickhead', 
        'motherfucker', 'stfu', 'gtfo',
        // Retiré : 'ass', 'damn', 'hell', 'crap' (trop courants)
        // Retiré : 'idiot', 'stupid', 'dumb', 'moron' (trop légers)
    ],
    
    // Dialecte tunisien (insultes graves uniquement)
    tunisian: [
        'kahba', 'zebbi', 'za9alouz',
        'khayna', 'nayek', 'zmel'
        // Retiré : 'ya7mar', 'kalb', 'khra', 'hmar' (trop courants)
    ]
};

// ========================================
// 🔍 FONCTIONS DE DÉTECTION
// ========================================

/**
 * Normalise le texte pour détecter les variations
 * Exemples : f*ck → fuck, fùck → fuck, F U C K → fuck
 */
function normalizeText(text) {
    return text
        .toLowerCase()
        .replace(/[*@#$%^&+=_\-]/g, '') // Supprimer caractères spéciaux
        .replace(/[àáâãäå]/g, 'a')
        .replace(/[èéêë]/g, 'e')
        .replace(/[ìíîï]/g, 'i')
        .replace(/[òóôõö]/g, 'o')
        .replace(/[ùúûü]/g, 'u')
        .replace(/[ç]/g, 'c')
        .replace(/\s+/g, ' ') // Espaces multiples → 1 espace
        .replace(/(.)\1{2,}/g, '$1$1') // Répétitions : aaaa → aa
        .trim();
}

/**
 * Retire tous les espaces (pour détecter "f u c k")
 */
function removeAllSpaces(text) {
    return text.replace(/\s+/g, '');
}

/**
 * Vérifie si le texte contient des mots interdits
 */
/**
 * Vérifie si le texte contient des mots interdits (VERSION AMÉLIORÉE)
 */
function containsForbiddenWords(text) {
    const normalized = normalizeText(text);
    const noSpaces = removeAllSpaces(normalized);
    const foundWords = [];
    
    // Parcourir toutes les catégories
    for (const [category, words] of Object.entries(FORBIDDEN_WORDS)) {
        for (const word of words) {
            const normalizedWord = normalizeText(word);
            
            //  AMÉLIORATION : Seulement les mots de 4+ caractères
            if (normalizedWord.length < 4) continue;
            
            // Détection 1 : Mot exact avec frontières
            const regexExact = new RegExp(`\\b${normalizedWord}\\b`, 'i');
            if (regexExact.test(normalized)) {
                foundWords.push({ word, category, type: 'exact' });
                continue;
            }
            
            // Détection 2 : Mot sans espaces (f u c k)
            if (noSpaces.includes(normalizedWord)) {
                foundWords.push({ word, category, type: 'spaced' });
            }
        }
    }
    
    return foundWords;
}

/**
 * Vérifie si le texte est du spam (CAPSLOCK, répétitions)
 */
/**
 * Vérifie si le texte est du spam (VERSION PLUS TOLÉRANTE)
 */
function isSpam(text) {
    // Trop de majuscules (> 80% et plus de 30 caractères)
    const upperCount = (text.match(/[A-Z]/g) || []).length;
    const letterCount = (text.match(/[a-zA-Z]/g) || []).length;
    if (letterCount > 30 && upperCount / letterCount > 0.8) {
        return { isSpam: true, reason: 'Trop de MAJUSCULES (spam détecté)' };
    }
    
    // Répétitions excessives (!!!!!!!! ou ????????)
    if (/([!?.]){10,}/.test(text)) {
        return { isSpam: true, reason: 'Ponctuation excessive' };
    }
    
    // Emojis excessifs (plus de 15)
    const emojiCount = (text.match(/[\u{1F300}-\u{1F9FF}]/gu) || []).length;
    if (emojiCount > 15) {
        return { isSpam: true, reason: 'Trop d\'emojis' };
    }
    
    return { isSpam: false };
}

/**
 * Validation complète du contenu
 */
/**
 * Validation complète du contenu (VERSION FLEXIBLE)
 */
function validateContent(text, options = {}) {
    const {
        minLength = 3,      //  Réduit à 3 caractères minimum
        maxLength = 5000,
        allowLinks = false,
        strictMode = false  //  Nouveau : mode strict optionnel
    } = options;
    
    // Vérifications de base
    if (!text || text.trim().length === 0) {
        return {
            valid: false,
            error: '❌ Le contenu ne peut pas être vide.'
        };
    }
    
    const trimmedLength = text.trim().length;
    
    if (trimmedLength < minLength) {
        return {
            valid: false,
            error: `❌ Le contenu doit contenir au moins ${minLength} caractères.`
        };
    }
    
    if (text.length > maxLength) {
        return {
            valid: false,
            error: `❌ Le contenu ne peut pas dépasser ${maxLength} caractères.`
        };
    }
    
    //  Vérification des liens (seulement si strictMode activé)
    if (!allowLinks && strictMode && /https?:\/\//i.test(text)) {
        return {
            valid: false,
            error: '❌ Les liens ne sont pas autorisés dans les messages.'
        };
    }
    
    // Vérification spam (seulement messages longs)
    if (trimmedLength > 20) {
        const spamCheck = isSpam(text);
        if (spamCheck.isSpam) {
            return {
                valid: false,
                error: `❌ ${spamCheck.reason}`
            };
        }
    }
    
    // Vérification mots interdits
    const forbiddenWords = containsForbiddenWords(text);
    if (forbiddenWords.length > 0) {
        const wordsList = forbiddenWords.map(w => `"${w.word}"`).join(', ');
        return {
            valid: false,
            error: `🚫 Langage inapproprié détecté : ${wordsList}\n\nMerci de rester respectueux dans vos messages.`,
            forbiddenWords: forbiddenWords
        };
    }
    
    return {
        valid: true,
        message: '✅ Contenu valide'
    };
}

// ========================================
//  AFFICHAGE DES MESSAGES D'ERREUR
// ========================================

function showError(inputElement, errorMessage) {
    // Supprimer les anciens messages
    const oldError = inputElement.parentElement.querySelector('.content-error');
    if (oldError) oldError.remove();
    
    // Créer le message d'erreur
    const errorDiv = document.createElement('div');
    errorDiv.className = 'content-error alert alert-danger mt-2';
    errorDiv.style.fontSize = '0.9rem';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${errorMessage}`;
    
    // Ajouter après l'input
    inputElement.parentElement.appendChild(errorDiv);
    
    // Bordure rouge
    inputElement.style.borderColor = '#dc3545';
    inputElement.style.borderWidth = '2px';
}

function clearError(inputElement) {
    const errorDiv = inputElement.parentElement.querySelector('.content-error');
    if (errorDiv) errorDiv.remove();
    
    inputElement.style.borderColor = '';
    inputElement.style.borderWidth = '';
}

function showSuccess(inputElement) {
    clearError(inputElement);
    inputElement.style.borderColor = '#28a745';
    inputElement.style.borderWidth = '2px';
}

// ========================================
//  INITIALISATION AUTOMATIQUE
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    const contentFields = document.querySelectorAll('textarea[name="content"]');
    
    contentFields.forEach(field => {
        
        // Validation en temps réel (keyup)
        field.addEventListener('keyup', function() {
            const text = this.value;
            
            if (text.length < 1) {
                clearError(this);
                return;
            }
            
            const validation = validateContent(text, {
                minLength: 3,        //  3 caractères minimum
                maxLength: 5000,
                allowLinks: false,
                strictMode: false    //  Mode souple
            });
            
            if (!validation.valid) {
                showError(this, validation.error);
            } else {
                showSuccess(this);
            }
        });
        
        // Validation au blur
        field.addEventListener('blur', function() {
            const text = this.value;
            if (text.length > 0) {
                const validation = validateContent(text, {
                    minLength: 3,
                    maxLength: 5000,
                    allowLinks: false,
                    strictMode: false
                });
                
                if (!validation.valid) {
                    showError(this, validation.error);
                }
            }
        });
    });
    
    // Intercepter les formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const contentField = this.querySelector('textarea[name="content"]');
            
            if (contentField) {
                const text = contentField.value;
                const validation = validateContent(text, {
                    minLength: 3,        //  3 caractères au submit
                    maxLength: 5000,
                    allowLinks: false,
                    strictMode: false
                });
                
                if (!validation.valid) {
                    e.preventDefault();
                    showError(contentField, validation.error);
                    contentField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    contentField.focus();
                    return false;
                }
            }
        });
    });
    
    console.log('✅ Content Filter System Initialized (Flexible Mode)');
});

// ========================================
//  EXPORT DES FONCTIONS
// ========================================

// Rendre les fonctions accessibles globalement
window.ContentFilter = {
    validate: validateContent,
    containsForbidden: containsForbiddenWords,
    isSpam: isSpam,
    normalize: normalizeText
};