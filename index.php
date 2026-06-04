
<?php
session_start();
require_once('config.php');

// Recupero dei dati di sessione con controlli espliciti
$ruolo = 0;
if (isset($_SESSION['ruolo'])) {
    $ruolo = $_SESSION['ruolo'];
}

$nome_utente = '';
if (isset($_SESSION['username'])) {
    $nome_utente = $_SESSION['username'];
}

$idUtente = 0;
if (isset($_SESSION['id_utente'])) {
    $idUtente = (int)$_SESSION['id_utente'];
}

// Inizializzazione contatori
$totProposte     = 0;
$totOrg          = 0;
$totInProgramma  = 0;
$totBozze        = 0;

// Se l'utente è loggato, calcola le statistiche
if ($ruolo > 0) {
    // 1. Conteggio Proposte e Gite in Organizzazione per le Gite di 1 Giorno
    $res1 = $conn->query("SELECT COUNT(CASE WHEN idStato IN (1,2,3) THEN 1 END) AS prop, COUNT(CASE WHEN idStato = 4 THEN 1 END) AS org FROM gita1g WHERE idUtente = $idUtente");
    $c1 = array();
    if ($res1) {
        $c1 = $res1->fetch_assoc();
    }
    
    // 2. Conteggio Proposte e Gite in Organizzazione per le Gite di 5 Giorni
    $res5 = $conn->query("SELECT COUNT(CASE WHEN idStato IN (1,2,3) THEN 1 END) AS prop, COUNT(CASE WHEN idStato = 4 THEN 1 END) AS org FROM gite5 WHERE idUtente = $idUtente");
    $c5 = array();
    if ($res5) {
        $c5 = $res5->fetch_assoc();
    }

    // Somma parziale proposte
    $prop1 = 0;
    if (isset($c1['prop'])) { $prop1 = $c1['prop']; }
    
    $prop5 = 0;
    if (isset($c5['prop'])) { $prop5 = $c5['prop']; }
    
    $totProposte = $prop1 + $prop5;

    // Somma parziale organizzazione
    $org1 = 0;
    if (isset($c1['org'])) { $org1 = $c1['org']; }
    
    $org5 = 0;
    if (isset($c5['org'])) { $org5 = $c5['org']; }
    
    $totOrg = $org1 + $org5;

    // 3. Conteggio Gite In Programma (Stato 4)
    $resProg1 = $conn->query("SELECT COUNT(*) AS tot FROM gita1g WHERE idStato = 4");
    $prog1 = 0;
    if ($resProg1) {
        $r1 = $resProg1->fetch_assoc();
        $prog1 = $r1['tot'];
    }

    $resProg5 = $conn->query("SELECT COUNT(*) AS tot FROM gite5 WHERE idStato = 4");
    $prog5 = 0;
    if ($resProg5) {
        $r5 = $resProg5->fetch_assoc();
        $prog5 = $r5['tot'];
    }
    $totInProgramma = $prog1 + $prog5;

    // 4. Se l'utente è della Commissione, conta le Bozze in attesa (Stato 1)
    if ($ruolo == 2) {
        $resBozze1 = $conn->query("SELECT COUNT(*) AS tot FROM gita1g WHERE idStato = 1");
        $bozze1 = 0;
        if ($resBozze1) {
            $rb1 = $resBozze1->fetch_assoc();
            $bozze1 = $rb1['tot'];
        }

        $resBozze5 = $conn->query("SELECT COUNT(*) AS tot FROM gite5 WHERE idStato = 1");
        $bozze5 = 0;
        if ($resBozze5) {
            $rb5 = $resBozze5->fetch_assoc();
            $bozze5 = $rb5['tot'];
        }
        $totBozze = $bozze1 + $bozze5;
    }
}

// Preparazione delle classi CSS dinamiche per evitare ternari nell'HTML
$gridClass = 'grid-3-cols';
if ($ruolo == 2) {
    $gridClass = 'grid-4-cols';
}

$coloreBozze = 'var(--blue-600)';
if ($totBozze > 0) {
    $coloreBozze = 'var(--hex-orange)';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Gite - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css?v=<?php echo time(); ?>">
    <script src="vetrina.js" defer></script>
</head>
<body>
    <?php include('nav.php'); ?>
    <div class="container">
        <main class="content home-padding">

<?php if (!$ruolo): ?>
            <!-- contenuto per utenti non loggati -->
            <div class="hero-section" style="text-align: center; margin-top: 5rem;">
                <h1 style="font-size: 3rem; margin-bottom: 1rem; color: var(--blue-700);">Sistema Gestione Gite</h1>
                <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto; color: #475569;">Benvenuto nel portale per l'organizzazione dei viaggi d'istruzione. Accedi con il tuo account del Portale Calvino per iniziare.</p>
            </div>
            <div style="max-width: 450px; margin: 3rem auto 6rem;">
                <div class="card centered" style="padding: 2.5rem 2rem; box-shadow: 0 4px 20px rgba(59, 130, 246, 0.1);">
                    <div class="card-header" style="border: none; padding-bottom: 0; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.5rem; margin: 0;">Accedi</h3>
                    </div>
                    <div class="card-body" style="margin-bottom: 1.5rem;">
                        <p style="font-size: 1rem; color: #64748b; margin: 0;">Effettua il login tramite il Portale Calvino per accedere alle funzionalità.</p>
                    </div>
                    <div class="card-footer" style="justify-content: center; border: none; padding-top: 0; margin-top: 0;">
                        <a href="login.php" class="button full-width home-button" style="height: 3.5rem; font-size: 1.1rem;">Accedi con Portale Calvino</a>
                    </div>
                </div>
            </div>
<?php else: ?>
            <!-- contenuto per utenti loggati -->
            <div class="hero-section" style="margin-top: 2rem; margin-bottom: 3rem;">
                <h1 style="font-size:2.5rem;font-weight:700;margin-bottom:0.5rem;color:var(--blue-700);">Benvenuto, <?php echo htmlspecialchars(explode(' ', $nome_utente)[0]); ?></h1>
                <p style="font-size:1.15rem;color:#475569;margin-bottom:0;max-width:600px;">
                    <?php if ($ruolo == 2): ?>
                        Gestisci le proposte, approva le bozze e organizza le gite scolastiche.
                    <?php else: ?>
                        Proponi nuove gite, organizza quelle approvate e segui lo stato delle tue proposte.
                    <?php endif; ?>
                </p>
            </div>
            <!-- riepilogo numerico -->
            <div class="home-grid <?php echo $gridClass; ?>" style="margin-bottom: 2rem; margin-top: 0;">
                <div class="card stat-card" onclick="window.location.href='mieGite.php'" style="text-align:center;padding:1.2rem;">
                    <span style="font-size:2rem;font-weight:700;color:var(--blue-600);"><?php echo $totProposte; ?></span>
                    <p style="font-size:0.85rem;color:var(--my-gray);margin-top:0.3rem;">Le mie proposte</p>
                </div>
                <div class="card stat-card" onclick="window.location.href='mieGite.php'" style="text-align:center;padding:1.2rem;">
                    <span style="font-size:2rem;font-weight:700;color:var(--blue-600);"><?php echo $totOrg; ?></span>
                    <p style="font-size:0.85rem;color:var(--my-gray);margin-top:0.3rem;">In organizzazione</p>
                </div>
                <div class="card stat-card" onclick="window.location.href='inProgramma.php'" style="text-align:center;padding:1.2rem;">
                    <span style="font-size:2rem;font-weight:700;color:var(--blue-600);"><?php echo $totInProgramma; ?></span>
                    <p style="font-size:0.85rem;color:var(--my-gray);margin-top:0.3rem;">Gite in programma</p>
                </div>
                <?php if ($ruolo == 2): ?>
                <div class="card stat-card" onclick="window.location.href='elencoBozze.php'" style="text-align:center;padding:1.2rem;">
                    <span style="font-size:2rem;font-weight:700;color:<?php echo $coloreBozze; ?>;"><?php echo $totBozze; ?></span>
                    <p style="font-size:0.85rem;color:var(--my-gray);margin-top:0.3rem;">Bozze in attesa</p>
                </div>
                <?php endif; ?>
            </div>
            <!-- card navigazione -->
            <div class="home-grid <?php echo $gridClass; ?>" style="margin-top: 2rem;">
                <div class="card">
                    <div class="card-header">
                        <h3>Proposte</h3>
                    </div>
                    <div class="card-body">
                        <p>Consulta le proposte approvate e organizza una nuova gita.</p>
                    </div>
                    <div class="card-footer">
                        <a href="catalogo.php" class="button">Vai alle Proposte</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Le Mie Gite</h3>
                    </div>
                    <div class="card-body">
                        <p>Visualizza le gite che hai proposto o che stai organizzando.</p>
                    </div>
                    <div class="card-footer">
                        <a href="mieGite.php" class="button">Vai alle Mie Gite</a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Gite in Programma</h3>
                    </div>
                    <div class="card-body">
                        <p>Elenco di tutte le gite attualmente in organizzazione.</p>
                    </div>
                    <div class="card-footer">
                        <a href="inProgramma.php" class="button">Vedi Programma</a>
                    </div>
                </div>
                <?php if ($ruolo == 2): ?>
                <div class="card">
                    <div class="card-header">
                        <h3>Bozze in Attesa</h3>
                    </div>
                    <div class="card-body">
                        <p>Approva o boccia le proposte inviate dai docenti.</p>
                    </div>
                    <div class="card-footer">
                        <a href="elencoBozze.php" class="button">Gestisci Bozze</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
<?php endif; ?>

        </main>
        <?php include('footer.php'); ?>
    </div>
</body>
</html>
