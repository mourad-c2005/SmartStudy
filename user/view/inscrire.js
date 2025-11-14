console.log("inscrire.js chargé !");

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
    dateNaissance: getEl('dateNaissance'),
    etablissement: getEl('etablissement'),
    niveau: getEl('niveau'),
    role: getEl('role'),
    twitter: getEl('twitter'),
    linkedin: getEl('linkedin'),
    github: getEl('github')
  };

  // Arrête si un champ manque
  if (Object.values(inputs).some(el => !el)) return;

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

  // === SIDEBAR ROUGE ===
  const sidebarError = document.getElementById('sidebarError');
  const sidebarErrorMsg = sidebarError?.querySelector('span');
  const sidebar = document.querySelector('.sidebar');

  const setSidebarRed = (msg) => {
    if (sidebar && sidebarError) {
      sidebar.style.border = '3px solid #e74c3c';
      sidebar.style.boxShadow = '0 8px 25px rgba(231,76,60,0.2)';
      sidebarErrorMsg.textContent = msg;
      sidebarError.style.display = 'flex';
    }
  };

  const clearSidebarRed = () => {
    if (sidebar && sidebarError) {
      sidebar.style.border = '';
      sidebar.style.boxShadow = '';
      sidebarError.style.display = 'none';
    }
  };

  // === VALIDATION EN TEMPS RÉEL ===
  inputs.nom.addEventListener('input', () => {
    const v = inputs.nom.value.trim();
    v && /\d/.test(v) ? showError('nom', 'Pas de chiffres') : hideError('nom');
  });

  inputs.email.addEventListener('input', () => {
    const v = inputs.email.value.trim().toLowerCase();
    v && (!v.includes('@') || !v.endsWith('.com'))
      ? showError('email', 'Email doit contenir @ et .com')
      : hideError('email');
  });

  inputs.dateNaissance.addEventListener('change', () => {
    const birth = new Date(inputs.dateNaissance.value);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    age < 13 ? showError('age', 'Âge minimum : 13 ans') : hideError('age');
  });

  // === LIENS SOCIAUX ===
  const checkSocial = () => {
    const t = inputs.twitter.value.trim();
    const l = inputs.linkedin.value.trim();
    const g = inputs.github.value.trim();
    (!t || !l || !g) ? setSidebarRed('Complétez les 3 liens') : clearSidebarRed();
  };
  ['twitter', 'linkedin', 'github'].forEach(id => inputs[id].addEventListener('input', checkSocial));

  // === SOUMISSION ===
  form.addEventListener('submit', e => {
    e.preventDefault();
    ['nom', 'email', 'age'].forEach(hideError);
    clearSidebarRed();

    const data = {
      action: 'signup',
      nom: inputs.nom.value.trim(),
      email: inputs.email.value.trim().toLowerCase(),
      password: inputs.password.value,
      date_naissance: inputs.dateNaissance.value,
      etablissement: inputs.etablissement.value,
      niveau: inputs.niveau.value,
      role: inputs.role.value,
      twitter: inputs.twitter.value.trim(),
      linkedin: inputs.linkedin.value.trim(),
      github: inputs.github.value.trim()
    };

    fetch('../controller/AuthController.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(r => r.ok ? r.json() : Promise.reject(`HTTP ${r.status}`))
    .then(res => {
      if (res.success) {
        alert(`Inscription réussie ! Bienvenue, ${res.user.nom} !`);
        window.location.href = res.redirect || '../view/index.html';
      } else {
        alert('Erreur : ' + res.message);
        if (res.message.includes('liens')) setSidebarRed(res.message);
      }
    })
    .catch(err => {
      console.error('Erreur:', err);
      alert('Erreur réseau ou serveur');
    });
  });
});