/* toggle show/hide password */
function togglePw() {
    const inp  = document.getElementById('password');
    const show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    document.getElementById('iconPw').innerHTML = show
        ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
           <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
           <line x1="1" y1="1" x2="23" y2="23"/>`
        : `<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>`;
}

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