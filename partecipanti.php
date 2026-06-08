<?php
include('nav.php');

$idGita = 0;
if (isset($_GET['id'])) {
    $idGita = (int)$_GET['id'];
}
if ($idGita === 0) {
    header("Location: mieGite.php");
    exit;
}

$idUtenteLoggato = 0;
if (isset($_SESSION['id_utente'])) {
    $idUtenteLoggato = (int)$_SESSION['id_utente'];
}

$ruolo = 0;
if (isset($_SESSION['ruolo'])) {
    $ruolo = $_SESSION['ruolo'];
}

// verifica accesso autore o accompagnatore gita
$sqlGita = "SELECT * FROM gite5 WHERE idGita = $idGita";
$resGita = mysqli_query($conn, $sqlGita);
if (!$resGita || mysqli_num_rows($resGita) == 0) {
    header("Location: mieGite.php");
    exit;
}
$gita = mysqli_fetch_assoc($resGita);

// controlla che sia autore o accompagnatore
$isAutore = false;
if ($gita['idUtente'] == $idUtenteLoggato) {
    $isAutore = true;
}

$chkAcc = mysqli_query($conn, "SELECT id FROM accompagnatori WHERE idgita=$idGita AND idutente=$idUtenteLoggato AND tipo_gita='5g'");
$isAccompagnatore = false;
if ($chkAcc) {
    if (mysqli_num_rows($chkAcc) > 0) {
        $isAccompagnatore = true;
    }
}

if (!$isAutore && !$isAccompagnatore && $ruolo != 2) {
    header("Location: mieGite.php");
    exit;
}

$messaggio = '';

// aggiungi partecipante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'aggiungi') {
    $nome = '';
    if (isset($_POST['nome'])) { $nome = mysqli_real_escape_string($conn, trim($_POST['nome'])); }
    
    $cognome = '';
    if (isset($_POST['cognome'])) { $cognome = mysqli_real_escape_string($conn, trim($_POST['cognome'])); }
    
    $classe = '';
    if (isset($_POST['classe'])) { $classe = mysqli_real_escape_string($conn, trim($_POST['classe'])); }
    
    $note = '';
    if (isset($_POST['note'])) { $note = mysqli_real_escape_string($conn, trim($_POST['note'])); }
    
    $documento = '';
    if (isset($_POST['documento'])) { $documento = mysqli_real_escape_string($conn, trim($_POST['documento'])); }
    
    $nDoc = '';
    if (isset($_POST['nDocumento'])) { $nDoc = mysqli_real_escape_string($conn, trim($_POST['nDocumento'])); }
    
    $scadenza = '';
    if (isset($_POST['scadenza'])) { $scadenza = trim($_POST['scadenza']); }
    
    $scadenza_s = "NULL";
    if ($scadenza) {
        $scadenza_s = "'$scadenza'";
    }

    $nDocCleaned = strtoupper(str_replace([' ', '-'], '', $nDoc));
    $today = date('Y-m-d');

    if ($nome !== '' && $cognome !== '' && $classe !== '' && $documento !== '' && $nDoc !== '' && $scadenza !== '') {
        if ($scadenza < $today) {
            $messaggio = 'scaduto';
        } elseif (!preg_match('/^([A-Z]{2}\d{5}[A-Z]{2}|[A-Z]{2}\d{7})$/', $nDocCleaned)) {
            $messaggio = 'formato';
        } else {
            $sql = "INSERT INTO partecipanti (idgita, nome, cognome, classe, descrizione, documento, nDocumento, scadenza) VALUES ($idGita, '$nome', '$cognome', '$classe', '$note', '$documento', '$nDocCleaned', $scadenza_s)";
            if (mysqli_query($conn, $sql)) {
                $messaggio = 'ok';
            } else {
                $messaggio = 'error';
            }
        }
    } else {
        $messaggio = 'campi';
    }
}

// elimina partecipante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina') {
    $idPart = 0;
    if (isset($_POST['id_part'])) {
        $idPart = (int)$_POST['id_part'];
    }
    
    if ($idPart > 0) {
        mysqli_query($conn, "DELETE FROM partecipanti WHERE id = $idPart AND idgita = $idGita");
    }
    header("Location: partecipanti.php?id=$idGita");
    exit;
}

// elimina accompagnatore solo per commissione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina_acc' && $ruolo == 2) {
    $idAcc = 0;
    if (isset($_POST['id_acc'])) {
        $idAcc = (int)$_POST['id_acc'];
    }
    if ($idAcc > 0) {
        mysqli_query($conn, "DELETE FROM accompagnatori WHERE id = $idAcc AND idgita = $idGita");
    }
    header("Location: partecipanti.php?id=$idGita&rem_acc=1");
    exit;
}

// modifica dati accompagnatore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mod_acc') {
    $accId = 0;
    if (isset($_POST['acc_id'])) { $accId = (int)$_POST['acc_id']; }
    
    $documento = '';
    if (isset($_POST['acc_documento'])) { $documento = mysqli_real_escape_string($conn, trim($_POST['acc_documento'])); }
    
    $nDoc = '';
    if (isset($_POST['acc_nDocumento'])) { $nDoc = mysqli_real_escape_string($conn, trim($_POST['acc_nDocumento'])); }
    
    $scadenza = '';
    if (isset($_POST['acc_scadenza'])) { $scadenza = trim($_POST['acc_scadenza']); }
    
    $scadenza_s = "NULL";
    if ($scadenza) { $scadenza_s = "'$scadenza'"; }
    
    $note = '';
    if (isset($_POST['acc_note'])) { $note = mysqli_real_escape_string($conn, trim($_POST['acc_note'])); }
    
    $nDocCleaned = strtoupper(str_replace([' ', '-'], '', $nDoc));
    $today = date('Y-m-d');
    
    if ($accId > 0) {
        if ($scadenza !== '' && $scadenza < $today) {
            header("Location: partecipanti.php?id=$idGita&err_acc=scaduto");
            exit;
        } elseif ($nDoc !== '' && !preg_match('/^([A-Z]{2}\d{5}[A-Z]{2}|[A-Z]{2}\d{7})$/', $nDocCleaned)) {
            header("Location: partecipanti.php?id=$idGita&err_acc=formato");
            exit;
        } else {
            mysqli_query($conn, "UPDATE accompagnatori SET documento='$documento', nDocumento='$nDocCleaned', scadenza=$scadenza_s, note='$note' WHERE id=$accId AND idgita=$idGita");
        }
    }
    header("Location: partecipanti.php?id=$idGita&acc=1");
    exit;
}

// carica accompagnatori
$resAcc = mysqli_query($conn,
    "SELECT a.*, CONCAT(u.Nome, ' ', u.Cognome) AS nomeUtente, u.Nome AS nome_u, u.Cognome AS cognome_u
     FROM accompagnatori a JOIN utente u ON a.idutente = u.IDUtente
     WHERE a.idgita = $idGita AND a.tipo_gita = '5g'
     ORDER BY u.Cognome ASC, u.Nome ASC"
);

// carica partecipanti
$resPart = mysqli_query($conn, "SELECT * FROM partecipanti WHERE idgita = $idGita ORDER BY cognome ASC, nome ASC");

$totPart = 0;
if ($resPart) {
    $totPart = mysqli_num_rows($resPart);
}

$destDisplay = '';
if (isset($gita['destinazione'])) {
    $destDisplay = htmlspecialchars($gita['destinazione']);
}

$periodoDisp = '';
if (isset($gita['periodo'])) {
    $periodoDisp = htmlspecialchars($gita['periodo']);
}

$mezzoDisp = '';
if (isset($gita['mezzo'])) {
    $mezzoDisp = htmlspecialchars($gita['mezzo']);
}

$classiDisp = '';
if (isset($gita['classi'])) {
    $classiDisp = htmlspecialchars($gita['classi']);
}

$gi = '';
if ($gita['giornoInizio']) {
    $gi = date('d/m/Y', strtotime($gita['giornoInizio']));
}

$gf = '';
if ($gita['giornoFine']) {
    $gf = date('d/m/Y', strtotime($gita['giornoFine']));
}

$numAlunniDisp = '';
if (isset($gita['numAlunni'])) {
    $numAlunniDisp = $gita['numAlunni'];
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partecipanti</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css">
    <script src="vetrina.js" defer></script>
</head>
<body>
<div class="container">
<main class="content bozze-padding">

    <div class="hero-section" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1.5rem;">
        <div>
            <h2 style="margin-bottom:0.25rem;color:var(--blue-700);">Partecipanti</h2>
            <p style="color:#64748b;margin:0;">Gita: <strong><?php echo $destDisplay; ?></strong></p>
        </div>
        <a href="mieGite.php" class="button outline" style="text-decoration:none;">&#8592; Torna alle Mie Gite</a>
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:1rem;background:var(--blue-50,#eff6ff);border-radius:10px;padding:1rem 1.5rem;margin-bottom:1.5rem;">
        <?php if ($gi): ?><span style="font-size:0.9rem;"><strong>Date:</strong> <?php echo $gi; ?> &rarr; <?php echo $gf; ?></span><?php endif; ?>
        <?php if ($periodoDisp): ?><span style="font-size:0.9rem;"><strong>Periodo:</strong> <?php echo $periodoDisp; ?></span><?php endif; ?>
        <?php if ($mezzoDisp): ?><span style="font-size:0.9rem;"><strong>Mezzo:</strong> <?php echo $mezzoDisp; ?></span><?php endif; ?>
        <?php if ($classiDisp): ?><span style="font-size:0.9rem;"><strong>Classi:</strong> <?php echo $classiDisp; ?></span><?php endif; ?>
        <?php if ($numAlunniDisp !== ''): ?><span style="font-size:0.9rem;"><strong>Alunni previsti:</strong> <?php echo $numAlunniDisp; ?></span><?php endif; ?>
    </div>

    <?php if (isset($_GET['acc']) && $_GET['acc'] === '1'): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">Dati accompagnatore aggiornati.</div>
    <?php elseif (isset($_GET['rem_acc']) && $_GET['rem_acc'] === '1'): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">Accompagnatore rimosso con successo.</div>
    <?php elseif (isset($_GET['err_acc'])): ?>
        <?php if ($_GET['err_acc'] === 'scaduto'): ?>
            <div class="alert alert-error" style="margin-bottom:1rem;">La data di scadenza del documento dell'accompagnatore deve essere nel futuro.</div>
        <?php elseif ($_GET['err_acc'] === 'formato'): ?>
            <div class="alert alert-error" style="margin-bottom:1rem;">Il formato del numero di documento dell'accompagnatore non è valido (es. CA12345AA o AB1234567).</div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($messaggio === 'ok'): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">Partecipante aggiunto con successo.</div>
    <?php elseif ($messaggio === 'error'): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">Errore durante l'inserimento. Riprova.</div>
    <?php elseif ($messaggio === 'campi'): ?>
        <div class="alert alert-warning" style="margin-bottom:1rem;">Compila tutti i campi obbligatori.</div>
    <?php elseif ($messaggio === 'scaduto'): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">La data di scadenza del documento deve essere nel futuro.</div>
    <?php elseif ($messaggio === 'formato'): ?>
        <div class="alert alert-error" style="margin-bottom:1rem;">Il formato del numero di documento non è valido (es. CA12345AA o AB1234567).</div>
    <?php endif; ?>

    <!-- tabella accompagnatori -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h3 style="color:var(--blue-700);margin:0;">
            Accompagnatori
            <span style="font-size:0.88rem;font-weight:400;color:#64748b;">(<?php echo $resAcc ? mysqli_num_rows($resAcc) : 0; ?>)</span>
        </h3>
    </div>

    <div class="table-section" style="margin-bottom:2rem;">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Documento</th>
                        <th>N. Documento</th>
                        <th>Scadenza</th>
                        <th>Note</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($resAcc && mysqli_num_rows($resAcc) > 0):
                    $nAcc = 1;
                    while ($acc = mysqli_fetch_assoc($resAcc)):
                        $aId = (int)$acc['id'];
                        
                        $aNome = '';
                        if (isset($acc['nome_u'])) { $aNome = htmlspecialchars($acc['nome_u']); }
                        
                        $aCognome = '';
                        if (isset($acc['cognome_u'])) { $aCognome = htmlspecialchars($acc['cognome_u']); }
                        
                        $aDoc = '';
                        if (isset($acc['documento'])) { $aDoc = htmlspecialchars($acc['documento']); }
                        
                        $aNDoc = '';
                        if (isset($acc['nDocumento'])) { $aNDoc = htmlspecialchars($acc['nDocumento']); }
                        
                        $aScad = '';
                        if ($acc['scadenza']) { $aScad = date('d/m/Y', strtotime($acc['scadenza'])); }
                        
                        $aNote = '';
                        if (isset($acc['note'])) { $aNote = htmlspecialchars($acc['note']); }
                        
                        $aDocJ = '';
                        if (isset($acc['documento'])) { $aDocJ = htmlspecialchars($acc['documento']); }
                        
                        $aNDocJ = '';
                        if (isset($acc['nDocumento'])) { $aNDocJ = htmlspecialchars($acc['nDocumento']); }
                        
                        $aScadV = '';
                        if (isset($acc['scadenza'])) { $aScadV = $acc['scadenza']; }
                        
                        $aNoteJ = '';
                        if (isset($acc['note'])) { $aNoteJ = htmlspecialchars($acc['note']); }

                        $canEdit = false;
                        if ($acc['idutente'] == $idUtenteLoggato || $ruolo == 2) {
                            $canEdit = true;
                        }
                        
                        $outDoc = '<span style="color:#94a3b8;">—</span>';
                        if (!empty($aDoc)) { $outDoc = $aDoc; }
                        
                        $outNDoc = '<span style="color:#94a3b8;">—</span>';
                        if (!empty($aNDoc)) { $outNDoc = $aNDoc; }
                        
                        $outScad = '<span style="color:#94a3b8;">—</span>';
                        if (!empty($aScad)) { $outScad = $aScad; }
                        
                        $outNote = '<span style="color:#94a3b8;">—</span>';
                        if (!empty($aNote)) { $outNote = $aNote; }
                ?>
                    <tr>
                        <td><?php echo $nAcc++; ?></td>
                        <td><?php echo $aNome; ?></td>
                        <td><?php echo $aCognome; ?></td>
                        <td><?php echo $outDoc; ?></td>
                        <td><?php echo $outNDoc; ?></td>
                        <td><?php echo $outScad; ?></td>
                        <td><?php echo $outNote; ?></td>
                        <td>
                            <div class="azioni-cell">
                                <?php if ($canEdit): ?>
                                <button type="button" class="button xs"
                                    data-acc-id="<?php echo $aId; ?>"
                                    data-nome="<?php echo $aNome; ?> <?php echo $aCognome; ?>"
                                    data-doc="<?php echo $aDocJ; ?>"
                                    data-ndoc="<?php echo $aNDocJ; ?>"
                                    data-scad="<?php echo $aScadV; ?>"
                                    data-note="<?php echo $aNoteJ; ?>"
                                    onclick="apriModAcc(this)">Modifica</button>
                                <?php endif; ?>
                                <?php if ($ruolo == 2): ?>
                                <button type="button" class="button cancel xs" onclick="apriRimuoviAcc(<?php echo $aId; ?>, '<?php echo addslashes($aNome . ' ' . $aCognome); ?>')">Rimuovi</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="8" style="text-align:center;color:#94a3b8;">Nessun accompagnatore registrato.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- tabella partecipanti -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h3 style="color:var(--blue-700);margin:0;">
            Lista Partecipanti
            <span style="font-size:0.88rem;font-weight:400;color:#64748b;">(<?php echo $totPart; ?> inseriti)</span>
        </h3>
        <button class="button" onclick="document.getElementById('modalAggiungi').classList.remove('hidden')">+ Aggiungi Partecipante</button>
    </div>

    <div class="table-section">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Classe</th>
                        <th>Documento</th>
                        <th>N. Documento</th>
                        <th>Scadenza</th>
                        <th>Note</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($totPart > 0):
                    $n = 1;
                    while ($p = mysqli_fetch_assoc($resPart)):
                        $pId = (int)$p['id'];
                        $pNome = htmlspecialchars($p['nome']);
                        $pCognome = htmlspecialchars($p['cognome']);
                        $pClasse = htmlspecialchars($p['classe']);
                        
                        $pDoc = '';
                        if (isset($p['documento'])) {
                            $pDoc = htmlspecialchars($p['documento']);
                        }
                        
                        $pNDoc = '';
                        if (isset($p['nDocumento'])) {
                            $pNDoc = htmlspecialchars($p['nDocumento']);
                        }
                        
                        $pScad = '';
                        if ($p['scadenza']) {
                            $pScad = date('d/m/Y', strtotime($p['scadenza']));
                        }
                        
                        $pNote = '';
                        if (isset($p['descrizione'])) {
                            $pNote = htmlspecialchars($p['descrizione']);
                        }
                        
                        $outPDoc = '';
                        if (!empty($pDoc)) { $outPDoc = $pDoc; }
                        
                        $outPNDoc = '';
                        if (!empty($pNDoc)) { $outPNDoc = $pNDoc; }
                        
                        $outPScad = '';
                        if (!empty($pScad)) { $outPScad = $pScad; }
                        
                        $outPNote = '';
                        if (!empty($pNote)) { $outPNote = $pNote; }
                ?>
                    <tr>
                        <td><?php echo $n++; ?></td>
                        <td><?php echo $pCognome; ?></td>
                        <td><?php echo $pNome; ?></td>
                        <td><?php echo $pClasse; ?></td>
                        <td><?php echo $outPDoc; ?></td>
                        <td><?php echo $outPNDoc; ?></td>
                        <td><?php echo $outPScad; ?></td>
                        <td><?php echo $outPNote; ?></td>
                        <td>
                            <div class="azioni-cell">
                                <button type="button" class="button cancel xs" onclick="apriRimuoviPart(<?php echo $pId; ?>, '<?php echo addslashes($pNome . ' ' . $pCognome); ?>')">Rimuovi</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="9" style="text-align:center;color:#94a3b8;">Nessun partecipante inserito.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- modal: modifica dati accompagnatore -->
<div class="modal-overlay hidden" id="modalModAcc">
<div class="modal wide-modal">
<div class="modal-header">
    <h3 id="modAccTit">Modifica dati documento</h3>
</div>
<div class="modal-body">
<form id="formModAcc" method="POST" action="partecipanti.php?id=<?php echo $idGita; ?>">
    <input type="hidden" name="action" value="mod_acc">
    <input type="hidden" name="acc_id" id="modAccId">
    <div class="form-grid">
        <div class="form-group">
            <label>Tipo documento</label>
            <select name="acc_documento" id="modAccDoc" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Carta d'identita">Carta d'identità</option>
                <option value="Passaporto">Passaporto</option>
            </select>
        </div>
        <div class="form-group">
            <label>N. Documento</label>
            <input type="text" name="acc_nDocumento" id="modAccNDoc" class="form-control" placeholder="es. CA12345AA">
        </div>
        <div class="form-group">
            <label>Scadenza documento</label>
            <input type="date" name="acc_scadenza" id="modAccScad" class="form-control">
        </div>
        <div class="form-group">
            <label>Allergeni / Note</label>
            <input type="text" name="acc_note" id="modAccNote" class="form-control" placeholder="es. allergie, intolleranze...">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalModAcc').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formModAcc" class="button">Salva</button>
</div>
</div>
</div>

<!-- modal: aggiungi partecipante -->
<div class="modal-overlay hidden" id="modalAggiungi">
<div class="modal wide-modal">
<div class="modal-header">
    <h3>Aggiungi Partecipante</h3>
</div>
<div class="modal-body">
<form id="formAggiungi" method="POST" action="partecipanti.php?id=<?php echo $idGita; ?>">
    <input type="hidden" name="action" value="aggiungi">
    <div class="form-grid">
        <div class="form-group">
            <label>Nome *</label>
            <input type="text" name="nome" class="form-control" placeholder="es. Mario" required>
        </div>
        <div class="form-group">
            <label>Cognome *</label>
            <input type="text" name="cognome" class="form-control" placeholder="es. Rossi" required>
        </div>
        <div class="form-group">
            <label>Classe *</label>
            <select name="classe" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="5AII">5AII</option>
                <option value="5BII">5BII</option>
                <option value="5CII">5CII</option>
                <option value="5DIT">5DIT</option>
                <option value="5AEA">5AEA</option>
                <option value="5BEA">5BEA</option>
                <option value="5CEA">5CEA</option>
                <option value="5AL">5AL</option>
                <option value="5BL">5BL</option>
                <option value="5CL">5CL</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tipo documento *</label>
            <select name="documento" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="Carta d'identita">Carta d'identità</option>
                <option value="Passaporto">Passaporto</option>
            </select>
        </div>
        <div class="form-group">
            <label>N. Documento *</label>
            <input type="text" name="nDocumento" class="form-control" placeholder="es. CA12345AA" required>
        </div>
        <div class="form-group">
            <label>Scadenza documento *</label>
            <input type="date" name="scadenza" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Allergeni / Note (facoltativo)</label>
            <input type="text" name="note" class="form-control" placeholder="es. allergie, intolleranze...">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button class="button cancel" onclick="document.getElementById('modalAggiungi').classList.add('hidden')">Annulla</button>
    <button class="button" type="submit" form="formAggiungi">Aggiungi</button>
</div>
</div>
</div>

<!-- modal: conferma rimozione accompagnatore -->
<div class="modal-overlay hidden" id="modalRimuoviAcc">
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--hex-red);margin-bottom:0.5rem;">Conferma Rimozione</h3>
    <p style="color:var(--blue-900);">Rimuovere l'accompagnatore <strong id="rimuoviAccNome"></strong>?</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button type="button" class="button cancel-outline" onclick="document.getElementById('modalRimuoviAcc').classList.add('hidden')">Annulla</button>
    <form id="formRimuoviAcc" method="POST" action="partecipanti.php?id=<?php echo $idGita; ?>" style="margin:0;">
        <input type="hidden" name="action" value="elimina_acc">
        <input type="hidden" name="id_acc" id="rimuoviAccId">
        <button type="submit" class="button cancel">Rimuovi</button>
    </form>
</div>
</div>
</div>

<!-- modal: conferma rimozione partecipante -->
<div class="modal-overlay hidden" id="modalRimuoviPart">
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--hex-red);margin-bottom:0.5rem;">Conferma Rimozione</h3>
    <p style="color:var(--blue-900);">Rimuovere il partecipante <strong id="rimuoviPartNome"></strong>?</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button type="button" class="button cancel-outline" onclick="document.getElementById('modalRimuoviPart').classList.add('hidden')">Annulla</button>
    <form id="formRimuoviPart" method="POST" action="partecipanti.php?id=<?php echo $idGita; ?>" style="margin:0;">
        <input type="hidden" name="action" value="elimina">
        <input type="hidden" name="id_part" id="rimuoviPartId">
        <button type="submit" class="button cancel">Rimuovi</button>
    </form>
</div>
</div>
</div>

    <?php include('footer.php'); ?>
</div>

<script>
// Imposta data minima odierna per i campi data di scadenza
document.addEventListener('DOMContentLoaded', function() {
    var todayStr = new Date().toISOString().split('T')[0];
    // Form aggiungi
    var scadAggiungi = document.querySelector('#formAggiungi [name="scadenza"]');
    if (scadAggiungi) scadAggiungi.min = todayStr;
    // Form modifica
    var scadModAcc = document.getElementById('modAccScad');
    if (scadModAcc) scadModAcc.min = todayStr;
});

// Validazione client-side form Aggiungi
document.getElementById('formAggiungi').addEventListener('submit', function(e) {
    var nDocInput = this.querySelector('[name="nDocumento"]');
    var scadInput = this.querySelector('[name="scadenza"]');
    
    // Pulisci e normalizza
    var nDocVal = nDocInput.value.replace(/[\s-]/g, '').toUpperCase();
    nDocInput.value = nDocVal;
    
    var docRegex = /^([A-Z]{2}\d{5}[A-Z]{2}|[A-Z]{2}\d{7})$/;
    if (!docRegex.test(nDocVal)) {
        alert("Il numero di documento inserito non è valido.\nFormati consentiti:\n- CIE (es. CA12345AA)\n- Cartaceo/Passaporto (es. AB1234567)");
        nDocInput.focus();
        e.preventDefault();
        return false;
    }
    
    var todayStr = new Date().toISOString().split('T')[0];
    if (scadInput.value < todayStr) {
        alert("La data di scadenza del documento deve essere nel futuro.");
        scadInput.focus();
        e.preventDefault();
        return false;
    }
});

// Validazione client-side form Modifica Accompagnatore
document.getElementById('formModAcc').addEventListener('submit', function(e) {
    var nDocInput = document.getElementById('modAccNDoc');
    var scadInput = document.getElementById('modAccScad');
    
    if (nDocInput.value.trim() !== '') {
        var nDocVal = nDocInput.value.replace(/[\s-]/g, '').toUpperCase();
        nDocInput.value = nDocVal;
        
        var docRegex = /^([A-Z]{2}\d{5}[A-Z]{2}|[A-Z]{2}\d{7})$/;
        if (!docRegex.test(nDocVal)) {
            alert("Il numero di documento dell'accompagnatore non è valido.\nFormati consentiti:\n- CIE (es. CA12345AA)\n- Cartaceo/Passaporto (es. AB1234567)");
            nDocInput.focus();
            e.preventDefault();
            return false;
        }
    }
    
    if (scadInput.value !== '') {
        var todayStr = new Date().toISOString().split('T')[0];
        if (scadInput.value < todayStr) {
            alert("La data di scadenza del documento dell'accompagnatore deve essere nel futuro.");
            scadInput.focus();
            e.preventDefault();
            return false;
        }
    }
});

function apriModAcc(btn) {
    var d = btn.dataset;
    document.getElementById('modAccTit').textContent  = 'Dati documento: ' + d.nome;
    document.getElementById('modAccId').value         = d.accId;
    document.getElementById('modAccDoc').value        = d.doc  || '';
    document.getElementById('modAccNDoc').value       = d.ndoc || '';
    document.getElementById('modAccScad').value       = d.scad || '';
    document.getElementById('modAccNote').value       = d.note || '';
    document.getElementById('modalModAcc').classList.remove('hidden');
}

window.addEventListener('click', function(e) {
    ['modalAggiungi','modalModAcc','modalRimuoviPart','modalRimuoviAcc'].forEach(function(id) {
        var m = document.getElementById(id);
        if (e.target === m) m.classList.add('hidden');
    });
});

function apriRimuoviAcc(id, nome) {
    document.getElementById('rimuoviAccId').value = id;
    document.getElementById('rimuoviAccNome').textContent = nome;
    document.getElementById('modalRimuoviAcc').classList.remove('hidden');
}

function apriRimuoviPart(id, nome) {
    document.getElementById('rimuoviPartId').value = id;
    document.getElementById('rimuoviPartNome').textContent = nome;
    document.getElementById('modalRimuoviPart').classList.remove('hidden');
}
<?php if ($messaggio === 'campi' || $messaggio === 'error' || $messaggio === 'scaduto' || $messaggio === 'formato'): ?>
document.getElementById('modalAggiungi').classList.remove('hidden');
<?php endif; ?>
</script>
</body>
</html>


