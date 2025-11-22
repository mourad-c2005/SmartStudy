
    console.log("Validation JavaScript chargée !");

    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('inscriptionForm');
      if (!form) return console.error("Formulaire non trouvé");

      // === CHAMPS SÉCURISÉS ===
      const getEl = (id) => {
        const el = document.getElementById(id);
        if (!el) console.error(`Champ manquant : #${id}`);
        return el;
      };

      const inputs = {
        nom: getEl('nom'),
        email: getEl('email'),
        password: getEl('password'),
        confirmPassword: getEl('confirm-password'),
        dateNaissance: getEl('date_naissance'),
        etablissement: getEl('etablissement'),
        niveau: getEl('niveau'),
        role: getEl('role'),
        twitter: getEl('twitter'),
        linkedin: getEl('linkedin'),
        github: getEl('github')
      };

      // Arrête si un champ obligatoire manque
      const requiredFields = ['nom', 'email', 'password', 'confirmPassword', 'role'];
      if (requiredFields.some(field => !inputs[field])) return;

      // === ERREURS ===
      const showError = (field, msg) => {
        const errorEl = document.getElementById(field + 'Error');
        if (errorEl && inputs[field]) {
          errorEl.querySelector('span').textContent = msg;
          errorEl.style.display = 'flex';
          inputs[field].classList.add('error');
        }
      };

      const hideError = (field) => {
        const errorEl = document.getElementById(field + 'Error');
        if (errorEl && inputs[field]) {
          errorEl.style.display = 'none';
          inputs[field].classList.remove('error');
        }
      };

      // === VALIDATION EN TEMPS RÉEL ===
      inputs.nom.addEventListener('input', () => {
        const v = inputs.nom.value.trim();
        if (v && /\d/.test(v)) {
          showError('nom', 'Le nom ne doit pas contenir de chiffres');
        } else {
          hideError('nom');
        }
      });

      inputs.email.addEventListener('input', () => {
        const v = inputs.email.value.trim().toLowerCase();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (v && !emailRegex.test(v)) {
          showError('email', 'Veuillez entrer un email valide');
        } else {
          hideError('email');
        }
      });

      inputs.dateNaissance.addEventListener('change', () => {
        const birth = new Date(inputs.dateNaissance.value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        
        if (inputs.dateNaissance.value && age < 13) {
          showError('age', 'Âge minimum : 13 ans');
        } else {
          hideError('age');
        }
      });

      inputs.password.addEventListener('input', () => {
        const password = inputs.password.value;
        if (password && password.length < 6) {
          showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
        } else {
          hideError('password');
        }
      });

      inputs.confirmPassword.addEventListener('input', () => {
        const password = inputs.password.value;
        const confirmPassword = inputs.confirmPassword.value;
        if (confirmPassword && password !== confirmPassword) {
          showError('confirmPassword', 'Les mots de passe ne correspondent pas');
        } else {
          hideError('confirmPassword');
        }
      });

      // === VALIDATION AVANT SOUMISSION ===
      form.addEventListener('submit', e => {
        e.preventDefault();
        
        // Reset errors
        ['nom', 'email', 'age', 'password', 'confirmPassword'].forEach(hideError);

        let isValid = true;

        // Validation des champs obligatoires
        if (!inputs.nom.value.trim()) {
          showError('nom', 'Le nom complet est obligatoire');
          isValid = false;
        }

        if (!inputs.email.value.trim()) {
          showError('email', 'L\'email est obligatoire');
          isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email.value.trim())) {
          showError('email', 'Veuillez entrer un email valide');
          isValid = false;
        }

        if (!inputs.password.value) {
          showError('password', 'Le mot de passe est obligatoire');
          isValid = false;
        } else if (inputs.password.value.length < 6) {
          showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
          isValid = false;
        }

        if (!inputs.confirmPassword.value) {
          showError('confirmPassword', 'Veuillez confirmer le mot de passe');
          isValid = false;
        } else if (inputs.password.value !== inputs.confirmPassword.value) {
          showError('confirmPassword', 'Les mots de passe ne correspondent pas');
          isValid = false;
        }

        if (!inputs.role.value) {
          alert('Veuillez sélectionner un rôle');
          isValid = false;
        }

        // Validation âge
        if (inputs.dateNaissance.value) {
          const birth = new Date(inputs.dateNaissance.value);
          const today = new Date();
          let age = today.getFullYear() - birth.getFullYear();
          const m = today.getMonth() - birth.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
          
          if (age < 13) {
            showError('age', 'Âge minimum : 13 ans');
            isValid = false;
          }
        }

        if (isValid) {
          // Copier les valeurs des réseaux sociaux dans les champs cachés
          document.getElementById('twitter_hidden').value = inputs.twitter.value.trim();
          document.getElementById('linkedin_hidden').value = inputs.linkedin.value.trim();
          document.getElementById('github_hidden').value = inputs.github.value.trim();
          
          // Soumettre le formulaire
          form.submit();
        }
      });
    });
