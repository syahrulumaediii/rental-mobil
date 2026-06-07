<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Rull Car</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/auth/login.css',
        'resources/js/auth/login.js'
    ])
</head>
<body>

<!-- ════════════ BACKGROUND SCENE ════════════ -->
<svg class="bg-scene" viewBox="0 0 1200 700" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
    <defs>
        <linearGradient id="lsky" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%"  stop-color="#020818"/>
            <stop offset="60%" stop-color="#061228"/>
            <stop offset="100%" stop-color="#0a1f40"/>
        </linearGradient>
        <linearGradient id="lroad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%"  stop-color="#141c2e"/>
            <stop offset="100%" stop-color="#0c1424"/>
        </linearGradient>
        <linearGradient id="lcone" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%"  stop-color="#fffbe6" stop-opacity=".7"/>
            <stop offset="100%" stop-color="#fffbe6" stop-opacity="0"/>
        </linearGradient>
        <linearGradient id="lbldGlow" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%"  stop-color="#0ea5e9" stop-opacity=".8"/>
            <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0"/>
        </linearGradient>
        <radialGradient id="lmoonGlow" cx="50%" cy="50%" r="50%">
            <stop offset="0%"  stop-color="#fffde7" stop-opacity=".25"/>
            <stop offset="100%" stop-color="#fffde7" stop-opacity="0"/>
        </radialGradient>
        <filter id="lneon" x="-30%" y="-30%" width="160%" height="160%">
            <feGaussianBlur stdDeviation="3" result="b"/>
            <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
    </defs>

    <!-- Sky -->
    <rect width="1200" height="700" fill="url(#lsky)"/>

    <!-- Moon -->
    <circle cx="1060" cy="90" r="60" fill="url(#lmoonGlow)"/>
    <circle cx="1060" cy="90" r="26" fill="#fffef0" opacity=".88"/>
    <circle cx="1052" cy="83" r="6" fill="#ffe082" opacity=".35"/>

    <!-- Stars -->
    <g fill="#fff">
        <circle class="star" style="--d:2.8s;--delay:0s"   cx="60"   cy="35"  r="1.2"/>
        <circle class="star" style="--d:3.5s;--delay:.4s"  cx="200"  cy="22"  r=".9"/>
        <circle class="star" style="--d:2.2s;--delay:1s"   cx="350"  cy="50"  r="1"/>
        <circle class="star" style="--d:4s;--delay:.2s"    cx="500"  cy="18"  r="1.4"/>
        <circle class="star" style="--d:3.1s;--delay:.7s"  cx="650"  cy="42"  r=".8"/>
        <circle class="star" style="--d:2.6s;--delay:1.3s" cx="800"  cy="14"  r="1.1"/>
        <circle class="star" style="--d:3.8s;--delay:.5s"  cx="950"  cy="36"  r=".7"/>
        <circle class="star" style="--d:2.9s;--delay:1.8s" cx="1100" cy="25"  r="1"/>
        <circle class="star" style="--d:3.3s;--delay:.9s"  cx="140"  cy="72"  r=".8"/>
        <circle class="star" style="--d:2.5s;--delay:.3s"  cx="420"  cy="78"  r=".9"/>
        <circle class="star" style="--d:4.2s;--delay:1.1s" cx="720"  cy="62"  r=".7"/>
        <circle class="star" style="--d:3.0s;--delay:1.6s" cx="900"  cy="82"  r="1.2"/>
        <circle class="star" style="--d:2.7s;--delay:.6s"  cx="1150" cy="55"  r=".8"/>
    </g>

    <!-- Buildings left cluster -->
    <rect x="0"   y="360" width="70"  height="340" fill="#0b1628"/>
    <rect x="75"  y="220" width="100" height="480" fill="#0c1a30"/>
    <rect x="75"  y="218" width="4"   height="462" fill="url(#lbldGlow)"/>
    <rect x="100" y="188" width="3"   height="32"  fill="#1a3a6e"/>
    <circle cx="101.5" cy="186" r="5" fill="#ef4444" opacity=".9" style="animation:twinkle 1.5s infinite"/>
    <rect x="180" y="280" width="80"  height="420" fill="#0d1f38"/>
    <rect x="180" y="278" width="4"   height="422" fill="#7c3aed" opacity=".18"/>
    <g fill="#93c5fd" opacity=".55">
        <rect x="85"  y="232" width="13" height="9" rx="1"/><rect x="104" y="232" width="13" height="9" rx="1" style="animation:winBlink 5s 1s infinite"/><rect x="123" y="232" width="13" height="9" rx="1"/><rect x="142" y="232" width="12" height="9" rx="1"/>
        <rect x="85"  y="249" width="13" height="9" rx="1"/><rect x="123" y="249" width="13" height="9" rx="1"/><rect x="142" y="249" width="12" height="9" rx="1" style="animation:winBlink 8s 2s infinite"/>
        <rect x="104" y="266" width="13" height="9" rx="1"/><rect x="123" y="266" width="13" height="9" rx="1"/>
        <rect x="85"  y="283" width="13" height="9" rx="1" style="animation:winBlink 6s .5s infinite"/><rect x="142" y="283" width="12" height="9" rx="1"/>
        <rect x="104" y="300" width="13" height="9" rx="1"/><rect x="123" y="300" width="13" height="9" rx="1"/>
        <rect x="85"  y="317" width="13" height="9" rx="1"/><rect x="142" y="317" width="12" height="9" rx="1" style="animation:winBlink 9s 3s infinite"/>
    </g>
    <g fill="#ddd6fe" opacity=".5">
        <rect x="190" y="292" width="12" height="8" rx="1"/><rect x="208" y="292" width="12" height="8" rx="1" style="animation:winBlink 7s 1s infinite"/><rect x="226" y="292" width="12" height="8" rx="1"/><rect x="244" y="292" width="8" height="8" rx="1"/>
        <rect x="190" y="308" width="12" height="8" rx="1"/><rect x="226" y="308" width="12" height="8" rx="1"/>
        <rect x="208" y="324" width="12" height="8" rx="1"/><rect x="244" y="324" width="8" height="8" rx="1" style="animation:winBlink 11s 2s infinite"/>
        <rect x="190" y="340" width="12" height="8" rx="1"/><rect x="226" y="340" width="12" height="8" rx="1"/>
        <rect x="190" y="356" width="12" height="8" rx="1" style="animation:winBlink 8s 3.5s infinite"/><rect x="208" y="356" width="12" height="8" rx="1"/>
    </g>

    <!-- Tallest center building -->
    <rect x="480" y="150" width="120" height="550" fill="#0a1826"/>
    <rect x="480" y="148" width="120" height="5"   fill="#0ea5e9" opacity=".4"/>
    <rect x="480" y="148" width="4"   height="552" fill="url(#lbldGlow)"/>
    <rect x="596" y="148" width="4"   height="552" fill="#0ea5e9" opacity=".12"/>
    <polygon points="539,118 552,150 527,150" fill="#0f2848"/>
    <rect x="537" y="96" width="5" height="22" fill="#1a3a6e"/>
    <circle cx="539.5" cy="94" r="5" fill="#0ea5e9" opacity=".9" style="animation:twinkle 2s infinite"/>
    <rect x="480" y="310" width="120" height="3" fill="#0ea5e9" opacity=".22"/>
    <rect x="480" y="400" width="120" height="3" fill="#0ea5e9" opacity=".18"/>
    <g filter="url(#lneon)">
        <text x="496" y="490" fill="#0ea5e9" font-size="13" font-family="monospace" font-weight="700" opacity=".95">RULL CAR</text>
    </g>
    <line x1="496" y1="494" x2="568" y2="494" stroke="#0ea5e9" stroke-width="1.5" opacity=".7"/>
    <g fill="#bae6fd" opacity=".5">
        <rect x="492" y="162" width="15" height="10" rx="1"/><rect x="514" y="162" width="15" height="10" rx="1" style="animation:winBlink 5s 1s infinite"/><rect x="536" y="162" width="15" height="10" rx="1"/><rect x="558" y="162" width="15" height="10" rx="1"/>
        <rect x="492" y="180" width="15" height="10" rx="1"/><rect x="536" y="180" width="15" height="10" rx="1"/><rect x="558" y="180" width="15" height="10" rx="1" style="animation:winBlink 8s 2s infinite"/>
        <rect x="514" y="198" width="15" height="10" rx="1"/><rect x="536" y="198" width="15" height="10" rx="1"/>
        <rect x="492" y="216" width="15" height="10" rx="1" style="animation:winBlink 6s .5s infinite"/><rect x="558" y="216" width="15" height="10" rx="1"/>
        <rect x="514" y="234" width="15" height="10" rx="1"/><rect x="536" y="234" width="15" height="10" rx="1"/>
        <rect x="492" y="252" width="15" height="10" rx="1"/><rect x="558" y="252" width="15" height="10" rx="1" style="animation:winBlink 9s 3s infinite"/>
        <rect x="514" y="320" width="15" height="10" rx="1"/><rect x="536" y="320" width="15" height="10" rx="1"/><rect x="492" y="320" width="15" height="10" rx="1"/>
        <rect x="492" y="338" width="15" height="10" rx="1" style="animation:winBlink 7s 1.5s infinite"/><rect x="558" y="338" width="15" height="10" rx="1"/>
    </g>

    <!-- Buildings right cluster -->
    <rect x="720"  y="270" width="90"  height="430" fill="#0c1a30"/>
    <rect x="720"  y="268" width="4"   height="432" fill="#7c3aed" opacity=".2"/>
    <rect x="820"  y="300" width="110" height="400" fill="#0b1628"/>
    <rect x="930"  y="340" width="90"  height="360" fill="#0d1f38"/>
    <rect x="1030" y="380" width="80"  height="320" fill="#0c1424"/>
    <rect x="1115" y="400" width="85"  height="300" fill="#0b1628"/>
    <g fill="#fed7aa" opacity=".5">
        <rect x="730" y="282" width="12" height="8" rx="1"/><rect x="748" y="282" width="12" height="8" rx="1" style="animation:winBlink 6s 1s infinite"/><rect x="766" y="282" width="12" height="8" rx="1"/><rect x="784" y="282" width="11" height="8" rx="1"/>
        <rect x="730" y="298" width="12" height="8" rx="1"/><rect x="766" y="298" width="12" height="8" rx="1"/>
        <rect x="748" y="314" width="12" height="8" rx="1"/><rect x="784" y="314" width="11" height="8" rx="1" style="animation:winBlink 9s 2s infinite"/>
        <rect x="730" y="330" width="12" height="8" rx="1"/><rect x="766" y="330" width="12" height="8" rx="1"/>
    </g>
    <g fill="#a7f3d0" opacity=".45">
        <rect x="830" y="312" width="13" height="8" rx="1"/><rect x="849" y="312" width="13" height="8" rx="1" style="animation:winBlink 7s 1s infinite"/><rect x="868" y="312" width="13" height="8" rx="1"/><rect x="887" y="312" width="10" height="8" rx="1"/>
        <rect x="830" y="328" width="13" height="8" rx="1"/><rect x="868" y="328" width="13" height="8" rx="1"/>
        <rect x="849" y="344" width="13" height="8" rx="1"/><rect x="887" y="344" width="10" height="8" rx="1" style="animation:winBlink 10s 3s infinite"/>
    </g>

    <!-- Ground -->
    <rect x="0" y="565" width="1200" height="135" fill="#060e1c"/>
    <rect x="0" y="568" width="1200" height="80"  fill="url(#lroad)"/>
    <line x1="0" y1="570" x2="1200" y2="570" stroke="#1e40af" stroke-width="1" opacity=".35"/>
    <line x1="0" y1="646" x2="1200" y2="646" stroke="#1e40af" stroke-width="1" opacity=".35"/>
    <line class="road-dash" x1="0" y1="608" x2="1200" y2="608"
          stroke="#475569" stroke-width="2" stroke-dasharray="20 20" opacity=".45"/>
    <rect x="0" y="646" width="1200" height="54" fill="#060e1c"/>
    <line x1="0" y1="648" x2="1200" y2="648" stroke="#1e3a6e" stroke-width="1.5" opacity=".4"/>

    <!-- Headlight beams -->
    <polygon class="beam" points="140,604 80,568 80,628" fill="url(#lcone)" opacity=".35"/>
    <polygon class="beam" points="140,616 65,590 65,644"  fill="url(#lcone)" opacity=".25"/>

    <!-- Car -->
    <g class="car-group">
        <ellipse cx="390" cy="648" rx="120" ry="8" fill="#000" opacity=".3"/>
        <rect x="290" y="604" width="210" height="40" rx="6" fill="#0ea5e9"/>
        <path d="M340,604 Q358,577 430,577 Q464,577 472,604 Z" fill="#0284c7"/>
        <path d="M352,604 Q365,584 424,584 Q448,584 458,604 Z" fill="#bae6fd" opacity=".55"/>
        <path d="M340,604 Q349,590 372,586 L358,604 Z" fill="#7dd3fc" opacity=".35"/>
        <line x1="398" y1="604" x2="398" y2="644" stroke="#0369a1" stroke-width="1.5" opacity=".5"/>
        <line x1="424" y1="604" x2="424" y2="644" stroke="#0369a1" stroke-width="1.5" opacity=".5"/>
        <rect x="402" y="620" width="14" height="3" rx="1.5" fill="#0369a1" opacity=".7"/>
        <rect x="428" y="620" width="14" height="3" rx="1.5" fill="#0369a1" opacity=".7"/>
        <rect x="290" y="626" width="210" height="4" fill="#0369a1" opacity=".3"/>
        <rect x="496" y="614" width="14" height="24" rx="4" fill="#0369a1"/>
        <rect x="284" y="614" width="12" height="24" rx="4" fill="#0369a1"/>
        <circle cx="446" cy="648" r="20" fill="#1e293b"/>
        <circle cx="446" cy="648" r="13" fill="#334155"/>
        <circle cx="446" cy="648" r="6"  fill="#475569"/>
        <line x1="446" y1="635" x2="446" y2="661" stroke="#94a3b8" stroke-width="1.5"/>
        <line x1="433" y1="648" x2="459" y2="648" stroke="#94a3b8" stroke-width="1.5"/>
        <line x1="437" y1="639" x2="455" y2="657" stroke="#94a3b8" stroke-width="1.2"/>
        <line x1="455" y1="639" x2="437" y2="657" stroke="#94a3b8" stroke-width="1.2"/>
        <circle cx="336" cy="648" r="20" fill="#1e293b"/>
        <circle cx="336" cy="648" r="13" fill="#334155"/>
        <circle cx="336" cy="648" r="6"  fill="#475569"/>
        <line x1="336" y1="635" x2="336" y2="661" stroke="#94a3b8" stroke-width="1.5"/>
        <line x1="323" y1="648" x2="349" y2="648" stroke="#94a3b8" stroke-width="1.5"/>
        <line x1="327" y1="639" x2="345" y2="657" stroke="#94a3b8" stroke-width="1.2"/>
        <line x1="345" y1="639" x2="327" y2="657" stroke="#94a3b8" stroke-width="1.2"/>
        <rect x="506" y="612" width="10" height="10" rx="2" fill="#fef9c3"/>
        <rect x="506" y="625" width="10" height="7"  rx="2" fill="#fef9c3" opacity=".8"/>
        <rect x="274" y="612" width="12" height="10" rx="2" fill="#ef4444"/>
        <rect x="274" y="625" width="12" height="7"  rx="2" fill="#f87171" opacity=".7"/>
    </g>

    <!-- Street lamps -->
    <rect x="660" y="490" width="5" height="160" fill="#1e3a5e"/>
    <path d="M662,490 Q682,475 705,478" fill="none" stroke="#1e3a5e" stroke-width="4"/>
    <ellipse cx="705" cy="480" rx="14" ry="7" fill="#fef3c7" opacity=".88"/>
    <ellipse cx="705" cy="492" rx="36" ry="18" fill="#fef3c7" opacity=".05"/>
    <rect x="1000" y="510" width="4" height="140" fill="#1e3a5e"/>
    <path d="M1002,510 Q1018,497 1035,499" fill="none" stroke="#1e3a5e" stroke-width="3"/>
    <ellipse cx="1035" cy="501" rx="10" ry="5" fill="#fef3c7" opacity=".8"/>
    <ellipse cx="600" cy="615" rx="140" ry="4" fill="#0ea5e9" opacity=".05"/>
</svg>

<div class="bg-overlay"></div>

<!-- ════════════ NAV ════════════ -->
<nav>
    <div class="nav-logo-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
            <rect x="9" y="11" width="14" height="10" rx="1"/>
            <circle cx="12" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        </svg>
    </div>
    <span class="nav-logo-text">Rull<span>Car</span></span>
</nav>

<!-- ════════════ MAIN ════════════ -->
<main class="page-center">
    <div class="card">
        <h1 class="card-title">Selamat Datang</h1>
        <p class="card-sub">Masuk ke akun Rull Car Anda</p>

        <!-- ERROR ALERT — Laravel: @if($errors->any() || session('error')) -->
        <div class="alert alert-error" id="alertError" style="display:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span id="alertErrorMsg">Email atau password salah. Silakan coba lagi.</span>
        </div>
        @endif

        <!-- FORM -->
        <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <!-- Email -->
            <div class="form-group">
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder=" " required autocomplete="email"
                       class="@error('email') is-error @enderror">
                <label for="email">Alamat Email</label>
                <span class="field-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                </span>
                <p class="field-error @error('email') visible @enderror" id="err-email">
                    @error('email'){{ $message }}@else Format email tidak valid @enderror
                </p>
            </div>

            <!-- Password -->
            <div class="form-group">
                <input type="password" id="password" name="password"
                       placeholder=" " required autocomplete="current-password"
                       class="@error('password') is-error @enderror">
                <label for="password">Password</label>
                <span class="field-icon toggle-pw" onclick="togglePw()">
                    <svg id="iconPw" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </span>
                <p class="field-error @error('password') visible @enderror" id="err-password">
                    @error('password'){{ $message }}@else Password tidak boleh kosong @enderror
                </p>
            </div>

            <!-- Remember + Forgot -->
            {{-- <div class="form-meta">
                <div class="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            </div> --}}

            <button type="submit" class="btn-submit" id="btnSubmit">
                <div class="spinner" id="spinner"></div>
                <span class="btn-text">Masuk</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     id="btnArrow">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                </svg>
            </button>
        </form>

        <!-- Stats -->
        {{-- <div class="stats-row">
            <div class="stat-item">
                <span class="stat-num">2.4K+</span>
                <span class="stat-lbl">Kendaraan</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">98%</span>
                <span class="stat-lbl">Kepuasan</span>
            </div>
            <div class="stat-item">
                <span class="stat-num">50+</span>
                <span class="stat-lbl">Kota</span>
            </div>
        </div> --}}

        <p class="footer-link">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </div>
</main>


</body>
</html>