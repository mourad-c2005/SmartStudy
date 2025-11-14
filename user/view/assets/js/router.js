// view/assets/js/router.js
const routes = {
  'login.html': () => load('login.html'),
  'inscrire.html': () => load('inscrire.html'),
  'index.html': () => load('index.html'),
  'admin/index.html': () => load('admin/index.html'),
  'admin/users.html': () => load('admin/users.html')
};

async function load(page) {
  const res = await fetch(`view/${page}`);
  document.body.innerHTML = await res.text();
  const scripts = ['auth.js', 'admin.js'];
  scripts.forEach(src => {
    const script = document.createElement('script');
    script.src = `view/assets/js/${src}`;
    document.body.appendChild(script);
  });
}

document.addEventListener('click', e => {
  if (e.target.matches('[data-nav]')) {
    e.preventDefault();
    history.pushState(null, null, e.target.href);
    const path = e.target.href.split('/').pop();
    routes[path]?.();
  }
});

window.onpopstate = () => routes[location.pathname.split('/').pop()]?.();