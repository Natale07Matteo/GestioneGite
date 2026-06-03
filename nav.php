<?php
    @session_start();
    require_once('config.php');
    require_once('portal_roles.php');
    require_once('portal_auth.php');

    $paginaCorrente = basename($_SERVER['PHP_SELF']);

    // Sviluppo locale: mock login temporaneo per test su localhost senza reindirizzamento SSO
    if (isset($_GET['mock_login'])) {
        $_SESSION['id_utente'] = 1;
        $_SESSION['username'] = 'Matteo Natale';
        $_SESSION['ruolo'] = (int)$_GET['mock_login']; // 1 = Docente, 2 = Commissione
        $_SESSION['foto'] = '';
        $_SESSION['mock'] = true;
    }

    // ─── Validazione token su ogni caricamento pagina ────────────────────────
    if (isset($_SESSION['ruolo']) && $_SESSION['ruolo']) {
        // Utente loggato → verifica che il token sia ancora valido (salta per mock login)
        if (empty($_SESSION['mock'])) {
            verificaTokenValido();
        }
    } else {
        // Non loggato → tenta auto-login dal cookie (silenzioso, no redirect)
        // Escludi login.php perché gestisce il login nel suo blocco PHP in testa
        if ($paginaCorrente !== 'login.php') {
            tentaAutoLogin($conn, $PORTAL_ROLES);
        }
    }

    $nome_utente = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utente Sconosciuto';
    $ruolo = isset($_SESSION['ruolo']) ? $_SESSION['ruolo'] : null;
    $foto_utente = isset($_SESSION['foto']) ? $_SESSION['foto'] : '';

    // protezione per chi non e loggato
    if (!$ruolo && $paginaCorrente != 'login.php' && $paginaCorrente != 'index.php') {
        header("Location: login.php");
        exit;
    }

    // protezione per pagine riservate alla commissione
    if ($ruolo == 1 && $paginaCorrente == 'elencoBozze.php') {
        header("Location: index.php");
        exit;
    }
?>

<header>
    <div class="header-container header-left" style="flex: 1; flex-basis: 0; align-items: center; gap: 0.75rem; display: flex;">
        <div class="header-school-logo" style="width: 42px; height: 42px; background: #ffffff; padding: 3px; border-radius: 8px; flex-shrink: 0; box-sizing: border-box; display: flex; align-items: center; justify-content: center;">
            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 753.000000 730.000000" preserveAspectRatio="xMidYMid meet">
                <g transform="translate(0.000000,730.000000) scale(0.100000,-0.100000)" fill="#06c" stroke="none">
                    <path d="M745 7250 c-145 -37 -231 -72 -323 -132 -183 -120 -304 -283 -383
                    -517 l-29 -85 0 -2840 0 -2841 43 -126 c51 -151 109 -256 198 -358 143 -167
                    331 -249 638 -280 134 -14 5532 -15 5707 -1 232 18 376 62 523 160 180 121
                    285 279 366 549 l25 85 0 2804 0 2805 -36 101 c-74 206 -127 299 -233 410
                    -111 116 -254 199 -441 258 l-105 33 -2920 2 -2920 2 -110 -29z m2860 -437
                    c152 -78 751 -644 1176 -1113 288 -317 614 -708 858 -1030 208 -275 190 -365
                    -154 -735 -129 -139 -326 -354 -711 -781 -109 -121 -530 -585 -934 -1032 -488
                    -539 -744 -829 -763 -865 -91 -172 -25 -381 157 -499 49 -32 68 -38 111 -38
                    101 0 179 59 407 309 84 91 173 189 198 216 26 28 105 113 176 190 130 142
                    459 494 698 749 72 77 136 144 141 150 6 6 96 103 200 216 105 113 260 282
                    346 375 191 208 320 332 352 340 25 7 187 -59 187 -76 0 -15 -116 -172 -196
                    -265 -42 -49 -158 -174 -258 -279 -100 -104 -260 -275 -356 -380 -96 -104
                    -272 -293 -390 -420 -119 -126 -410 -440 -647 -696 -237 -256 -461 -493 -499
                    -528 -99 -90 -206 -140 -316 -148 -157 -11 -270 37 -404 172 -148 148 -222
                    349 -196 530 15 105 79 233 165 332 94 108 886 986 1386 1538 245 270 481 531
                    525 580 45 50 137 151 206 225 161 175 308 342 398 453 l72 88 -97 127 c-534
                    698 -1191 1416 -1744 1903 -165 146 -222 189 -249 189 -47 0 -262 -195 -741
                    -671 -600 -596 -978 -1006 -1460 -1584 -236 -282 -629 -793 -629 -816 0 -13
                    76 -117 174 -239 417 -519 789 -918 1431 -1536 132 -127 282 -274 334 -327
                    l94 -96 -73 -81 c-41 -44 -79 -79 -86 -78 -19 4 -451 407 -713 663 -318 313
                    -729 755 -1027 1105 -131 154 -313 388 -335 432 -44 86 -42 215 6 309 66 133
                    745 977 1080 1344 50 55 138 152 195 215 268 299 873 915 1305 1330 209 201
                    226 214 302 246 50 21 70 24 148 21 77 -3 99 -8 150 -34z m142 -1264 c-3 -10
                    -663 -732 -782 -856 -59 -62 -111 -113 -115 -113 -4 0 -40 36 -80 80 l-72 80
                    67 72 c36 40 185 204 330 363 144 160 314 344 378 409 l114 120 82 -75 c44
                    -41 80 -77 78 -80z m201 -230 c39 -38 72 -75 72 -82 0 -7 -21 -33 -46 -57 -25
                    -25 -132 -139 -237 -255 -220 -241 -243 -266 -398 -433 -63 -68 -131 -143
                    -153 -168 -21 -24 -44 -44 -51 -44 -13 0 -155 151 -155 165 0 5 44 54 98 109
                    53 56 185 198 291 316 263 292 480 520 494 520 7 0 45 -32 85 -71z m307 -328
                    c47 -21 108 -86 131 -138 9 -21 16 -66 16 -101 1 -102 -63 -210 -240 -408
                    l-52 -59 -75 80 c-41 44 -75 83 -75 88 0 4 32 46 70 93 84 103 168 224 156
                    224 -13 0 -218 -185 -302 -271 -126 -131 -314 -371 -314 -401 0 -15 51 25 152
                    122 l96 90 77 -85 c42 -46 75 -88 73 -92 -1 -4 -54 -57 -116 -116 -164 -158
                    -261 -197 -367 -148 -92 42 -155 159 -141 264 17 123 138 282 485 640 74 76
                    155 153 180 171 92 66 172 81 246 47z m2105 -557 c393 -74 677 -362 744 -753
                    33 -195 9 -338 -68 -410 -14 -14 -26 -23 -26 -20 -1 2 -5 103 -10 224 -9 248
                    -20 298 -93 438 -47 92 -161 218 -250 279 -70 48 -182 100 -252 118 -29 8
                    -139 16 -258 19 l-208 6 23 35 c39 59 95 80 214 80 56 0 139 -7 184 -16z m25
                    -226 c195 -56 354 -186 438 -358 61 -124 79 -215 75 -380 l-3 -125 -40 3 c-60
                    5 -70 14 -66 64 4 72 -19 258 -40 322 -26 80 -78 158 -151 227 -113 108 -223
                    146 -455 156 -79 3 -143 9 -143 13 0 4 13 25 30 48 30 42 45 48 130 54 70 6
                    156 -4 225 -24z m-102 -199 c139 -22 277 -121 338 -244 27 -55 57 -176 74
                    -296 5 -43 4 -47 -17 -53 -13 -3 -39 -6 -60 -6 l-36 0 -6 53 c-23 187 -40 244
                    -92 313 -36 47 -102 79 -246 120 -65 18 -139 44 -165 57 -45 23 -46 25 -25 36
                    18 10 92 24 150 30 7 0 46 -4 85 -10z m18 -287 c165 -86 154 -326 -18 -397
                    -53 -22 -141 -19 -183 6 -86 52 -120 103 -120 181 0 72 14 116 51 161 67 82
                    171 101 270 49z"/>
                </g>
            </svg>
        </div>
        <h2 style="margin:0;">Gestione Gite</h2>
    </div>
    
    <nav class="header-nav">
        <a href="index.php" class="<?php echo ($paginaCorrente == 'index.php') ? 'active' : ''; ?>">Home</a>
        <?php if ($ruolo): ?>
            <a href="catalogo.php" class="<?php echo ($paginaCorrente == 'catalogo.php') ? 'active' : ''; ?>">Proposte</a>
            <a href="mieGite.php" class="<?php echo ($paginaCorrente == 'mieGite.php') ? 'active' : ''; ?>">Le mie Gite</a>
            <a href="inProgramma.php" class="<?php echo ($paginaCorrente == 'inProgramma.php') ? 'active' : ''; ?>">In Programma</a>
            <?php if ($ruolo == 2): ?>
                <a href="elencoBozze.php" class="<?php echo ($paginaCorrente == 'elencoBozze.php') ? 'active' : ''; ?>">Bozze</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="login.php" class="<?php echo ($paginaCorrente == 'login.php') ? 'active' : ''; ?>">Accedi</a>
        <?php endif; ?>
    </nav>

    <div class="header-container header-right" style="flex: 1; flex-basis: 0; position: relative;">
        <?php if ($ruolo): ?>
            <div class="profile-container" id="pulsanteProfilo" onclick="toggleMenuTendina(event)">
                <?php if ($foto_utente): ?>
                    <img src="<?php echo htmlspecialchars($foto_utente); ?>" alt="Foto profilo" class="profile-picture" style="object-fit:cover;">
                <?php else: ?>
                    <div class="profile-picture"></div>
                <?php endif; ?>
                <div class="profile-info">
                    <span class="user-name"><?php echo htmlspecialchars($nome_utente); ?></span>
                    <span class="user-role"><?php echo ($ruolo == 2) ? 'Commissione' : 'Docente'; ?></span>
                </div>
                <div class="profile-arrow" id="frecciaTendina">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="white">
                        <path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/>
                    </svg>
                </div>
            </div>

            <div class="profile-modal hidden" id="menuTendina">
                <a href="profilo.php" class="profile-modal-row">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                        <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z"/>
                    </svg>
                    <p>Profilo</p>
                </a>
                <hr>
                <a href="manuale.php" class="profile-modal-row">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                        <path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-560v-160H240v640h480v-480H520ZM240-800v160-160 640-640Z"/>
                    </svg>
                    <p>Manuale d'uso</p>
                </a>
                <hr>
                <a href="logout.php" class="profile-modal-row profile-modal-row-exit">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24">
                        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z"/>
                    </svg>
                    <p>Esci</p>
                </a>
            </div>
        <?php endif; ?>
    </div>
</header>

<script>
    function toggleMenuTendina(e) {
        if (e) e.stopPropagation();
        var menu = document.getElementById('menuTendina');
        var freccia = document.getElementById('frecciaTendina');
        if (menu) menu.classList.toggle('hidden');
        if (freccia) freccia.classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        var menu = document.getElementById('menuTendina');
        var pulsante = document.getElementById('pulsanteProfilo');
        var freccia = document.getElementById('frecciaTendina');
        if (menu && pulsante && !pulsante.contains(e.target)) {
            menu.classList.add('hidden');
            if (freccia) freccia.classList.remove('open');
        }
    });
</script>
