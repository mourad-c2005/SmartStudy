// ========================================
// VALIDATION DU FORMULAIRE DE FORUM
// ========================================

document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("addForumForm");
  
  if (form) {
    // ========================================
    // PARTIE 1: Validation en temps réel
    // ========================================

    // Vérification du titre (keyup)
    const titleField = document.getElementById("title");
    if (titleField) {
      titleField.addEventListener("keyup", function() {
        const value = this.value.trim();
        const msg = document.getElementById("title_error");

        if (msg) {
          if (value.length === 0) {
            msg.style.color = "red";
            msg.innerText = "❌ Le titre est obligatoire";
          } else if (value.length < 10) {
            msg.style.color = "orange";
            msg.innerText = "⚠️ Le titre devrait contenir au moins 10 caractères";
          } else if (value.length > 200) {
            msg.style.color = "red";
            msg.innerText = "❌ Le titre ne peut pas dépasser 200 caractères";
          } else {
            msg.style.color = "green";
            msg.innerText = "✅ Titre valide";
          }
        }
      });
    }

    // Vérification de l'auteur (blur)
    const authorField = document.getElementById("author");
    if (authorField) {
      authorField.addEventListener("blur", function() {
        const value = this.value.trim();
        const msg = document.getElementById("author_error");
        const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,50}$/;

        if (msg) {
          if (value.length === 0) {
            msg.style.color = "red";
            msg.innerText = "❌ L'auteur est obligatoire";
          } else if (!regex.test(value)) {
            msg.style.color = "red";
            msg.innerText = "❌ L'auteur doit contenir uniquement des lettres (2-50 caractères)";
          } else {
            msg.style.color = "green";
            msg.innerText = "✅ Auteur valide";
          }
        }
      });
    }

    // Vérification du contenu (keyup)
    const contentField = document.getElementById("content");
    if (contentField) {
      contentField.addEventListener("keyup", function() {
        const value = this.value.trim();
        const msg = document.getElementById("content_error");
        const charCount = value.length;

        if (msg) {
          if (charCount === 0) {
            msg.style.color = "red";
            msg.innerText = "❌ Le contenu est obligatoire";
          } else if (charCount < 20) {
            msg.style.color = "orange";
            msg.innerText = `⚠️ Le contenu devrait contenir au moins 20 caractères (${charCount}/20)`;
          } else if (charCount > 5000) {
            msg.style.color = "red";
            msg.innerText = `❌ Le contenu ne peut pas dépasser 5000 caractères (${charCount}/5000)`;
          } else {
            msg.style.color = "green";
            msg.innerText = `✅ Contenu valide (${charCount} caractères)`;
          }
        }
      });
    }

    // Vérification de la catégorie (change)
    const categoryField = document.getElementById("category");
    if (categoryField) {
      categoryField.addEventListener("change", function() {
        const value = this.value;
        const msg = document.getElementById("category_error");

        if (msg) {
          if (value === "") {
            msg.style.color = "red";
            msg.innerText = "❌ Veuillez sélectionner une catégorie";
          } else {
            msg.style.color = "green";
            msg.innerText = `✅ Catégorie "${value}" sélectionnée`;
          }
        }
      });
    }

    // ========================================
    // PARTIE 2: Validation à la soumission
    // ========================================

    form.addEventListener("submit", function(event) {
      let isValid = true;
      let errors = [];

      // Validation du titre
      const title = document.getElementById("title").value.trim();
      if (title.length === 0) {
        errors.push("Le titre est obligatoire");
        isValid = false;
      } else if (title.length < 10) {
        errors.push("Le titre doit contenir au moins 10 caractères");
        isValid = false;
      } else if (title.length > 200) {
        errors.push("Le titre ne peut pas dépasser 200 caractères");
        isValid = false;
      }

      // Validation de la catégorie
      const category = document.getElementById("category").value;
      if (category === "") {
        errors.push("Veuillez sélectionner une catégorie");
        isValid = false;
      }

      // Validation de l'auteur
      const author = document.getElementById("author").value.trim();
      const authorRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,50}$/;
      if (author.length === 0) {
        errors.push("L'auteur est obligatoire");
        isValid = false;
      } else if (!authorRegex.test(author)) {
        errors.push("L'auteur doit contenir uniquement des lettres (2-50 caractères)");
        isValid = false;
      }

      // Validation du contenu
      const content = document.getElementById("content").value.trim();
      if (content.length === 0) {
        errors.push("Le contenu est obligatoire");
        isValid = false;
      } else if (content.length < 20) {
        errors.push("Le contenu doit contenir au moins 20 caractères");
        isValid = false;
      } else if (content.length > 5000) {
        errors.push("Le contenu ne peut pas dépasser 5000 caractères");
        isValid = false;
      }

      // Si des erreurs existent
      if (!isValid) {
        event.preventDefault();
        
        // Afficher les erreurs
        let errorMessage = "⚠️ Veuillez corriger les erreurs suivantes :\n\n";
        errors.forEach((error, index) => {
          errorMessage += `${index + 1}. ${error}\n`;
        });
        
        alert(errorMessage);
        return false;
      }

      // Confirmation avant soumission
      const confirmMessage = "Êtes-vous sûr de vouloir créer ce forum ?\n\n" +
                           `Titre: ${title}\n` +
                           `Catégorie: ${category}\n` +
                           `Auteur: ${author}`;
      
      if (!confirm(confirmMessage)) {
        event.preventDefault();
        return false;
      }

      return true;
    });
  }
});

// ========================================
// FONCTIONS UTILITAIRES
// ========================================

// Fonction pour nettoyer les espaces multiples
function cleanWhitespace(str) {
  return str.replace(/\s+/g, ' ').trim();
}

// Fonction pour échapper les caractères HTML
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}

// Fonction pour compter les mots
function countWords(str) {
  return str.trim().split(/\s+/).filter(word => word.length > 0).length;
}

// Auto-resize du textarea
document.addEventListener("DOMContentLoaded", function() {
  const contentField = document.getElementById("content");
  if (contentField) {
    contentField.addEventListener("input", function() {
      this.style.height = "auto";
      this.style.height = (this.scrollHeight) + "px";
    });
  }
});