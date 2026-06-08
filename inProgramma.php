<?php
include('nav.php');
include('utils.php');

$ruolo = 0;
if (isset($_SESSION['ruolo'])) {
    $ruolo = $_SESSION['ruolo'];
}

$idUtenteLoggato = 0;
if (isset($_SESSION['id_utente'])) {
    $idUtenteLoggato = (int)$_SESSION['id_utente'];
}

// partecipa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'partecipa') {
    $idGita = 0;
    if (isset($_POST['id_gita'])) {
        $idGita = (int)$_POST['id_gita'];
    }
    
    $tipoGita = '1g';
    if (isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === 'gite5') {
        $tipoGita = '5g';
    }
    
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        mysqli_query($conn, "INSERT IGNORE INTO accompagnatori (idgita, idutente, tipo_gita) VALUES ($idGita, $idUtenteLoggato, '$tipoGita')");
    }
    header("Location: inProgramma.php?partecipato=1");
    exit;
}

// disiscriviti
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'disiscriviti') {
    $idGita = 0;
    if (isset($_POST['id_gita'])) {
        $idGita = (int)$_POST['id_gita'];
    }
    
    $tipoGita = '1g';
    if (isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === 'gite5') {
        $tipoGita = '5g';
    }
    
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        mysqli_query($conn, "DELETE FROM accompagnatori WHERE idgita = $idGita AND idutente = $idUtenteLoggato AND tipo_gita = '$tipoGita'");
    }
    header("Location: inProgramma.php?disiscritto=1");
    exit;
}

// elimina solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina' && $ruolo == 2) {
    $idGita = 0;
    if (isset($_POST['id_gita'])) {
        $idGita = (int)$_POST['id_gita'];
    }
    
    $tab = 'gita1g';
    if ($_POST['tabella'] === 'gite5') {
        $tab = 'gite5';
    }

    if ($idGita > 0) {
        $tipoDel = '1g';
        if ($tab === 'gite5') {
            $tipoDel = '5g';
        }
        mysqli_query($conn, "DELETE FROM $tab WHERE idGita = $idGita");
        mysqli_query($conn, "DELETE FROM accompagnatori WHERE idgita = $idGita AND tipo_gita = '$tipoDel'");
    }
    header("Location: inProgramma.php");
    exit;
}

// modifica gita 1g solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_1g' && $ruolo == 2) {
    $id = 0;
    if (isset($_POST['id_gita'])) {
        $id = (int)$_POST['id_gita'];
    }
    
    $dest = '';
    if (isset($_POST['destinazione'])) {
        $dest = mysqli_real_escape_string($conn, trim($_POST['destinazione']));
    }
    
    $desc = '';
    if (isset($_POST['descrizione'])) {
        $desc = mysqli_real_escape_string($conn, trim($_POST['descrizione']));
    }
    
    $mezz = '';
    if (isset($_POST['mezzo'])) {
        $mezz = mysqli_real_escape_string($conn, trim($_POST['mezzo']));
    }
    
    $per = '';
    if (isset($_POST['periodo'])) {
        $per = mysqli_real_escape_string($conn, trim($_POST['periodo']));
    }
    
    $cls = '';
    if (isset($_POST['classi'])) {
        $cls = mysqli_real_escape_string($conn, trim($_POST['classi']));
    }
    
    $gio = '';
    if (isset($_POST['giorno'])) {
        $gio = trim($_POST['giorno']);
    }

    // validazione dati
    if ($gio) {
        if (strtotime($gio) === false || (int)date('Y', strtotime($gio)) < 2024 || (int)date('Y', strtotime($gio)) > 2030) {
            $gio = '';
        }
    }
    if ($gio) {
        if (strtotime($gio) <= strtotime(date('Y-m-d'))) {
            $gio = '';
        }
    }

    $gio_s = "NULL";
    if ($gio) {
        $gio_s = "'" . $conn->real_escape_string($gio) . "'";
    }
    
    $costoM = 'NULL';
    if (isset($_POST['costoMezzo']) && $_POST['costoMezzo'] !== '') {
        $costoM = (float)$_POST['costoMezzo'];
    }
    
    $costoA = 'NULL';
    if (isset($_POST['costoAttivita']) && $_POST['costoAttivita'] !== '') {
        $costoA = (float)$_POST['costoAttivita'];
    }
    
    $costoP = 'NULL';
    if (isset($_POST['costoAPersona']) && $_POST['costoAPersona'] !== '') {
        $costoP = (float)$_POST['costoAPersona'];
    }
    
    $numAl = 'NULL';
    if (isset($_POST['numAlunni']) && $_POST['numAlunni'] !== '') {
        $numAl = (int)$_POST['numAlunni'];
    }
    
    if ($id > 0) {
        mysqli_query($conn, "UPDATE gita1g SET destinazione='$dest', descrizione='$desc', mezzo='$mezz', periodo='$per', classi='$cls', giorno=$gio_s, costoMezzo=$costoM, costoAttivita=$costoA, costoAPersona=$costoP, numAlunni=$numAl WHERE idGita=$id");
    }
    header("Location: inProgramma.php");
    exit;
}

// modifica gita 5g solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_5g' && $ruolo == 2) {
    $id = 0;
    if (isset($_POST['id_gita'])) {
        $id = (int)$_POST['id_gita'];
    }
    
    $dest = '';
    if (isset($_POST['destinazione'])) {
        $dest = mysqli_real_escape_string($conn, trim($_POST['destinazione']));
    }
    
    $desc = '';
    if (isset($_POST['descrizione'])) {
        $desc = mysqli_real_escape_string($conn, trim($_POST['descrizione']));
    }
    
    $mezz = '';
    if (isset($_POST['mezzo'])) {
        $mezz = mysqli_real_escape_string($conn, trim($_POST['mezzo']));
    }
    
    $per = '';
    if (isset($_POST['periodo'])) {
        $per = mysqli_real_escape_string($conn, trim($_POST['periodo']));
    }
    
    $cls = '';
    if (isset($_POST['classi'])) {
        $cls = mysqli_real_escape_string($conn, trim($_POST['classi']));
    }
    
    $gi = '';
    if (isset($_POST['giornoInizio'])) {
        $gi = trim($_POST['giornoInizio']);
    }
    
    $gf = '';
    if (isset($_POST['giornoFine'])) {
        $gf = trim($_POST['giornoFine']);
    }

    // validazione dati
    if ($gi) {
        if (strtotime($gi) === false || (int)date('Y', strtotime($gi)) < 2024 || (int)date('Y', strtotime($gi)) > 2030) {
            $gi = '';
        }
    }
    
    if ($gf) {
        if (strtotime($gf) === false || (int)date('Y', strtotime($gf)) < 2024 || (int)date('Y', strtotime($gf)) > 2030) {
            $gf = '';
        }
    }
    
    if ($gi) {
        if (strtotime($gi) <= strtotime(date('Y-m-d'))) {
            $gi = '';
        }
    }
    
    if ($gi && $gf) {
        if (strtotime($gi) >= strtotime($gf)) {
            $gi = '';
            $gf = '';
        }
    }

    $gi_s = "NULL";
    if ($gi) {
        $gi_s = "'" . $conn->real_escape_string($gi) . "'";
    }
    
    $gf_s = "NULL";
    if ($gf) {
        $gf_s = "'" . $conn->real_escape_string($gf) . "'";
    }
    
    $costoP = 'NULL';
    if (isset($_POST['costoAPersona']) && $_POST['costoAPersona'] !== '') {
        $costoP = (float)$_POST['costoAPersona'];
    }
    
    $numAl = 'NULL';
    if (isset($_POST['numAlunni']) && $_POST['numAlunni'] !== '') {
        $numAl = (int)$_POST['numAlunni'];
    }
    
    if ($id > 0) {
        mysqli_query($conn, "UPDATE gite5 SET destinazione='$dest', descrizione='$desc', mezzo='$mezz', periodo='$per', classi='$cls', giornoInizio=$gi_s, giornoFine=$gf_s, costoAPersona=$costoP, numAlunni=$numAl WHERE idGita=$id");
    }
    header("Location: inProgramma.php");
    exit;
}

// query gite 1 giorno (stato 4 = in programma, stato 5 = concluse)
$res1g = mysqli_query($conn,
    "SELECT g.*, CONCAT(u.Nome, ' ', u.Cognome) AS autore
     FROM gita1g g JOIN utente u ON g.idUtente = u.IDUtente
     WHERE g.idStato IN (4, 5) ORDER BY g.idStato ASC, g.giorno ASC"
);

// query gite piu giorni (stato 4 = in programma, stato 5 = concluse)
$res5g = mysqli_query($conn,
    "SELECT g.*, CONCAT(u.Nome, ' ', u.Cognome) AS autore
     FROM gite5 g JOIN utente u ON g.idUtente = u.IDUtente
     WHERE g.idStato IN (4, 5) ORDER BY g.idStato ASC, g.giornoInizio ASC"
);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gite in Programma</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css?v=<?php echo time(); ?>">
    <script src="vetrina.js" defer></script>
</head>
<body>
<div class="container">
<main class="content bozze-padding">

<?php if (isset($_GET['partecipato']) && $_GET['partecipato'] === '1'): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">
    Hai aderito alla gita come accompagnatore. La trovi ora in <a href="mieGite.php"><strong>Le Mie Gite</strong></a>.
</div>
<?php endif; ?>

<?php if (isset($_GET['disiscritto']) && $_GET['disiscritto'] === '1'): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">
    Ti sei disiscritto correttamente dalla gita. Puoi riscriverti dalla pagina <a href="inProgramma.php" style="color:inherit;text-decoration:underline;font-weight:bold;">In Programma</a>.
</div>
<?php endif; ?>

<div style="display: flex; justify-content: flex-start; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem; margin-bottom: 1.5rem;">
    <div class="search-bar-wrapper" style="margin-bottom: 0; max-width: 380px; width: 100%;">
        <input type="text" id="cercaGite" onkeyup="cercaInTabelle('cercaGite', 'table')" placeholder="Cerca destinazione, mezzo, periodo, docente...">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
            <path d="M784-120 533-371q-30 24-74 37.5T367-320q-101 0-171-70t-70-171q0-101 70-171t171-70q101 0 171 70t70 171q0 48-13.5 92T533-533l251 251-50 50ZM367-400q67 0 113.5-46.5T527-560q0-67-46.5-113.5T367-720q-67 0-113.5 46.5T207-560q0 67 46.5 113.5T367-400Z"/>
        </svg>
    </div>
    <a href="#gite-quinte" class="button outline" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; border-radius: 99px; padding: 0.6rem 1.2rem; font-size: 0.88rem; transition: all 0.2s ease; height: auto; min-width: 0; line-height: 1;">
        <span>vai a gite per le quinte</span>
        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor">
            <path d="M440-800v487L216-537l-56 57 320 320 320-320-56-57-224 224v-487h-80Z"/>
        </svg>
    </a>
</div>

<!-- gite 1 giorno -->
<h3 style="color:var(--blue-700);margin-bottom:0.75rem;">Gite di un giorno</h3>
<div class="table-section"><div class="table-container">
<table>
<thead><tr>
    <th>Destinazione</th><th>Descrizione</th><th>Stato</th><th>Mezzo</th><th>Classi</th>
    <th>Giorno</th><th>Costo/Persona</th><th>N. Alunni</th><th>Docente</th><th>Azioni</th>
</tr></thead>
<tbody>
<?php
if ($res1g && mysqli_num_rows($res1g) > 0):
    while ($riga = mysqli_fetch_assoc($res1g)):
        $id = (int)$riga['idGita'];
        
        $dest = '';
        if (isset($riga['destinazione'])) {
            $dest = htmlspecialchars($riga['destinazione']);
        }
        
        $mezzo = '';
        if (isset($riga['mezzo'])) {
            $mezzo = htmlspecialchars($riga['mezzo']);
        }
        
        $classi = '';
        if (isset($riga['classi'])) {
            $classi = htmlspecialchars($riga['classi']);
        }
        
        $giorno = '—';
        if ($riga['giorno']) {
            if (strtotime($riga['giorno']) !== false) {
                $giorno = date('d/m/Y', strtotime($riga['giorno']));
            }
        }
        
        $giornoV = '';
        if (isset($riga['giorno'])) {
            $giornoV = $riga['giorno'];
        }
        
        $costo = '—';
        if ($riga['costoAPersona'] !== null) {
            $costo = '&euro; ' . number_format($riga['costoAPersona'], 2, ',', '.');
        }
        
        $numAl = '';
        if (isset($riga['numAlunni'])) {
            $numAl = $riga['numAlunni'];
        }
        
        $autore = '';
        if (isset($riga['autore'])) {
            $autore = htmlspecialchars($riga['autore']);
        }
        
        $costoMV = '';
        if (isset($riga['costoMezzo'])) {
            $costoMV = $riga['costoMezzo'];
        }
        
        $costoAV = '';
        if (isset($riga['costoAttivita'])) {
            $costoAV = $riga['costoAttivita'];
        }
        
        $costoPA = '';
        if (isset($riga['costoAPersona'])) {
            $costoPA = $riga['costoAPersona'];
        }
        
        $dJ = '';
        if (isset($riga['destinazione'])) {
            $dJ = htmlspecialchars($riga['destinazione']);
        }
        
        $descDisp = '';
        if (isset($riga['descrizione'])) {
            $descDisp = htmlspecialchars($riga['descrizione']);
        }
        // iscritto se è accompagnatore OPPURE se è l'autore della gita
        $chk = mysqli_query($conn,
        "SELECT id
        FROM accompagnatori
        WHERE idgita=$id AND idutente=$idUtenteLoggato AND tipo_gita='1g'");
        $isAccompagnatore = ($chk && mysqli_num_rows($chk) > 0);
        $isAutore = ($riga['idUtente'] == $idUtenteLoggato);
        $outDesc = '—';
        if ($descDisp) { $outDesc = $descDisp; }
        
        $outMezzo = '—';
        if (!empty($mezzo)) { $outMezzo = $mezzo; }
        
        $outClassi = '—';
        if (!empty($classi)) { $outClassi = $classi; }
        
        $giornoAttr = null;
        if (isset($riga['giorno'])) { $giornoAttr = $riga['giorno']; }
?>
    <tr>
        <td style="white-space:normal;"><?php echo $dest; ?></td>
        <td style="white-space:normal; max-width:200px;"><?php echo $outDesc; ?></td>
        <td><?php echo badgeStatoHtml($riga['idStato'], $giornoAttr); ?></td>
        <td><?php echo $outMezzo; ?></td>
        <td><?php echo $outClassi; ?></td>
        <td><?php echo $giorno; ?></td>
        <td><?php echo $costo; ?></td>
        <td><?php echo !empty($numAl) ? $numAl : '—'; ?></td>
        <td><?php echo $autore; ?></td>
        <td>
            <div class="azioni-cell">
                <?php if ($riga['idStato'] == 4): ?>
                <?php if ($isAccompagnatore): ?>
                    <button type="button" class="button cancel xs" onclick="apriConferma('disiscriviti', <?php echo $id; ?>, 'gita1g', '<?php echo htmlspecialchars($dJ); ?>')">Disiscriviti</button>
                <?php else: ?>
                    <button type="button" class="button xs" onclick="apriConferma('partecipa', <?php echo $id; ?>, 'gita1g', '<?php echo htmlspecialchars($dJ); ?>')">Partecipa</button>
                <?php endif; ?>
                <?php if ($ruolo == 2): ?>
                <button type="button" class="button xs"
                    data-id="<?php echo $id; ?>"
                    data-dest="<?php echo $dJ; ?>"
                    data-desc="<?php echo htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : ''); ?>"
                    data-mezzo="<?php echo htmlspecialchars(isset($riga['mezzo']) ? $riga['mezzo'] : ''); ?>"
                    data-periodo="<?php echo htmlspecialchars(isset($riga['periodo']) ? $riga['periodo'] : ''); ?>"
                    data-classi="<?php echo htmlspecialchars(isset($riga['classi']) ? $riga['classi'] : ''); ?>"
                    data-giorno="<?php echo $giornoV; ?>"
                    data-costo-mezzo="<?php echo $costoMV; ?>"
                    data-costo-att="<?php echo $costoAV; ?>"
                    data-costo-ap="<?php echo $costoPA; ?>"
                    data-num-alunni="<?php echo $numAl; ?>"
                    onclick="apriMod1g(this)">Modifica</button>
                <button type="button" class="button cancel xs"
                    data-id="<?php echo $id; ?>"
                    data-dest="<?php echo $dJ; ?>"
                    data-tab="gita1g"
                    onclick="apriElimina(this)">Elimina</button>
                <?php endif; ?>
                <?php else: ?>
                    <span style="color:#94a3b8;font-size:0.85rem;">—</span>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endwhile; else: ?>
    <tr><td colspan="9" style="text-align:center;color:#94a3b8;">Nessuna gita di 1 giorno al momento.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div></div>

<!-- gite piu giorni -->
<h3 id="gite-quinte" style="color:var(--blue-700);margin:2rem 0 0.75rem;">Gite per le quinte</h3>
<div class="table-section"><div class="table-container table-quinte">
<table>
<thead><tr>
    <th>Destinazione</th><th>Descrizione</th><th>Stato</th><th>Mezzo</th><th>Classi</th>
    <th>Dal</th><th>Al</th><th>Costo/Persona</th><th>N. Alunni</th><th>Docente</th><th>Azioni</th>
</tr></thead>
<tbody>
<?php
if ($res5g && mysqli_num_rows($res5g) > 0):
    while ($riga = mysqli_fetch_assoc($res5g)):
        $id     = (int)($riga['idGita']);
        $dest = '';
        if (isset($riga['destinazione'])) {
            $dest = htmlspecialchars($riga['destinazione']);
        }
        
        $mezzo = '';
        if (isset($riga['mezzo'])) {
            $mezzo = htmlspecialchars($riga['mezzo']);
        }
        
        $classi = '';
        if (isset($riga['classi'])) {
            $classi = htmlspecialchars($riga['classi']);
        }
        
        $gi = '—';
        if ($riga['giornoInizio']) {
            if (strtotime($riga['giornoInizio']) !== false) {
                $gi = date('d/m/Y', strtotime($riga['giornoInizio']));
            }
        }
        
        $gf = '—';
        if ($riga['giornoFine']) {
            if (strtotime($riga['giornoFine']) !== false) {
                $gf = date('d/m/Y', strtotime($riga['giornoFine']));
            }
        }
        
        $giV = '';
        if (isset($riga['giornoInizio'])) {
            $giV = $riga['giornoInizio'];
        }
        
        $gfV = '';
        if (isset($riga['giornoFine'])) {
            $gfV = $riga['giornoFine'];
        }
        
        $costo = '—';
        if ($riga['costoAPersona'] !== null) {
            $costo = '&euro; ' . number_format($riga['costoAPersona'], 2, ',', '.');
        }
        
        $numAl = '';
        if (isset($riga['numAlunni'])) {
            $numAl = $riga['numAlunni'];
        }
        
        $autore = '';
        if (isset($riga['autore'])) {
            $autore = htmlspecialchars($riga['autore']);
        }
        
        $costoPA = '';
        if (isset($riga['costoAPersona'])) {
            $costoPA = $riga['costoAPersona'];
        }
        
        $dJ = '';
        if (isset($riga['destinazione'])) {
            $dJ = htmlspecialchars($riga['destinazione']);
        }
        
        $descDisp = '';
        if (isset($riga['descrizione'])) {
            $descDisp = htmlspecialchars($riga['descrizione']);
        }
        
        // Preparazione per HTML per rimuovere ternari
        $outDesc = '—';
        if ($descDisp) { $outDesc = $descDisp; }
        
        $outMezzo = '—';
        if (!empty($mezzo)) { $outMezzo = $mezzo; }
        
        $outClassi = '—';
        if (!empty($classi)) { $outClassi = $classi; }
        
        $giAttr = null;
        if (isset($riga['giornoInizio'])) { $giAttr = $riga['giornoInizio']; }
        
        $gfAttr = null;
        if (isset($riga['giornoFine'])) { $gfAttr = $riga['giornoFine']; }

        // iscritto se è accompagnatore OPPURE se è l'autore della gita
        $chk = mysqli_query($conn, "SELECT id FROM accompagnatori WHERE idgita=$id AND idutente=$idUtenteLoggato AND tipo_gita='5g'");
        $isAccompagnatore = false;
        if ($chk) {
            if (mysqli_num_rows($chk) > 0) {
                $isAccompagnatore = true;
            }
        }
        $isAutore = false;
        if ($riga['idUtente'] == $idUtenteLoggato) {
            $isAutore = true;
        }
?>
    <tr>
        <td style="white-space:normal;"><?php echo $dest; ?></td>
        <td style="white-space:normal; max-width:200px;"><?php echo $outDesc; ?></td>
        <td><?php echo badgeStatoHtml($riga['idStato'], $giAttr, $gfAttr); ?></td>
        <td><?php echo $outMezzo; ?></td>
        <td><?php echo $outClassi; ?></td>
        <td><?php echo $gi; ?></td>
        <td><?php echo $gf; ?></td>
        <td><?php echo $costo; ?></td>
        <td><?php echo !empty($numAl) ? $numAl : '—'; ?></td>
        <td><?php echo $autore; ?></td>
        <td>
            <div class="azioni-cell">
                <?php if ($riga['idStato'] == 4): ?>
                <?php if ($isAccompagnatore): ?>
                    <button type="button" class="button cancel xs" onclick="apriConferma('disiscriviti', <?php echo $id; ?>, 'gite5', '<?php echo htmlspecialchars($dJ); ?>')">Disiscriviti</button>
                <?php else: ?>
                    <button type="button" class="button xs" onclick="apriConferma('partecipa', <?php echo $id; ?>, 'gite5', '<?php echo htmlspecialchars($dJ); ?>')">Partecipa</button>
                <?php endif; ?>
                <?php if ($ruolo == 2): ?>
                <button type="button" class="button xs"
                    data-id="<?php echo $id; ?>"
                    data-dest="<?php echo $dJ; ?>"
                    data-desc="<?php echo htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : ''); ?>"
                    data-mezzo="<?php echo htmlspecialchars(isset($riga['mezzo']) ? $riga['mezzo'] : ''); ?>"
                    data-periodo="<?php echo htmlspecialchars(isset($riga['periodo']) ? $riga['periodo'] : ''); ?>"
                    data-classi="<?php echo htmlspecialchars(isset($riga['classi']) ? $riga['classi'] : ''); ?>"
                    data-gi="<?php echo $giV; ?>"
                    data-gf="<?php echo $gfV; ?>"
                    data-costo-ap="<?php echo $costoPA; ?>"
                    data-num-alunni="<?php echo $numAl; ?>"
                    onclick="apriMod5g(this)">Modifica</button>
                <button type="button" class="button cancel xs"
                    data-id="<?php echo $id; ?>"
                    data-dest="<?php echo $dJ; ?>"
                    data-tab="gite5"
                    onclick="apriElimina(this)">Elimina</button>
                <?php endif; ?>
                <?php else: ?>
                    <span style="color:#94a3b8;font-size:0.85rem;">—</span>
                <?php endif; ?>
            </div>
        </td>
    </tr>
<?php endwhile; else: ?>
    <tr><td colspan="10" style="text-align:center;color:#94a3b8;">
        Nessuna gita di più giorni al momento.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div></div>

</main>

<!-- modal: conferma elimina -->
<div class="modal-overlay hidden" id="modalElimina">
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="chiudi('modalElimina')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--hex-red);margin-bottom:0.5rem;">Conferma Eliminazione</h3>
    <p style="color:var(--blue-900);margin-bottom:0.5rem;">Stai per eliminare la gita:</p>
    <p style="font-weight:600;color:var(--blue-700);font-size:1.1rem;margin-bottom:0.5rem;" id="elimDest"></p>
    <p style="color:#64748b;font-size:0.9rem;">Questa azione non può essere annullata.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button cancel-outline" onclick="chiudi('modalElimina')">Annulla</button>
    <form id="formElimina" method="POST" action="inProgramma.php" style="margin:0;">
        <input type="hidden" name="action"  value="elimina">
        <input type="hidden" name="id_gita" id="elimId">
        <input type="hidden" name="tabella" id="elimTab">
        <button type="submit" class="button cancel">Sì, elimina</button>
    </form>
</div>
</div>
</div>

<!-- modal: modifica gita 1 giorno -->
<div class="modal-overlay hidden" id="modalMod1g">
<div class="modal wide-modal">
<div class="modal-header">
    <h3 id="modTit1g">Modifica Gita 1 Giorno</h3>
    <button class="close-btn" onclick="chiudi('modalMod1g')">&times;</button>
</div>
<div class="modal-body">
<form id="formMod1g" method="POST" action="inProgramma.php">
    <input type="hidden" name="action"  value="modifica_1g">
    <input type="hidden" name="id_gita" id="m1g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="destinazione" id="m1g_dest" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="descrizione" id="m1g_desc" class="form-control" placeholder="Breve descrizione">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <select name="mezzo" id="m1g_mezzo" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Bus">Bus</option>
                <option value="Treno">Treno</option>
                <option value="Ci incontriamo direttamente li">Ci incontriamo direttamente lì</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="periodo" id="m1g_periodo" class="form-control">
        </div>
        <div class="form-group">
            <label>Classe/i</label>
            <input type="text" name="classi" id="m1g_classi" class="form-control">
        </div>
        <div class="form-group">
            <label>Giorno</label>
            <input type="date" name="giorno" id="m1g_giorno" class="form-control" min="2024-01-01" max="2030-12-31">
        </div>
        <div class="form-group">
            <label>Costo Mezzo (&euro;)</label>
            <input type="number" name="costoMezzo" id="m1g_costoMezzo" class="form-control" step="0.50" min="0">
        </div>
        <div class="form-group">
            <label>Costo Attività (&euro;)</label>
            <input type="number" name="costoAttivita" id="m1g_costoAtt" class="form-control" step="0.50" min="0">
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;)</label>
            <input type="number" name="costoAPersona" id="m1g_costoAP" class="form-control" step="0.50" min="0">
        </div>
        <div class="form-group">
            <label>Num. Alunni</label>
            <input type="number" name="numAlunni" id="m1g_numAlunni" class="form-control" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="chiudi('modalMod1g')">Annulla</button>
    <button type="submit" form="formMod1g" class="button">Salva modifiche</button>
</div>
</div>
</div>

<!-- modal: modifica gita piu giorni -->
<div class="modal-overlay hidden" id="modalMod5g">
<div class="modal wide-modal">
<div class="modal-header">
    <h3 id="modTit5g">Modifica Gita Più Giorni</h3>
    <button class="close-btn" onclick="chiudi('modalMod5g')">&times;</button>
</div>
<div class="modal-body">
<form id="formMod5g" method="POST" action="inProgramma.php">
    <input type="hidden" name="action"  value="modifica_5g">
    <input type="hidden" name="id_gita" id="m5g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="destinazione" id="m5g_dest" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="descrizione" id="m5g_desc" class="form-control" placeholder="Breve descrizione">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <select name="mezzo" id="m5g_mezzo" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Bus GT">Bus GT</option>
                <option value="Treno">Treno</option>
                <option value="Aereo">Aereo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="periodo" id="m5g_periodo" class="form-control">
        </div>
        <div class="form-group">
            <label>Classe/i</label>
            <input type="text" name="classi" id="m5g_classi" class="form-control">
        </div>
        <div class="form-group">
            <label>Giorno Inizio</label>
            <input type="date" name="giornoInizio" id="m5g_gi" class="form-control" min="2024-01-01" max="2030-12-31">
        </div>
        <div class="form-group">
            <label>Giorno Fine</label>
            <input type="date" name="giornoFine" id="m5g_gf" class="form-control" min="2024-01-01" max="2030-12-31">
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;)</label>
            <input type="number" name="costoAPersona" id="m5g_costoAP" class="form-control" step="0.50" min="0">
        </div>
        <div class="form-group">
            <label>Num. Alunni</label>
            <input type="number" name="numAlunni" id="m5g_numAlunni" class="form-control" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="chiudi('modalMod5g')">Annulla</button>
    <button type="submit" form="formMod5g" class="button">Salva modifiche</button>
</div>
</div>
</div>

<?php include('footer.php'); ?>
</div>

<script>
function chiudi(id) { document.getElementById(id).classList.add('hidden'); }

window.addEventListener('click', function(e) {
    ['modalElimina','modalMod1g','modalMod5g'].forEach(function(id) {
        var m = document.getElementById(id);
        if (e.target === m) m.classList.add('hidden');
    });
});

function apriElimina(btn) {
    var d = btn.dataset;
    document.getElementById('elimDest').textContent = d.dest;
    document.getElementById('elimId').value  = d.id;
    document.getElementById('elimTab').value = d.tab;
    document.getElementById('modalElimina').classList.remove('hidden');
}

function apriMod1g(btn) {
    var d = btn.dataset;
    document.getElementById('modTit1g').textContent    = 'Modifica: ' + d.dest;
    document.getElementById('m1g_id').value            = d.id;
    document.getElementById('m1g_dest').value          = d.dest;
    document.getElementById('m1g_desc').value          = d.desc       || '';
    document.getElementById('m1g_mezzo').value         = d.mezzo      || '';
    document.getElementById('m1g_periodo').value       = d.periodo    || '';
    document.getElementById('m1g_classi').value        = d.classi     || '';
    document.getElementById('m1g_giorno').value        = d.giorno     || '';
    document.getElementById('m1g_costoMezzo').value    = d.costoMezzo || '';
    document.getElementById('m1g_costoAtt').value      = d.costoAtt   || '';
    document.getElementById('m1g_costoAP').value       = d.costoAp    || '';
    document.getElementById('m1g_numAlunni').value     = d.numAlunni  || '';
    document.getElementById('modalMod1g').classList.remove('hidden');
}

function apriMod5g(btn) {
    var d = btn.dataset;
    document.getElementById('modTit5g').textContent    = 'Modifica: ' + d.dest;
    document.getElementById('m5g_id').value            = d.id;
    document.getElementById('m5g_dest').value          = d.dest;
    document.getElementById('m5g_desc').value          = d.desc      || '';
    document.getElementById('m5g_mezzo').value         = d.mezzo     || '';
    document.getElementById('m5g_periodo').value       = d.periodo   || '';
    document.getElementById('m5g_classi').value        = d.classi    || '';
    document.getElementById('m5g_gi').value            = d.gi        || '';
    document.getElementById('m5g_gf').value            = d.gf        || '';
    document.getElementById('m5g_costoAP').value       = d.costoAp   || '';
    document.getElementById('m5g_numAlunni').value     = d.numAlunni || '';
    document.getElementById('modalMod5g').classList.remove('hidden');
}
</script>

<!-- modal: conferma partecipa / disiscriviti -->
<div class="modal-overlay hidden" id="modalConfermaAzione">
<div class="modal" style="text-align:center;max-width:420px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalConfermaAzione').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 id="confermaTitolo" style="margin-bottom:0.5rem;"></h3>
    <p id="confermaMessaggio" style="color:#475569;"></p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button type="button" class="button cancel-outline" onclick="document.getElementById('modalConfermaAzione').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formConfermaAzione" id="confermaBtnSubmit" class="button">Conferma</button>
</div>
</div>
</div>
<form id="formConfermaAzione" method="POST" action="inProgramma.php" style="display:none;">
    <input type="hidden" name="action"    id="confermaAction">
    <input type="hidden" name="id_gita"   id="confermaIdGita">
    <input type="hidden" name="tipo_gita" id="confermaTipoGita">
</form>

<script>
function apriConferma(azione, idGita, tipoGita, dest) {
    document.getElementById('confermaAction').value   = azione;
    document.getElementById('confermaIdGita').value    = idGita;
    document.getElementById('confermaTipoGita').value  = tipoGita;
    var titolo = document.getElementById('confermaTitolo');
    var msg    = document.getElementById('confermaMessaggio');
    var btn    = document.getElementById('confermaBtnSubmit');
    if (azione === 'partecipa') {
        titolo.style.color = 'var(--blue-700)';
        titolo.textContent = 'Conferma Partecipazione';
        msg.textContent    = 'Vuoi partecipare come accompagnatore alla gita verso ' + dest + '?';
        btn.className      = 'button';
        btn.textContent    = 'Partecipa';
    } else {
        titolo.style.color = 'var(--hex-red)';
        titolo.textContent = 'Conferma Disiscrizione';
        msg.textContent    = 'Vuoi disiscriverti dalla gita verso ' + dest + '?';
        btn.className      = 'button cancel';
        btn.textContent    = 'Disiscriviti';
    }
    document.getElementById('modalConfermaAzione').classList.remove('hidden');
}
</script>
</body>
</html>


