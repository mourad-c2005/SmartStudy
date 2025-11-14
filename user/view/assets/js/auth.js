// view/assets/js/auth.js
async function submitLogin() {
  const email = document.querySelector('#login-form input[type="email"]').value;
  const password = document.querySelector('#login-form input[type="password"]').value;
  const code = currentRole === 'admin' ? document.querySelector('#admin-code-field input').value : null;

  if (currentRole === 'admin' && code !== 'SMART2025') {
    alert('Code administrateur incorrect');
    return;
  }

  const res = await fetch('../controller/AuthController.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'login', email, password })
  });
  const data = await res.json();

  if (data.success) {
    window.location.href = data.redirect;
  } else {
    alert(data.message);
  }
}

// Pour inscrire.html
document.getElementById('inscriptionForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const data = Object.fromEntries(formData);
  data.action = 'signup';

  const res = await fetch('../controller/AuthController.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });
  const result = await res.json();

  if (result.success) {
    alert('Inscription réussie !');
    window.location.href = result.redirect;
  }
});