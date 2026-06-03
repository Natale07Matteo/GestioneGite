<?php
include('nav.php');
include('utils.php');

$ruolo           = isset($_SESSION['ruolo'])     ? $_SESSION['ruolo']     : 0;
$idUtenteLoggato = isset($_SESSION['id_utente']) ? (int)$_SESSION['id_utente'] : 0;

// partecipa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'partecipa') {
    $idGita   = isset($_POST['id_gita'])   ? (int)$_POST['id_gita']   : 0;
    $tipoGita = isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === 'gite5' ? '5g' : '1g';
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        mysqli_query($conn, "INSERT IGNORE INTO accompagnatori (idgita, idutente, tipo_gita) VALUES ($idGita, $idUtenteLoggato, '$tipoGita')");
    }
    header("Location: inProgramma.php?partecipato=1");
    exit;
}

// disiscriviti
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'disiscriviti') {
    $idGita   = isset($_POST['id_gita'])   ? (int)$_POST['id_gita']   : 0;
    $tipoGita = isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === 'gite5' ? '5g' : '1g';
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        mysqli_query($conn, "DELETE FROM accompagnatori WHERE idgita = $idGita AND idutente = $idUtenteLoggato AND tipo_gita = '$tipoGita'");
    }
    header("Location: inProgramma.php?disiscritto=1");
    exit;
}

// elimina solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina' && $ruolo == 2) {
    $idGita = isset($_POST['id_gita']) ? (int)$_POST['id_gita'] : 0;
    $tab    = $_POST['tabella'] === 'gite5' ? 'gite5' : 'gita1g';
    if ($idGita > 0) {
        mysqli_query($conn, "DELETE FROM $tab WHERE idGita = $idGita");
        mysqli_query($conn, "DELETE FROM accompagnatori WHERE idgita = $idGita AND tipo_gita = '" . ($tab === 'gite5' ? '5g' : '1g') . "'");
    }
    header("Location: inProgramma.php");
    exit;
}

// modifica gita 1g solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_1g' && $ruolo == 2) {
    $id   = isset($_POST['id_gita']) ? (int)$_POST['id_gita'] : 0;
    $dest = mysqli_real_escape_string($conn, trim(isset($_POST['destinazione']) ? $_POST['destinazione'] : ''));
    $desc = mysqli_real_escape_string($conn, trim(isset($_POST['descrizione'])  ? $_POST['descrizione']  : ''));
    $mezz = mysqli_real_escape_string($conn, trim(isset($_POST['mezzo'])        ? $_POST['mezzo']        : ''));
    $per  = mysqli_real_escape_string($conn, trim(isset($_POST['periodo'])      ? $_POST['periodo']      : ''));
    $cls  = mysqli_real_escape_string($conn, trim(isset($_POST['classi'])       ? $_POST['classi']       : ''));
    $gio  = trim(isset($_POST['giorno']) ? $_POST['giorno'] : '');

    // validazione dati
    if ($gio && (strtotime($gio) === false || (int)date('Y', strtotime($gio)) < 2024 || (int)date('Y', strtotime($gio)) > 2030)) {
        $gio = '';
    }
    if ($gio && strtotime($gio) <= strtotime(date('Y-m-d'))) {
        $gio = '';
    }

    $gio_s   = $gio ? "'" . $conn->real_escape_string($gio) . "'" : "NULL";
    $costoM = isset($_POST['costoMezzo'])    && $_POST['costoMezzo']    !== '' ? (float)$_POST['costoMezzo']    : 'NULL';
    $costoA = isset($_POST['costoAttivita']) && $_POST['costoAttivita'] !== '' ? (float)$_POST['costoAttivita'] : 'NULL';
    $costoP = isset($_POST['costoAPersona']) && $_POST['costoAPersona'] !== '' ? (float)$_POST['costoAPersona'] : 'NULL';
    $numAl  = isset($_POST['numAlunni'])     && $_POST['numAlunni']     !== '' ? (int)$_POST['numAlunni']       : 'NULL';
    if ($id > 0) {
        mysqli_query($conn, "UPDATE gita1g SET destinazione='$dest', descrizione='$desc', mezzo='$mezz', periodo='$per', classi='$cls', giorno=$gio_s, costoMezzo=$costoM, costoAttivita=$costoA, costoAPersona=$costoP, numAlunni=$numAl WHERE idGita=$id");
    }
    header("Location: inProgramma.php");
    exit;
}

// modifica gita 5g solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_5g' && $ruolo == 2) {
    $id   = isset($_POST['id_gita']) ? (int)$_POST['id_gita'] : 0;
    $dest = mysqli_real_escape_string($conn, trim(isset($_POST['destinazione']) ? $_POST['destinazione'] : ''));
    $desc = mysqli_real_escape_string($conn, trim(isset($_POST['descrizione'])  ? $_POST['descrizione']  : ''));
    $mezz = mysqli_real_escape_string($conn, trim(isset($_POST['mezzo'])        ? $_POST['mezzo']        : ''));
    $per  = mysqli_real_escape_string($conn, trim(isset($_POST['periodo'])      ? $_POST['periodo']      : ''));
    $cls  = mysqli_real_escape_string($conn, trim(isset($_POST['classi'])       ? $_POST['classi']       : ''));
    $gi   = trim(isset($_POST['giornoInizio']) ? $_POST['giornoInizio'] : '');
    $gf   = trim(isset($_POST['giornoFine'])   ? $_POST['giornoFine']   : '');

    // validazione dati
    if ($gi && (strtotime($gi) === false || (int)date('Y', strtotime($gi)) < 2024 || (int)date('Y', strtotime($gi)) > 2030)) {
        $gi = '';
    }
    if ($gf && (strtotime($gf) === false || (int)date('Y', strtotime($gf)) < 2024 || (int)date('Y', strtotime($gf)) > 2030)) {
        $gf = '';
    }
    if ($gi && strtotime($gi) <= strtotime(date('Y-m-d'))) {
        $gi = '';
    }
    if ($gi && $gf && strtotime($gi) >= strtotime($gf)) {
        $gi = '';
        $gf = '';
    }

    $gi_s    = $gi ? "'" . $conn->real_escape_string($gi) . "'" : "NULL";
    $gf_s    = $gf ? "'" . $conn->real_escape_string($gf) . "'" : "NULL";
    $costoP = isset($_POST['costoAPersona']) && $_POST['costoAPersona'] !== '' ? (float)$_POST['costoAPersona'] : 'NULL';
    $numAl  = isset($_POST['numAlunni'])     && $_POST['numAlunni']     !== '' ? (int)$_POST['numAlunni']       : 'NULL';
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
        $id      = (int)($riga['idGita']);
        $dest    = htmlspecialchars(isset($riga['destinazione']) ? $riga['destinazione'] : '');
        $mezzo   = htmlspecialchars(isset($riga['mezzo'])   ? $riga['mezzo']   : '');
        $classi  = htmlspecialchars(isset($riga['classi'])  ? $riga['classi']  : '');
        $giorno  = $riga['giorno'] ? (strtotime($riga['giorno']) !== false ? date('d/m/Y', strtotime($riga['giorno'])) : '—') : '—';
        $giornoV = isset($riga['giorno']) ? $riga['giorno'] : '';
        $costo   = $riga['costoAPersona'] !== null ? '&euro; ' . number_format($riga['costoAPersona'], 2, ',', '.') : '—';
        $numAl   = isset($riga['numAlunni']) ? $riga['numAlunni'] : '';
        $autore  = htmlspecialchars(isset($riga['autore']) ? $riga['autore'] : '');
        $costoMV = isset($riga['costoMezzo'])    ? $riga['costoMezzo']    : '';
        $costoAV = isset($riga['costoAttivita']) ? $riga['costoAttivita'] : '';
        $costoPA = isset($riga['costoAPersona']) ? $riga['costoAPersona'] : '';
        $dJ      = htmlspecialchars(isset($riga['destinazione']) ? $riga['destinazione'] : '');
        $descDisp = htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : '');
        // iscritto se è accompagnatore OPPURE se è l'autore della gita
        $chk = mysqli_query($conn,
        "SELECT id
        FROM accompagnatori
        WHERE idgita=$id AND idutente=$idUtenteLoggato AND tipo_gita='1g'");
        $isAccompagnatore = ($chk && mysqli_num_rows($chk) > 0);
        $isAutore = ($riga['idUtente'] == $idUtenteLoggato);
?>
    <tr>
        <td style="white-space:normal;"><?php echo $dest; ?></td>
        <td style="white-space:normal; max-width:200px;"><?php echo $descDisp ? $descDisp : '—'; ?></td>
        <td><?php echo badgeStatoHtml($riga['idStato'], isset($riga['giorno']) ? $riga['giorno'] : null); ?></td>
        <td><?php echo !empty($mezzo) ? $mezzo : '—'; ?></td>
        <td><?php echo !empty($classi) ? $classi : '—'; ?></td>
        <td><?php echo $giorno; ?></td>
        <td><?php echo $costo; ?></td>
        <td><?php echo !empty($numAl) ? $numAl : '—'; ?></td>
        <td><?php echo $autore; ?></td>
        <td>
            <div class="azioni-cell">
                <?php if ($riga['idStato'] == 4): ?>
                <?php if ($isAccompagnatore): ?>
                    <form method="POST" action="inProgramma.php" style="margin:0;">
                        <input type="hidden" name="action"    value="disiscriviti">
                        <input type="hidden" name="id_gita"   value="<?php echo $id; ?>">
                        <input type="hidden" name="tipo_gita" value="gita1g">
                        <button type="submit" class="button cancel xs">Disiscriviti</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="inProgramma.php" style="margin:0;">
                        <input type="hidden" name="action"    value="partecipa">
                        <input type="hidden" name="id_gita"   value="<?php echo $id; ?>">
                        <input type="hidden" name="tipo_gita" value="gita1g">
                        <button type="submit" class="button xs">Partecipa</button>
                    </form>
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
<h3 style="color:var(--blue-700);margin:2rem 0 0.75rem;">Gite per le quinte</h3>
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
        $dest   = htmlspecialchars(isset($riga['destinazione']) ? $riga['destinazione'] : '');
        $mezzo  = htmlspecialchars(isset($riga['mezzo'])   ? $riga['mezzo']   : '');
        $classi = htmlspecialchars(isset($riga['classi'])  ? $riga['classi']  : '');
        $gi     = $riga['giornoInizio'] ? (strtotime($riga['giornoInizio']) !== false ? date('d/m/Y', strtotime($riga['giornoInizio'])) : '—') : '—';
        $gf     = $riga['giornoFine']   ? (strtotime($riga['giornoFine'])   !== false ? date('d/m/Y', strtotime($riga['giornoFine']))   : '—') : '—';
        $giV    = isset($riga['giornoInizio']) ? $riga['giornoInizio'] : '';
        $gfV    = isset($riga['giornoFine'])   ? $riga['giornoFine']   : '';
        $costo  = $riga['costoAPersona'] !== null ? '&euro; ' . number_format($riga['costoAPersona'], 2, ',', '.') : '—';
        $numAl  = isset($riga['numAlunni']) ? $riga['numAlunni'] : '';
        $autore = htmlspecialchars(isset($riga['autore']) ? $riga['autore'] : '');
        $costoPA = isset($riga['costoAPersona']) ? $riga['costoAPersona'] : '';
        $dJ     = htmlspecialchars(isset($riga['destinazione']) ? $riga['destinazione'] : '');
        $descDisp = htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : '');
        // iscritto se è accompagnatore OPPURE se è l'autore della gita
        $chk = mysqli_query($conn, "SELECT id FROM accompagnatori WHERE idgita=$id AND idutente=$idUtenteLoggato AND tipo_gita='5g'");
        $isAccompagnatore = ($chk && mysqli_num_rows($chk) > 0);
        $isAutore = ($riga['idUtente'] == $idUtenteLoggato);
?>
    <tr>
        <td style="white-space:normal;"><?php echo $dest; ?></td>
        <td style="white-space:normal; max-width:200px;"><?php echo $descDisp ? $descDisp : '—'; ?></td>
        <td><?php echo badgeStatoHtml($riga['idStato'], isset($riga['giornoInizio']) ? $riga['giornoInizio'] : null, isset($riga['giornoFine']) ? $riga['giornoFine'] : null); ?></td>
        <td><?php echo !empty($mezzo) ? $mezzo : '—'; ?></td>
        <td><?php echo !empty($classi) ? $classi : '—'; ?></td>
        <td><?php echo $gi; ?></td>
        <td><?php echo $gf; ?></td>
        <td><?php echo $costo; ?></td>
        <td><?php echo !empty($numAl) ? $numAl : '—'; ?></td>
        <td><?php echo $autore; ?></td>
        <td>
            <div class="azioni-cell">
                <?php if ($riga['idStato'] == 4): ?>
                <?php if ($isAccompagnatore): ?>
                    <form method="POST" action="inProgramma.php" style="margin:0;">
                        <input type="hidden" name="action"    value="disiscriviti">
                        <input type="hidden" name="id_gita"   value="<?php echo $id; ?>">
                        <input type="hidden" name="tipo_gita" value="gite5">
                        <button type="submit" class="button cancel xs">Disiscriviti</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="inProgramma.php" style="margin:0;">
                        <input type="hidden" name="action"    value="partecipa">
                        <input type="hidden" name="id_gita"   value="<?php echo $id; ?>">
                        <input type="hidden" name="tipo_gita" value="gite5">
                        <button type="submit" class="button xs">Partecipa</button>
                    </form>
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
</body>
</html>


