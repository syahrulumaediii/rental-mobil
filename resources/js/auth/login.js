/* toggle show/hide password */
window.togglePw = function () {
    const inp = document.getElementById('password');

    inp.type = inp.type === 'password'
        ? 'text'
        : 'password';
};



/* client-side validation */
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;
    const email = document.getElementById('email');
    const pw    = document.getElementById('password');

    const setErr = (id, show) => {
        document.getElementById('err-' + id).classList.toggle('visible', show);
        document.getElementById(id).classList.toggle('is-error', show);
    };

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { setErr('email', true);  valid = false; }
    else setErr('email', false);

    if (!pw.value.trim()) { setErr('password', true); valid = false; }
    else setErr('password', false);

    if (!valid) { e.preventDefault(); return; }

    const btn = document.getElementById('btnSubmit');
    btn.classList.add('loading');
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('btnArrow').style.display = 'none';
});

/* Demo preview — hapus di production */
(function() {
    const p = new URLSearchParams(window.location.search);
    if (p.get('status') === 'error') {
        const el  = document.getElementById('alertError');
        const msg = document.getElementById('alertErrorMsg');
        msg.textContent = p.get('msg') || 'Email atau password salah. Silakan coba lagi.';
        el.style.display = 'flex';
    }
})();