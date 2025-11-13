/**
 * inscrire.js – Works with updated HTML
 * Social links are in left sidebar
 * Red sidebar if any link is missing
 * Top alert for email
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('inscriptionForm');
  const formCard = document.getElementById('formCard');
  const quizColumn = document.getElementById('quizColumn');
  const quizQuestions = document.getElementById('quizQuestions');
  const submitQuiz = document.getElementById('submitQuiz');
  const quizResult = document.getElementById('quizResult');
  const sidebar = document.querySelector('.sidebar');

  // Google Callback
  window.onSignInCallback = (user) => {
    if (user.getBasicProfile()) {
      document.getElementById('nom').value = user.getBasicProfile().getName();
      document.getElementById('email').value = user.getBasicProfile().getEmail();
      alert('Connexion Google réussie ! Formulaire rempli.');
    }
  };

  // Top Alert
  const topAlert = document.createElement('div');
  topAlert.id = 'topAlert';
  topAlert.style.cssText = `position:fixed;top:0;left:0;right:0;background:#e74c3c;color:white;padding:1rem;text-align:center;font-weight:600;z-index:1000;display:none;animation:slideDown .4s ease;`;
  topAlert.innerHTML = `<i class="fas fa-exclamation-triangle"></i> <span></span>`;
  document.body.appendChild(topAlert);
  const topAlertMsg = topAlert.querySelector('span');

  // Sidebar Error
  const sidebarError = document.createElement('div');
  sidebarError.className = 'error-msg';
  sidebarError.style.marginTop = '0.8rem';
  sidebarError.style.justifyContent = 'center';
  sidebarError.innerHTML = `<i class="fas fa-exclamation-circle"></i> <span></span>`;
  sidebar.appendChild(sidebarError);
  const sidebarErrorMsg = sidebarError.querySelector('span');

  const inputs = {
    nom: document.getElementById('nom'), email: document.getElementById('email'),
    password: document.getElementById('password'), dateNaissance: document.getElementById('dateNaissance'),
    etablissement: document.getElementById('etablissement'), niveau: document.getElementById('niveau'),
    role: document.getElementById('role'), twitter: document.getElementById('twitter'),
    linkedin: document.getElementById('linkedin'), github: document.getElementById('github')
  };

  const errors = {
    nom: document.getElementById('nomError').querySelector('span'),
    email: document.getElementById('emailError').querySelector('span'),
    age: document.getElementById('ageError').querySelector('span')
  };
  const errorContainers = { nom: document.getElementById('nomError'), email: document.getElementById('emailError'), age: document.getElementById('ageError') };

  const showError = (f, m) => { errors[f].textContent = m; errorContainers[f].style.display = 'flex'; inputs[Object.keys(inputs).find(k => errors[f] === document.querySelector(`#${k}Error span`))]?.classList.add('error'); };
  const hideError = (f) => { errorContainers[f].style.display = 'none'; inputs[Object.keys(inputs).find(k => errors[f] === document.querySelector(`#${k}Error span`))]?.classList.remove('error'); };

  const showTopAlert = (m) => { topAlertMsg.textContent = m; topAlert.style.display = 'block'; setTimeout(() => topAlert.style.display = 'none', 4000); };
  const setSidebarRed = (m) => { sidebar.style.border = '3px solid #e74c3c'; sidebar.style.boxShadow = '0 8px 25px rgba(231,76,60,0.2)'; sidebarErrorMsg.textContent = m; sidebarError.style.display = 'flex'; };
  const clearSidebarRed = () => { sidebar.style.border = ''; sidebar.style.boxShadow = ''; sidebarError.style.display = 'none'; };

  // Real-time
  inputs.nom.addEventListener('input', () => { /[0-9]/.test(inputs.nom.value.trim()) ? showError('nom', 'Pas de chiffres') : hideError('nom'); });
  inputs.email.addEventListener('input', () => { const v = inputs.email.value.trim().toLowerCase(); if (v && (!v.includes('@') || !v.endsWith('.com'))) { showError('email', 'Email doit contenir @ et .com'); showTopAlert('Email invalide'); } else hideError('email'); });
  inputs.dateNaissance.addEventListener('change', () => { const b = new Date(inputs.dateNaissance.value); const t = new Date(); let a = t.getFullYear() - b.getFullYear(); const m = t.getMonth() - b.getMonth(); const d = t.getDate() - b.getDate(); if (m < 0 || (m === 0 && d < 0)) a--; a < 13 ? showError('age', 'Âge minimum : 13 ans') : hideError('age'); });

  // Social links check
  const checkSocial = () => { const t = inputs.twitter.value.trim(), l = inputs.linkedin.value.trim(), g = inputs.github.value.trim(); (!t || !l || !g) ? setSidebarRed('Complétez les 3 liens') : clearSidebarRed(); };
  ['twitter', 'linkedin', 'github'].forEach(id => inputs[id].addEventListener('input', checkSocial));

  // Quiz
  const quizData = [
    { q: "Heures d'étude/jour ?", options: ["<2h","2-4h","4-6h",">6h"] },
    { q: "Style préféré ?", options: ["Visuel","Auditif","Kinésthésique","Lecture"] },
    { q: "Objectif ?", options: ["Notes","Compétence","Examen","Stress"] }
  ];
  const loadQuiz = () => {
    quizQuestions.innerHTML = '';
    quizData.forEach((item, i) => {
      const div = document.createElement('div'); div.className = 'question';
      div.innerHTML = `<p>${item.q}</p>` + item.options.map((o,j) => `<label><input type="radio" name="q${i}" value="${o}" required> ${o}</label>`).join('');
      quizQuestions.appendChild(div);
    });
  };

  submitQuiz.addEventListener('click', () => {
    const answers = []; let ok = true;
    quizData.forEach((_, i) => { const s = document.querySelector(`input[name="q${i}"]:checked`); if (!s) ok = false; else answers.push(s.value); });
    if (!ok) { quizResult.innerHTML = `<i class="fas fa-times-circle" style="color:var(--error)"></i> Répondez à tout`; return; }
    quizResult.innerHTML = `<i class="fas fa-check-circle" style="color:var(--success)"></i> Réponses enregistrées !`; submitQuiz.disabled = true;
    const s = JSON.parse(localStorage.getItem('smartstudy_users') || '[]'); const l = s[s.length-1]; if (l) { l.profile.quiz = answers; localStorage.setItem('smartstudy_users', JSON.stringify(s)); }
  });

  // Submit
  form.addEventListener('submit', e => {
    e.preventDefault(); Object.keys(errors).forEach(hideError); clearSidebarRed(); topAlert.style.display = 'none';
    const data = {
      nom: inputs.nom.value.trim(), email: inputs.email.value.trim().toLowerCase(), password: inputs.password.value,
      dateNaissance: inputs.dateNaissance.value, etablissement: inputs.etablissement.value, niveau: inputs.niveau.value,
      role: inputs.role.value, twitter: inputs.twitter.value.trim() || null, linkedin: inputs.linkedin.value.trim() || null, github: inputs.github.value.trim() || null
    };

    if (!data.nom) return showError('nom', 'Nom requis');
    if (/[0-9]/.test(data.nom)) return showError('nom', 'Pas de chiffres');
    if (!data.email) return showError('email', 'Email requis');
    if (!data.email.includes('@') || !data.email.endsWith('.com')) { showError('email', 'Email doit contenir @ et .com'); showTopAlert('Email invalide'); return; }
    if (!data.dateNaissance) return showError('age', 'Date requise');
    const age = ((b) => { const t = new Date(); let a = t.getFullYear() - b.getFullYear(); const m = t.getMonth() - b.getMonth(); const d = t.getDate() - b.getDate(); if (m < 0 || (m === 0 && d < 0)) a--; return a; })(new Date(data.dateNaissance));
    if (age < 13) return showError('age', 'Âge minimum : 13 ans');
    if (!data.etablissement || !data.niveau || !data.role) return alert('Champs obligatoires manquants');
    if (!data.twitter || !data.linkedin || !data.github) { setSidebarRed('Liens sociaux obligatoires'); showTopAlert('Complétez les 3 liens'); return; }

    const user = { id_user: Date.now(), ...data, date_creation: new Date().toISOString().split('T')[0] };
    const profile = { id_profile: Date.now()+1, id_user: user.id_user, photo: null, bio: '', preferences: '', role: data.role, date_naissance: data.dateNaissance, etablissement: data.etablissement, niveau: data.niveau, derniere_connexion: null, social: { twitter: data.twitter, linkedin: data.linkedin, github: data.github }, quiz: [] };
    const s = JSON.parse(localStorage.getItem('smartstudy_users') || '[]'); s.push({ user, profile }); localStorage.setItem('smartstudy_users', JSON.stringify(s));

    formCard.style.display = 'none'; quizColumn.style.display = 'flex'; loadQuiz();
    alert(`Inscription réussie ! Bienvenue, ${user.nom} !`);
  });

  const style = document.createElement('style'); style.textContent = `@keyframes slideDown { from { transform: translateY(-100%); } to { transform: translateY(0); } }`; document.head.appendChild(style);
});