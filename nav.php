<?php
    @session_start();
    require_once('config.php');

    $nome_utente = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utente Sconosciuto';
    $ruolo = isset($_SESSION['ruolo']) ? $_SESSION['ruolo'] : null;
    $foto_utente = isset($_SESSION['foto']) ? $_SESSION['foto'] : '';
    $paginaCorrente = basename($_SERVER['PHP_SELF']);

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
    <div class="header-container header-left" style="flex: 1; flex-basis: 0;">
        <h2>Gestione Gite</h2>
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
