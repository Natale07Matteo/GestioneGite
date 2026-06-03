<?php
include('nav.php');
include('utils.php');

$idUtenteLoggato = $_SESSION['id_utente'];
$messaggio = "";

$conn->query("CREATE TABLE IF NOT EXISTS gite_archiviate_utente (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idutente INT NOT NULL,
    idgita INT NOT NULL,
    tipo_gita ENUM('1g','5g') NOT NULL,
    data_archiviazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_archivio (idutente, idgita, tipo_gita)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

// disiscriviti da accompagnatore
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'disiscriviti') {
    $idGita   = (int)$_POST['id_gita'];
    $tipoGita = isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === '5g' ? '5g' : '1g';
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        $conn->query("
        DELETE FROM accompagnatori WHERE idgita = $idGita AND idutente = $idUtenteLoggato AND tipo_gita = '$tipoGita'");
        $messaggio = "disiscritto_ok";
    }
}

// partecipa da mieGite
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'partecipa') {
    $idGita   = (int)$_POST['id_gita'];
    $tipoGita = isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === '5g' ? '5g' : '1g';
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        $conn->query(
            "INSERT IGNORE INTO accompagnatori (idgita, idutente, tipo_gita) VALUES ($idGita, $idUtenteLoggato, '$tipoGita')");
        $messaggio = "partecipato_ok";
    }
}

// archivia gita conclusa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'archivia') {
    $idGita   = (int)$_POST['id_gita'];
    $tipoGita = isset($_POST['tipo_gita']) && $_POST['tipo_gita'] === '5g' ? '5g' : '1g';
    $sonoAutore = isset($_POST['sono_autore']) ? (int)$_POST['sono_autore'] : 0;
    if ($idGita > 0 && $idUtenteLoggato > 0) {
        $conn->query("INSERT IGNORE INTO gite_archiviate_utente (idutente, idgita, tipo_gita) VALUES ($idUtenteLoggato, $idGita, '$tipoGita')");
        $messaggio = "archiviata_ok";
    }
}

// modifica gita 1g in organizzazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_org_1g') {
    $idGita       = (int)$_POST['id_gita'];
    $dest         = $conn->real_escape_string(trim(isset($_POST['mo_destinazione']) ? $_POST['mo_destinazione'] : ''));
    $desc         = $conn->real_escape_string(trim(isset($_POST['mo_descrizione'])  ? $_POST['mo_descrizione']  : ''));
    $mezzo        = $conn->real_escape_string(trim(isset($_POST['mo_mezzo'])        ? $_POST['mo_mezzo']        : ''));
    $periodo      = $conn->real_escape_string(trim(isset($_POST['mo_periodo'])      ? $_POST['mo_periodo']      : ''));
    $classi       = $conn->real_escape_string(trim(isset($_POST['mo_classi'])       ? $_POST['mo_classi']       : ''));
    $giorno       = isset($_POST['mo_giorno']) && $_POST['mo_giorno'] !== '' ? $_POST['mo_giorno'] : null;

    $is_valid = true;
    if (empty($dest)) $is_valid = false;
    if ($giorno && (strtotime($giorno) === false || (int)date('Y', strtotime($giorno)) < 2024 || (int)date('Y', strtotime($giorno)) > 2030)) {
        $is_valid = false;
    }
    if ($giorno && strtotime($giorno) <= strtotime(date('Y-m-d'))) {
        $is_valid = false;
    }

    $costoMezzo   = isset($_POST['mo_costoMezzo'])   && $_POST['mo_costoMezzo']   !== '' ? (float)str_replace(',', '.', $_POST['mo_costoMezzo'])   : null;
    $costoAtt     = isset($_POST['mo_costoAttivita']) && $_POST['mo_costoAttivita'] !== '' ? (float)str_replace(',', '.', $_POST['mo_costoAttivita']) : null;
    $costoAP      = isset($_POST['mo_costoAPersona']) && $_POST['mo_costoAPersona'] !== '' ? (float)str_replace(',', '.', $_POST['mo_costoAPersona']) : null;
    $numAlunni    = isset($_POST['mo_numAlunni']) && $_POST['mo_numAlunni'] !== '' ? (int)$_POST['mo_numAlunni'] : null;
    
    if ($is_valid) {
        $giorno_s     = $giorno ? "'" . $conn->real_escape_string($giorno) . "'" : "NULL";
        $cMezzo_s     = $costoMezzo !== null ? $costoMezzo : "NULL";
        $cAtt_s       = $costoAtt   !== null ? $costoAtt   : "NULL";
        $cAP_s        = $costoAP    !== null ? $costoAP    : "NULL";
        $nAlunni_s    = $numAlunni  !== null ? $numAlunni  : "NULL";
        
        $conn->query("UPDATE gita1g SET destinazione='$dest', descrizione='$desc', mezzo='$mezzo', periodo='$periodo', classi='$classi', giorno=$giorno_s, costoMezzo=$cMezzo_s, costoAttivita=$cAtt_s, costoAPersona=$cAP_s, numAlunni=$nAlunni_s WHERE idGita=$idGita AND idUtente=$idUtenteLoggato");
        $messaggio = "modifica_org_ok";
    } else {
        $messaggio = "errore_validazione";
    }
}

// modifica gita 5g in organizzazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_org_5g') {
    $idGita       = (int)$_POST['id_gita'];
    $dest         = $conn->real_escape_string(trim(isset($_POST['mo_destinazione'])  ? $_POST['mo_destinazione']  : ''));
    $desc         = $conn->real_escape_string(trim(isset($_POST['mo_descrizione'])   ? $_POST['mo_descrizione']   : ''));
    $mezzo        = $conn->real_escape_string(trim(isset($_POST['mo_mezzo'])         ? $_POST['mo_mezzo']         : ''));
    $periodo      = $conn->real_escape_string(trim(isset($_POST['mo_periodo'])       ? $_POST['mo_periodo']       : ''));
    $classi       = $conn->real_escape_string(trim(isset($_POST['mo_classi'])        ? $_POST['mo_classi']        : ''));
    $gi           = isset($_POST['mo_giornoInizio']) && $_POST['mo_giornoInizio'] !== '' ? $_POST['mo_giornoInizio'] : null;
    $gf           = isset($_POST['mo_giornoFine'])   && $_POST['mo_giornoFine']   !== '' ? $_POST['mo_giornoFine']   : null;

    $is_valid = true;
    if (empty($dest)) $is_valid = false;
    if ($gi && (strtotime($gi) === false || (int)date('Y', strtotime($gi)) < 2024 || (int)date('Y', strtotime($gi)) > 2030)) {
        $is_valid = false;
    }
    if ($gf && (strtotime($gf) === false || (int)date('Y', strtotime($gf)) < 2024 || (int)date('Y', strtotime($gf)) > 2030)) {
        $is_valid = false;
    }
    if ($gi && strtotime($gi) <= strtotime(date('Y-m-d'))) {
        $is_valid = false;
    }
    if ($gi && $gf && strtotime($gi) >= strtotime($gf)) {
        $is_valid = false;
    }

    $costoAP      = isset($_POST['mo_costoAPersona']) && $_POST['mo_costoAPersona'] !== '' ? (float)str_replace(',', '.', $_POST['mo_costoAPersona']) : null;
    $numAlunni    = isset($_POST['mo_numAlunni']) && $_POST['mo_numAlunni'] !== '' ? (int)$_POST['mo_numAlunni'] : null;
    
    if ($is_valid) {
        $gi_s         = $gi ? "'" . $conn->real_escape_string($gi) . "'" : "NULL";
        $gf_s         = $gf ? "'" . $conn->real_escape_string($gf) . "'" : "NULL";
        $cAP_s        = $costoAP    !== null ? $costoAP    : "NULL";
        $nAlunni_s    = $numAlunni  !== null ? $numAlunni  : "NULL";
        
        $conn->query("UPDATE gite5 SET destinazione='$dest', descrizione='$desc', mezzo='$mezzo', periodo='$periodo', classi='$classi', giornoInizio=$gi_s, giornoFine=$gf_s, costoAPersona=$cAP_s, numAlunni=$nAlunni_s WHERE idGita=$idGita AND idUtente=$idUtenteLoggato");
        $messaggio = "modifica_org_ok";
    } else {
        $messaggio = "errore_validazione";
    }
}

// organizza gita 1 giorno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'organizza_1g') {
    $idGita      = (int)$_POST['id_gita'];
    $idUtente    = $_SESSION['id_utente'];
    $descrizione = isset($_POST['org_descrizione']) ? $_POST['org_descrizione'] : '';
    $mezzo       = isset($_POST['org_mezzo'])       ? $_POST['org_mezzo']       : '';
    $classi      = isset($_POST['org_classe'])      ? $_POST['org_classe']      : '';
    $periodo     = isset($_POST['org_periodo'])    && $_POST['org_periodo']    !== '' ? $_POST['org_periodo']    : null;
    $giorno      = isset($_POST['org_giorno'])     && $_POST['org_giorno']     !== '' ? $_POST['org_giorno']     : null;
    $costoMezzo  = isset($_POST['org_costoMezzo'])  && $_POST['org_costoMezzo']  !== '' ? (float)$_POST['org_costoMezzo']  : null;
    $costoGiorno = isset($_POST['org_costoGiorno']) && $_POST['org_costoGiorno'] !== '' ? (float)$_POST['org_costoGiorno'] : null;
    $numAlunni   = isset($_POST['org_numAlunni'])   && $_POST['org_numAlunni']   !== '' ? (int)$_POST['org_numAlunni']     : null;

    $orig = $conn->query("SELECT * FROM gita1g WHERE idGita = $idGita")->fetch_assoc();
    if ($orig) {
        $is_valid = true;
        if ($giorno && (strtotime($giorno) === false || (int)date('Y', strtotime($giorno)) < 2024 || (int)date('Y', strtotime($giorno)) > 2030)) {
            $is_valid = false;
        }
        if ($giorno && strtotime($giorno) <= strtotime(date('Y-m-d'))) {
            $is_valid = false;
        }

        if ($is_valid) {
            $dest_s       = $conn->real_escape_string($orig['destinazione']);
            $desc_s       = $conn->real_escape_string($descrizione);
            $mezzoTmp     = !empty($mezzo) ? $mezzo : (isset($orig['mezzo']) ? $orig['mezzo'] : '');
            $mezzoFin_s   = $conn->real_escape_string($mezzoTmp);
            $classi_s     = $conn->real_escape_string($classi);
            $perTmp       = !empty($periodo) ? $periodo : (isset($orig['periodo']) ? $orig['periodo'] : '');
            $perFin_s     = $conn->real_escape_string($perTmp);
            $giorno_s     = $giorno      ? "'" . $conn->real_escape_string($giorno) . "'" : "NULL";
            $costoMezzo_s = $costoMezzo  !== null ? (float)$costoMezzo  : "NULL";
            $costoGiorno_s= $costoGiorno !== null ? (float)$costoGiorno : "NULL";
            $costoA_s     = (float)$orig['costoAPersona'];
            $numAlunni_s  = $numAlunni   !== null ? (int)$numAlunni     : "NULL";

            $sql = "INSERT INTO gita1g (idUtente, destinazione, descrizione, mezzo, periodo, classi, giorno, costoMezzo, costoAttivita, costoAPersona, numAlunni, idStato)
                    VALUES ($idUtente, '$dest_s', '$desc_s', '$mezzoFin_s', '$perFin_s', '$classi_s', $giorno_s, $costoMezzo_s, $costoGiorno_s, $costoA_s, $numAlunni_s, 4)";
            if ($conn->query($sql)) {
                $messaggio = "organizza_ok";
                $newId = $conn->insert_id;
                $conn->query("INSERT IGNORE INTO accompagnatori (idgita, idutente, tipo_gita) VALUES ($newId, $idUtente, '1g')");
            } else {
                $messaggio = "error";
            }
        } else {
            $messaggio = "errore_validazione";
        }
    }
}

// organizza gita piu giorni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'organizza_5g') {
    $idGita       = (int)$_POST['id_gita'];
    $idUtente     = $_SESSION['id_utente'];
    $descrizione  = isset($_POST['org_descrizione'])  ? $_POST['org_descrizione']  : '';
    $mezzo        = isset($_POST['org_mezzo'])         ? $_POST['org_mezzo']         : '';
    $classi       = isset($_POST['org_classe'])        ? $_POST['org_classe']        : '';
    $periodo      = isset($_POST['org_periodo'])      && $_POST['org_periodo']      !== '' ? $_POST['org_periodo']      : null;
    $giornoInizio = isset($_POST['org_giornoInizio']) && $_POST['org_giornoInizio'] !== '' ? $_POST['org_giornoInizio'] : null;
    $giornoFine   = isset($_POST['org_giornoFine'])   && $_POST['org_giornoFine']   !== '' ? $_POST['org_giornoFine']   : null;
    $costoAPersona= isset($_POST['org_costoAPersona']) && $_POST['org_costoAPersona'] !== '' ? (float)$_POST['org_costoAPersona'] : null;
    $numAlunni    = isset($_POST['org_numAlunni'])     && $_POST['org_numAlunni']     !== '' ? (int)$_POST['org_numAlunni']       : null;

    $orig = $conn->query("SELECT * FROM gite5 WHERE idGita = $idGita")->fetch_assoc();
    if ($orig) {
        $is_valid = true;
        if ($giornoInizio && (strtotime($giornoInizio) === false || (int)date('Y', strtotime($giornoInizio)) < 2024 || (int)date('Y', strtotime($giornoInizio)) > 2030)) {
            $is_valid = false;
        }
        if ($giornoFine && (strtotime($giornoFine) === false || (int)date('Y', strtotime($giornoFine)) < 2024 || (int)date('Y', strtotime($giornoFine)) > 2030)) {
            $is_valid = false;
        }
        if ($giornoInizio && strtotime($giornoInizio) <= strtotime(date('Y-m-d'))) {
            $is_valid = false;
        }
        if ($giornoInizio && $giornoFine && strtotime($giornoInizio) >= strtotime($giornoFine)) {
            $is_valid = false;
        }

        if ($is_valid) {
            $dest_s      = $conn->real_escape_string($orig['destinazione']);
            $desc_s      = $conn->real_escape_string($descrizione);
            $mezzoTmp    = !empty($mezzo) ? $mezzo : (isset($orig['mezzo']) ? $orig['mezzo'] : '');
            $mezzoFin_s  = $conn->real_escape_string($mezzoTmp);
            $classi_s    = $conn->real_escape_string($classi);
            $perTmp      = !empty($periodo) ? $periodo : (isset($orig['periodo']) ? $orig['periodo'] : '');
            $perFin_s    = $conn->real_escape_string($perTmp);
            $gi_s        = $giornoInizio ? "'" . $conn->real_escape_string($giornoInizio) . "'" : "NULL";
            $gf_s        = $giornoFine   ? "'" . $conn->real_escape_string($giornoFine)   . "'" : "NULL";
            $costoOrig   = isset($orig['costoAPersona']) ? (float)$orig['costoAPersona'] : 0;
            $costoFin    = $costoAPersona !== null ? (float)$costoAPersona : $costoOrig;
            $numAlunni_s = $numAlunni !== null ? (int)$numAlunni : "NULL";

            $sql = "INSERT INTO gite5 (idUtente, destinazione, descrizione, mezzo, periodo, classi, giornoInizio, giornoFine, costoAPersona, numAlunni, idStato)
                    VALUES ($idUtente, '$dest_s', '$desc_s', '$mezzoFin_s', '$perFin_s', '$classi_s', $gi_s, $gf_s, $costoFin, $numAlunni_s, 4)";
            if ($conn->query($sql)) {
                $messaggio = "organizza_ok";
                $newId = $conn->insert_id;
                $conn->query("INSERT IGNORE INTO accompagnatori (idgita, idutente, tipo_gita) VALUES ($newId, $idUtente, '5g')");
            } else {
                $messaggio = "error";
            }
        } else {
            $messaggio = "errore_validazione";
        }
    }
}

// riproponi (modifica campi e rimetti in bozza)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'riproponi_1g') {
        $idGita       = (int)$_POST['id_gita'];
        $destinazione = $conn->real_escape_string($_POST['destinazione']);
        $mezzo        = $conn->real_escape_string(isset($_POST['mezzo']) ? $_POST['mezzo'] : '');
        $periodo      = $conn->real_escape_string(isset($_POST['periodo']) ? $_POST['periodo'] : '');
        $costo        = isset($_POST['costo']) ? (float)$_POST['costo'] : 0;
        $conn->query("UPDATE gita1g SET destinazione='$destinazione', mezzo='$mezzo', periodo='$periodo', costoAPersona=$costo, idStato=1, motivazione=NULL WHERE idGita=$idGita AND idUtente=$idUtenteLoggato");
        $messaggio = "ok";
    }

    if ($_POST['action'] === 'riproponi_5g') {
        $idGita       = (int)$_POST['id_gita'];
        $destinazione = $conn->real_escape_string($_POST['destinazione']);
        $mezzo        = $conn->real_escape_string(isset($_POST['mezzo']) ? $_POST['mezzo'] : '');
        $periodo      = $conn->real_escape_string(isset($_POST['periodo']) ? $_POST['periodo'] : '');
        $costo        = isset($_POST['costo']) ? (float)$_POST['costo'] : 0;
        $conn->query("UPDATE gite5 SET destinazione='$destinazione', mezzo='$mezzo', periodo='$periodo', costoAPersona=$costo, idStato=1, motivazione=NULL WHERE idGita=$idGita AND idUtente=$idUtenteLoggato");
        $messaggio = "ok";
    }

    if ($_POST['action'] === 'elimina_bocciata_1g') {
        $idGita = (int)$_POST['id_gita'];
        $conn->query("DELETE FROM gita1g WHERE idGita=$idGita AND idUtente=$idUtenteLoggato AND idStato=3");
        $messaggio = "eliminata";
    }

    if ($_POST['action'] === 'elimina_bocciata_5g') {
        $idGita = (int)$_POST['id_gita'];
        $conn->query("DELETE FROM gite5 WHERE idGita=$idGita AND idUtente=$idUtenteLoggato AND idStato=3");
        $messaggio = "eliminata";
    }
}

// query proposte create da me
$proposte = [];
$r1 = $conn->query("SELECT *, '1g' AS tipo FROM gita1g WHERE idUtente = $idUtenteLoggato AND idStato IN (1,2,3) ORDER BY idGita DESC");
if ($r1) { while ($riga = $r1->fetch_assoc()) $proposte[] = $riga; }
$r2 = $conn->query("SELECT *, '5g' AS tipo FROM gite5 WHERE idUtente = $idUtenteLoggato AND idStato IN (1,2,3) ORDER BY idGita DESC");
if ($r2) { while ($riga = $r2->fetch_assoc()) $proposte[] = $riga; }

// query gite in organizzazione e concluse (in piu parti, senza LEFT JOIN)
$organizzateMap = [];

// gite 1g create da me
$r = $conn->query("SELECT *, '1g' AS tipo, 1 AS sono_autore, 0 AS sono_accompagnatore FROM gita1g WHERE idUtente = $idUtenteLoggato AND idStato IN (4,5)");
if ($r) while ($row = $r->fetch_assoc()) $organizzateMap['1g_' . $row['idGita']] = $row;

// gite 1g dove sono accompagnatore
$r = $conn->query("SELECT g.*, '1g' AS tipo, 0 AS sono_autore, 1 AS sono_accompagnatore FROM gita1g g JOIN accompagnatori a ON a.idgita = g.idGita AND a.tipo_gita = '1g' WHERE a.idutente = $idUtenteLoggato AND g.idStato IN (4,5)");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $k = '1g_' . $row['idGita'];
        if (isset($organizzateMap[$k])) {
            $organizzateMap[$k]['sono_accompagnatore'] = 1;
        } else {
            $organizzateMap[$k] = $row;
        }
    }
}

// gite 5g create da me
$r = $conn->query("SELECT *, '5g' AS tipo, 1 AS sono_autore, 0 AS sono_accompagnatore FROM gite5 WHERE idUtente = $idUtenteLoggato AND idStato IN (4,5)");
if ($r) while ($row = $r->fetch_assoc()) $organizzateMap['5g_' . $row['idGita']] = $row;

// gite 5g dove sono accompagnatore
$r = $conn->query("SELECT g.*, '5g' AS tipo, 0 AS sono_autore, 1 AS sono_accompagnatore FROM gite5 g JOIN accompagnatori a ON a.idgita = g.idGita AND a.tipo_gita = '5g' WHERE a.idutente = $idUtenteLoggato AND g.idStato IN (4,5)");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $k = '5g_' . $row['idGita'];
        if (isset($organizzateMap[$k])) {
            $organizzateMap[$k]['sono_accompagnatore'] = 1;
        } else {
            $organizzateMap[$k] = $row;
        }
    }
}

// escludi archiviate
$arch = $conn->query("SELECT idgita, tipo_gita FROM gite_archiviate_utente WHERE idutente = $idUtenteLoggato");
if ($arch) while ($a = $arch->fetch_assoc()) unset($organizzateMap[$a['tipo_gita'] . '_' . $a['idgita']]);

// ordina bubble sort per idGita discendente
$organizzate = array_values($organizzateMap);
$n = count($organizzate);
for ($i = 0; $i < $n; $i++) {
    for ($j = 0; $j < $n - $i - 1; $j++) {
        if ($organizzate[$j]['idGita'] < $organizzate[$j + 1]['idGita']) {
            $temp = $organizzate[$j];
            $organizzate[$j] = $organizzate[$j + 1];
            $organizzate[$j + 1] = $temp;
        }
    }
}

//per modificare il colore
function badgeClass($stato) {
    if ($stato === 'Approvata')      return 'badge-success';
    if ($stato === 'Bocciata')       return 'badge-danger';
    if ($stato === 'Bozza')          return 'badge-warning';
    if ($stato === 'Organizzazione') return 'badge-primary';
    if ($stato === 'Conclusa')       return 'badge-secondary';
    return 'badge-secondary';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Mie Gite</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css?v=<?php echo time(); ?>">
    <script src="vetrina.js" defer></script>
    <script>
    function apriArchivia(id, tipo, sonoAutore, dest) {
        document.getElementById('archiviaId').value = id;
        document.getElementById('archiviaTipo').value = tipo;
        document.getElementById('archiviaAutore').value = sonoAutore;
        document.getElementById('archiviaDest').innerText = dest;
        document.getElementById('modalArchivia').classList.remove('hidden');
    }
    function chiudiArchivia() {
        document.getElementById('modalArchivia').classList.add('hidden');
    }
    </script>
</head>
<body>
<div class="container">
<main class="content bozze-padding" style="display:flex;flex-direction:column;">

<?php if ($messaggio === 'ok'): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ document.getElementById('modalRiproponiOk').classList.remove('hidden'); });</script>
<?php endif; ?>
<?php if ($messaggio === 'organizza_ok'): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ document.getElementById('modalOrganizzaOk').classList.remove('hidden'); });</script>
<?php endif; ?>
<?php if ($messaggio === 'modifica_org_ok'): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ document.getElementById('modalModOrgOk').classList.remove('hidden'); });</script>
<?php endif; ?>
<?php if ($messaggio === 'eliminata'): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ document.getElementById('modalEliminataOk').classList.remove('hidden'); });</script>
<?php endif; ?>
<?php if ($messaggio === 'errore_validazione'): ?>
<script>document.addEventListener('DOMContentLoaded',function(){ document.getElementById('modalErroreValidazione').classList.remove('hidden'); });</script>
<?php endif; ?>

<?php if ($messaggio === 'disiscritto_ok'): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">
    Ti sei disiscritto correttamente dalla gita. La trovi in <a href="inProgramma.php" style="color:inherit;text-decoration:underline;font-weight:bold;">In Programma</a>.
</div>
<?php endif; ?>

<?php if ($messaggio === 'partecipato_ok'): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">
    Ti sei iscritto correttamente alla gita. La trovi ora in <a href="mieGite.php" style="color:inherit;text-decoration:underline;font-weight:bold;">Le mie Gite</a>.
</div>
<?php endif; ?>

<?php if ($messaggio === 'archiviata_ok'): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">
    La gita conclusa è stata rimossa dalla visualizzazione.
</div>
<?php endif; ?>

<?php if (isset($_GET['organizzata'])): ?>
<div class="alert alert-success" style="margin-bottom:1rem;">
    Gita messa in organizzazione con successo. La trovi ora in <a href="mieGite.php" style="color:inherit;text-decoration:underline;font-weight:bold;">Le mie Gite</a>.
</div>
<?php endif; ?>

<!-- sezione 1: proposte create da me -->
<div class="table-section" style="margin-top:3rem;order:2;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;">
        <h3 style="margin:0;color:var(--blue-700);">Proposte create da me</h3>
        <span style="font-size:0.85rem;color:var(--blue-400);"><?php echo count($proposte); ?> proposta<?php echo count($proposte) != 1 ? 'e' : ''; ?></span>
    </div>

    <?php if (count($proposte) === 0): ?>
        <p style="color:#64748b;font-style:italic;">Non hai ancora creato nessuna proposta.</p>
    <?php else: ?>
    <div class="miegite-grid">
        <?php foreach ($proposte as $riga):
            $tipo      = $riga['tipo'] === '1g' ? 'Gita 1 Giorno' : 'Gita Più Giorni';
            $dest      = htmlspecialchars($riga['destinazione']);
            $destJs    = htmlspecialchars($riga['destinazione']);
            $mezzo     = htmlspecialchars(isset($riga['mezzo']) ? $riga['mezzo'] : '');
            $mezzoJs   = htmlspecialchars(isset($riga['mezzo']) ? $riga['mezzo'] : '');
            $periodo   = htmlspecialchars(isset($riga['periodo']) ? $riga['periodo'] : '');
            $periodoJs = htmlspecialchars(isset($riga['periodo']) ? $riga['periodo'] : '');
            $descJs    = htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : '');
            $classiJs  = htmlspecialchars(isset($riga['classi']) ? $riga['classi'] : '');
            $descDisp  = htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : '');
            $costoRaw  = isset($riga['costoAPersona']) ? $riga['costoAPersona'] : 0;
            $costo     = $costoRaw !== null ? '€ ' . number_format($costoRaw, 2, ',', '.') : '—';
            $stato     = nomeStato($riga['idStato']);
            $badge     = badgeClass($stato);
            $id        = (int)$riga['idGita'];
            $tipoTabella = $riga['tipo'];

            if ($riga['tipo'] === '1g') {
                $data = formattaData($riga['giorno']);
                $dataLabel = 'Giorno';
            } else {
                $ini = formattaData($riga['giornoInizio']);
                $fin = formattaData($riga['giornoFine']);
                $data = "$ini → $fin";
                $dataLabel = 'Date';
            }
        ?>
        <div class="miegite-card">
            <div class="miegite-card-header">
                <h4 class="miegite-card-title"><?php echo $dest; ?></h4>
                <?php
                if ($riga['tipo'] === '1g') {
                    echo badgeStatoHtml($riga['idStato'], isset($riga['giorno']) ? $riga['giorno'] : null);
                } else {
                    echo badgeStatoHtml($riga['idStato'], isset($riga['giornoInizio']) ? $riga['giornoInizio'] : null, isset($riga['giornoFine']) ? $riga['giornoFine'] : null);
                }
                ?>
            </div>
            <div class="miegite-card-body">
                <div class="miegite-card-info">
                    <span><strong>Tipo:</strong> <?php echo $tipo; ?></span>
                    <span><strong>Mezzo:</strong> <?php echo !empty($mezzo) ? $mezzo : '—'; ?></span>
                    <span><strong>Periodo:</strong> <?php echo !empty($periodo) ? $periodo : '—'; ?></span>
                    <span><strong><?php echo $dataLabel; ?>:</strong> <?php echo $data; ?></span>
                    <span><strong>Costo a persona:</strong> <?php echo $costo; ?></span>
                    <span><strong>Descrizione:</strong> <?php echo $descDisp ? $descDisp : '—'; ?></span>
                    <?php if ($riga['idStato'] == 3 && !empty($riga['motivazione'])): ?>
                        <div class="motivo-bocciatura-box">
                            <strong>Motivo bocciatura:</strong>
                            <div class="motivo-bocciatura-testo"><?php echo htmlspecialchars($riga['motivazione']); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($stato === 'Approvata'): ?>
            <div class="miegite-card-footer">
                <button type="button" class="button xs"
                    data-id="<?php echo $id; ?>"
                    data-dest="<?php echo $destJs; ?>"
                    data-desc="<?php echo $descJs; ?>"
                    data-mezzo="<?php echo $mezzoJs; ?>"
                    data-periodo="<?php echo $periodoJs; ?>"
                    data-classi="<?php echo $classiJs; ?>"
                    data-costo="<?php echo (float)$costoRaw; ?>"
                    data-tipo="<?php echo $tipoTabella; ?>"
                    onclick="apriOrg(this)">
                    Organizza
                </button>
            </div>
            <?php elseif ($stato === 'Bocciata'): ?>
            <div class="miegite-card-footer">
                <button type="button" class="button xs"
                    onclick="apriModifica(<?php echo $id; ?>,'<?php echo $destJs; ?>','<?php echo $mezzoJs; ?>','<?php echo $periodoJs; ?>',<?php echo (float)$costoRaw; ?>,'<?php echo $tipoTabella; ?>')">
                    Modifica
                </button>
                <button type="button" class="button cancel xs"
                    onclick="apriElimina(<?php echo $id; ?>,'<?php echo $destJs; ?>','<?php echo $tipoTabella; ?>')">
                    Elimina
                </button>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- sezione 2: gite in organizzazione / concluse -->
<div class="table-section" style="margin-top:0;order:1;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;">
        <h3 style="margin:0;color:var(--blue-700);">Gite che sto organizzando</h3>
        <span style="font-size:0.85rem;color:var(--blue-400);"><?php echo count($organizzate); ?> gita<?php echo count($organizzate) != 1 ? 'e' : ''; ?></span>
    </div>

    <?php if (count($organizzate) === 0): ?>
        <p style="color:#64748b;font-style:italic;">Non stai organizzando nessuna gita al momento.</p>
    <?php else: ?>
    <div class="miegite-grid">
        <?php foreach ($organizzate as $riga):
            $tipo    = $riga['tipo'] === '1g' ? 'Gita 1 Giorno' : 'Gita Più Giorni';
            $dest    = htmlspecialchars($riga['destinazione']);
            $destJs  = htmlspecialchars($riga['destinazione']);
            $mezzo   = htmlspecialchars(isset($riga['mezzo']) ? $riga['mezzo'] : '—');
            $mezzoJs = htmlspecialchars(isset($riga['mezzo']) ? $riga['mezzo'] : '');
            $periodo = htmlspecialchars(isset($riga['periodo']) ? $riga['periodo'] : '—');
            $periodoJs = htmlspecialchars(isset($riga['periodo']) ? $riga['periodo'] : '');
            $descJs  = htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : '');
            $descDisp = htmlspecialchars(isset($riga['descrizione']) ? $riga['descrizione'] : '');
            $classiJs= htmlspecialchars(isset($riga['classi']) ? $riga['classi'] : '');
            $costo   = $riga['costoAPersona'] !== null ? '€ ' . number_format($riga['costoAPersona'], 2, ',', '.') : '—';
            $stato   = nomeStato($riga['idStato']);
            $badge   = badgeClass($stato);
            $numAl   = $riga['numAlunni'] !== null ? $riga['numAlunni'] : '—';
            $id      = (int)$riga['idGita'];
            $tipoTabella = $riga['tipo'];
            if ($riga['tipo'] === '1g') {
                $dataRaw   = isset($riga['giorno']) ? $riga['giorno'] : '';
                $data      = formattaData($dataRaw);
                $dataLabel = 'Giorno';
                $costoMezzoRaw = isset($riga['costoMezzo']) ? $riga['costoMezzo'] : '';
                $costoAttRaw   = isset($riga['costoAttivita']) ? $riga['costoAttivita'] : '';
                $costoAPRaw    = isset($riga['costoAPersona']) ? $riga['costoAPersona'] : '';
                $costoMezzo = $costoMezzoRaw !== null && $costoMezzoRaw !== '' ? '€ ' . number_format($costoMezzoRaw, 2, ',', '.') : '—';
                $costoAtt   = $costoAttRaw   !== null && $costoAttRaw   !== '' ? '€ ' . number_format($costoAttRaw,   2, ',', '.') : '—';
                $extraInfo  = "<span><strong>Costo mezzo:</strong> $costoMezzo</span><span><strong>Costo attività:</strong> $costoAtt</span>";
            } else {
                $dataRaw = '';
                $ini = formattaData($riga['giornoInizio']);
                $fin = formattaData($riga['giornoFine']);
                $data      = "$ini → $fin";
                $dataLabel = 'Date';
                $extraInfo = "";
                $costoMezzoRaw = '';
                $costoAttRaw   = '';
                $costoAPRaw    = isset($riga['costoAPersona']) ? $riga['costoAPersona'] : '';
            }
        ?>
        <div class="miegite-card">
            <div class="miegite-card-header">
                <h4 class="miegite-card-title"><?php echo $dest; ?></h4>
                <?php
                if ($riga['tipo'] === '1g') {
                    echo badgeStatoHtml($riga['idStato'], isset($riga['giorno']) ? $riga['giorno'] : null);
                } else {
                    echo badgeStatoHtml($riga['idStato'], isset($riga['giornoInizio']) ? $riga['giornoInizio'] : null, isset($riga['giornoFine']) ? $riga['giornoFine'] : null);
                }
                ?>
            </div>
            <div class="miegite-card-body">
                <div class="miegite-card-info">
                    <span><strong>Tipo:</strong> <?php echo $tipo; ?></span>
                    <span><strong>Mezzo:</strong> <?php echo $mezzo; ?></span>
                    <span><strong>Periodo:</strong> <?php echo $periodo; ?></span>
                    <span><strong><?php echo $dataLabel; ?>:</strong> <?php echo $data; ?></span>
                    <span><strong>Costo a persona:</strong> <?php echo $costo; ?></span>
                    <span><strong>Num. alunni:</strong> <?php echo $numAl; ?></span>
                    <span><strong>Descrizione:</strong> <?php echo $descDisp ? $descDisp : '—'; ?></span>
                    <?php echo $extraInfo; ?>
                </div>
            </div>
            <div class="miegite-card-footer" style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                <?php if ($riga['idStato'] != 5): ?>
                    <?php if ($riga['sono_autore'] == 1): ?>
                    <button type="button" class="button xs"
                        data-id="<?php echo $id; ?>"
                        data-tipo="<?php echo $tipoTabella; ?>"
                        data-dest="<?php echo $destJs; ?>"
                        data-desc="<?php echo $descJs; ?>"
                        data-mezzo="<?php echo $mezzoJs; ?>"
                        data-periodo="<?php echo $periodoJs; ?>"
                        data-classi="<?php echo $classiJs; ?>"
                        data-giorno="<?php echo htmlspecialchars($riga['tipo']==='1g' ? (isset($riga['giorno']) ? $riga['giorno'] : '') : ''); ?>"
                        data-giorno-inizio="<?php echo htmlspecialchars($riga['tipo']==='5g' ? (isset($riga['giornoInizio']) ? $riga['giornoInizio'] : '') : ''); ?>"
                        data-giorno-fine="<?php echo htmlspecialchars($riga['tipo']==='5g' ? (isset($riga['giornoFine']) ? $riga['giornoFine'] : '') : ''); ?>"
                        data-costo-mezzo="<?php echo $costoMezzoRaw; ?>"
                        data-costo-att="<?php echo $costoAttRaw; ?>"
                        data-costo-ap="<?php echo $costoAPRaw; ?>"
                        data-num-alunni="<?php echo isset($riga['numAlunni']) ? $riga['numAlunni'] : ''; ?>"
                        onclick="apriModOrg(this)">
                        Modifica
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($riga['sono_accompagnatore'] == 1): ?>
                    <form method="POST" action="mieGite.php" style="margin:0;">
                        <input type="hidden" name="action"    value="disiscriviti">
                        <input type="hidden" name="id_gita"   value="<?php echo $id; ?>">
                        <input type="hidden" name="tipo_gita" value="<?php echo $tipoTabella; ?>">
                        <button type="submit" class="button cancel xs">Disiscriviti</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="mieGite.php" style="margin:0;">
                        <input type="hidden" name="action"    value="partecipa">
                        <input type="hidden" name="id_gita"   value="<?php echo $id; ?>">
                        <input type="hidden" name="tipo_gita" value="<?php echo $tipoTabella; ?>">
                        <button type="submit" class="button xs">Partecipa</button>
                    </form>
                    <?php endif; ?>
                <?php else: ?>
                    <button type="button" class="button cancel-outline xs"
                        onclick="apriArchivia(<?php echo $id; ?>, '<?php echo $tipoTabella; ?>', <?php echo $riga['sono_autore']; ?>, '<?php echo $destJs; ?>')">
                        Rimuovi
                    </button>
                <?php endif; ?>
                <?php if ($riga['tipo'] === '1g'):
                    $accRes = $conn->query("SELECT CONCAT(u.Nome,' ',u.Cognome) AS nome FROM accompagnatori a JOIN utente u ON a.idutente=u.IDUtente WHERE a.idgita={$riga['idGita']} AND a.tipo_gita='1g' ORDER BY u.Cognome,u.Nome");
                    $accList = [];
                    if ($accRes) { while ($aRiga = $accRes->fetch_assoc()) $accList[] = $aRiga['nome']; }
                    $accJson = htmlspecialchars(json_encode($accList));
                ?>
                <button type="button" class="button xs outline"
                    data-dest="<?php echo $destJs; ?>"
                    data-acc="<?php echo $accJson; ?>"
                    onclick="apriAccompagnatori(this)">Accompagnatori</button>
                <?php endif; ?>
                <?php if ($riga['tipo'] === '5g'): ?>
                <a href="partecipanti.php?id=<?php echo $riga['idGita']; ?>" class="button xs" style="text-decoration:none;">Partecipanti</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

</main>

<!-- modal: modifica gita 1g in organizzazione -->
<div class="modal-overlay hidden" id="modalModOrg1g">
<div class="modal wide-modal">
<div class="modal-header">
    <h3 id="modOrg1gTitle">Modifica Gita 1 Giorno</h3>
    <button class="close-btn" onclick="document.getElementById('modalModOrg1g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<form id="formModOrg1g" method="POST" action="mieGite.php">
    <input type="hidden" name="action"  value="modifica_org_1g">
    <input type="hidden" name="id_gita" id="modOrg1g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="mo_destinazione" id="modOrg1g_dest" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="mo_descrizione" id="modOrg1g_desc" class="form-control" placeholder="Breve descrizione">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <select name="mo_mezzo" id="modOrg1g_mezzo" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Bus">Bus</option>
                <option value="Treno">Treno</option>
                <option value="Ci incontriamo direttamente lì">Ci incontriamo direttamente lì</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="mo_periodo" id="modOrg1g_periodo" class="form-control">
        </div>
        <div class="form-group">
            <label>Classe/i</label>
            <input type="text" name="mo_classi" id="modOrg1g_classi" class="form-control" placeholder="es. 3A">
        </div>
        <div class="form-group">
            <label>Giorno</label>
            <input type="date" name="mo_giorno" id="modOrg1g_giorno" class="form-control" min="2024-01-01" max="2030-12-31">
        </div>
        <div class="form-group">
            <label>Costo Mezzo (&euro;)</label>
            <input type="text" name="mo_costoMezzo" id="modOrg1g_costoMezzo" class="form-control" placeholder="es. 10,50">
        </div>
        <div class="form-group">
            <label>Costo Attività (&euro;)</label>
            <input type="text" name="mo_costoAttivita" id="modOrg1g_costoAtt" class="form-control" placeholder="es. 5,00">
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;)</label>
            <input type="text" name="mo_costoAPersona" id="modOrg1g_costoAP" class="form-control" placeholder="es. 15,50">
        </div>
        <div class="form-group">
            <label>Num. Alunni</label>
            <input type="number" name="mo_numAlunni" id="modOrg1g_numAlunni" class="form-control" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalModOrg1g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formModOrg1g" class="button">Salva</button>
</div>
</div>
</div>

<!-- modal: modifica gita 5g in organizzazione -->
<div class="modal-overlay hidden" id="modalModOrg5g">
<div class="modal wide-modal">
<div class="modal-header">
    <h3 id="modOrg5gTitle">Modifica Gita Più Giorni</h3>
    <button class="close-btn" onclick="document.getElementById('modalModOrg5g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<form id="formModOrg5g" method="POST" action="mieGite.php">
    <input type="hidden" name="action"  value="modifica_org_5g">
    <input type="hidden" name="id_gita" id="modOrg5g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="mo_destinazione" id="modOrg5g_dest" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="mo_descrizione" id="modOrg5g_desc" class="form-control" placeholder="Breve descrizione">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <select name="mo_mezzo" id="modOrg5g_mezzo" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Bus GT">Bus GT</option>
                <option value="Treno">Treno</option>
                <option value="Aereo">Aereo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="mo_periodo" id="modOrg5g_periodo" class="form-control">
        </div>
        <div class="form-group">
            <label>Classe/i</label>
            <input type="text" name="mo_classi" id="modOrg5g_classi" class="form-control" placeholder="es. 4B">
        </div>
        <div class="form-group">
            <label>Giorno Inizio</label>
            <input type="date" name="mo_giornoInizio" id="modOrg5g_gi" class="form-control" min="2024-01-01" max="2030-12-31">
        </div>
        <div class="form-group">
            <label>Giorno Fine</label>
            <input type="date" name="mo_giornoFine" id="modOrg5g_gf" class="form-control" min="2024-01-01" max="2030-12-31">
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;)</label>
            <input type="text" name="mo_costoAPersona" id="modOrg5g_costoAP" class="form-control" placeholder="es. 150,00">
        </div>
        <div class="form-group">
            <label>Num. Alunni</label>
            <input type="number" name="mo_numAlunni" id="modOrg5g_numAlunni" class="form-control" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalModOrg5g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formModOrg5g" class="button">Salva</button>
</div>
</div>
</div>

<!-- modal: modifica salvata -->
<div class="modal-overlay hidden" id="modalModOrgOk">
<div class="modal" style="text-align:center;max-width:400px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalModOrgOk').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--blue-700);margin-bottom:0.5rem;">Modifiche Salvate</h3>
    <p style="color:#475569;">I dati della gita sono stati aggiornati con successo. Visualizza in <a href="mieGite.php" style="color:var(--blue-600);text-decoration:underline;font-weight:bold;">Le mie Gite</a>.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button" onclick="document.getElementById('modalModOrgOk').classList.add('hidden')">OK</button>
</div>
</div>
</div>

<!-- modal: modifica e riproponi -->
<div class="modal-overlay hidden" id="modalModifica">
<div class="modal">
<div class="modal-header">
    <h3 id="modTitolo">Modifica Proposta</h3>
    <button class="close-btn" onclick="chiudiModifica()">&times;</button>
</div>
<div class="modal-body">
<form id="formModifica" method="POST" action="mieGite.php">
    <input type="hidden" name="action" id="modAction">
    <input type="hidden" name="id_gita" id="modId">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="destinazione" id="modDest" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <input type="text" name="mezzo" id="modMezzo" class="form-control">
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="periodo" id="modPeriodo" class="form-control" placeholder="es. Marzo 2026">
        </div>
        <div class="form-group">
            <label>Costo a persona (&euro;)</label>
            <input type="number" name="costo" id="modCosto" class="form-control" step="0.50" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="chiudiModifica()">Annulla</button>
    <button type="submit" form="formModifica" class="button">Proponi di Nuovo</button>
</div>
</div>
</div>

<!-- modal: conferma elimina -->
<div class="modal-overlay hidden" id="modalElimina">
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="chiudiElimina()">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--hex-red);margin-bottom:0.5rem;">Conferma Eliminazione</h3>
    <p style="color:var(--blue-900);margin-bottom:0.5rem;">Sei sicuro di voler eliminare la gita verso:</p>
    <p style="font-weight:600;color:var(--blue-700);font-size:1.1rem;margin-bottom:0.5rem;" id="elimDestTxt"></p>
    <p style="color:#64748b;font-size:0.9rem;">L'operazione non è reversibile.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button type="button" class="button cancel-outline" onclick="chiudiElimina()">Annulla</button>
    <button type="submit" form="formElimina" class="button cancel">Elimina</button>
</div>
</div>
</div>
<form id="formElimina" method="POST" action="mieGite.php" style="display:none;">
    <input type="hidden" name="action" id="elimAction">
    <input type="hidden" name="id_gita" id="elimId">
</form>

<!-- modal: organizza gita 1 giorno -->
<div class="modal-overlay hidden" id="modalOrg1g">
<div class="modal">
<div class="modal-header">
    <h3 id="org1g_title">Organizza Gita 1 Giorno</h3>
    <button class="close-btn" onclick="document.getElementById('modalOrg1g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<p style="font-size:0.9rem; color:var(--hex-red); margin-bottom:1rem; text-align:center;">I campi contrassegnati con l'asterisco (*) devono essere compilati obbligatoriamente.</p>
<form id="formOrg1g" method="POST" action="mieGite.php">
    <input type="hidden" name="action"  value="organizza_1g">
    <input type="hidden" name="id_gita" id="org1g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione</label>
            <input type="text" id="org1g_mezzo_dest" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label>Descrizione *</label>
            <input type="text" name="org_descrizione" id="org1g_descrizione" class="form-control" placeholder="Breve descrizione" required>
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto *</label>
            <select name="org_mezzo" id="org1g_mezzo" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="Bus">Bus</option>
                <option value="Treno">Treno</option>
                <option value="Ci incontriamo direttamente lì">Ci incontriamo direttamente lì</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo *</label>
            <input type="text" name="org_periodo" id="org1g_periodo" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Classe/i *</label>
            <input type="text" name="org_classe" id="org1g_classe" class="form-control" placeholder="es. 3A" required>
        </div>
        <div class="form-group">
            <label>Giorno *</label>
            <input type="date" name="org_giorno" id="org1g_giorno" class="form-control" required max="2030-12-31">
            <small id="org1g_giorno_error" style="color:var(--hex-red);display:block;margin-top:0.25rem;"></small>
        </div>
        <div class="form-group">
            <label>Costo Mezzo (&euro;) *</label>
            <input type="number" name="org_costoMezzo" id="org1g_costoMezzo" class="form-control" step="0.50" min="0" required>
        </div>
        <div class="form-group">
            <label>Costo Giornata (&euro;) *</label>
            <input type="number" name="org_costoGiorno" id="org1g_costoGiorno" class="form-control" step="0.50" min="0" required>
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;) *</label>
            <input type="number" name="org_costoPersona" id="org1g_costoPersona" class="form-control" step="0.50" min="0" required>
        </div>
        <div class="form-group">
            <label>Num. Alunni *</label>
            <input type="number" name="org_numAlunni" id="org1g_numAlunni" class="form-control" min="0" required>
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalOrg1g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formOrg1g" class="button">Organizza</button>
</div>
</div>
</div>

<!-- modal: organizza gita piu giorni -->
<div class="modal-overlay hidden" id="modalOrg5g">
<div class="modal">
<div class="modal-header">
    <h3 id="org5g_title">Organizza Gita Più Giorni</h3>
    <button class="close-btn" onclick="document.getElementById('modalOrg5g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<p style="font-size:0.9rem; color:var(--hex-red); margin-bottom:1rem; text-align:center;">I campi contrassegnati con l'asterisco (*) devono essere compilati obbligatoriamente.</p>
<form id="formOrg5g" method="POST" action="mieGite.php">
    <input type="hidden" name="action"  value="organizza_5g">
    <input type="hidden" name="id_gita" id="org5g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione</label>
            <input type="text" id="org5g_mezzo_dest" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label>Descrizione *</label>
            <input type="text" name="org_descrizione" id="org5g_descrizione" class="form-control" placeholder="Breve descrizione" required>
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto *</label>
            <select name="org_mezzo" id="org5g_mezzo" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="Bus GT">Bus GT</option>
                <option value="Treno">Treno</option>
                <option value="Aereo">Aereo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo *</label>
            <input type="text" name="org_periodo" id="org5g_periodo" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Classe/i *</label>
            <input type="text" name="org_classe" id="org5g_classe" class="form-control" placeholder="es. 4B" required>
        </div>
        <div class="form-group">
            <label>Giorno Inizio *</label>
            <input type="date" name="org_giornoInizio" id="org5g_giornoInizio" class="form-control" required max="2030-12-31">
            <small id="org5g_giornoInizio_error" style="color:var(--hex-red);display:block;margin-top:0.25rem;"></small>
        </div>
        <div class="form-group">
            <label>Giorno Fine *</label>
            <input type="date" name="org_giornoFine" id="org5g_giornoFine" class="form-control" required max="2030-12-31">
            <small id="org5g_giornoFine_error" style="color:var(--hex-red);display:block;margin-top:0.25rem;"></small>
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;) *</label>
            <input type="number" name="org_costoAPersona" id="org5g_costoAPersona" class="form-control" step="0.50" min="0" required>
        </div>
        <div class="form-group">
            <label>Num. Alunni *</label>
            <input type="number" name="org_numAlunni" id="org5g_numAlunni" class="form-control" min="0" required>
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalOrg5g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formOrg5g" class="button">Organizza</button>
</div>
</div>
</div>

<!-- modal: organizzata con successo -->
<div class="modal-overlay hidden" id="modalOrganizzaOk">
<div class="modal" style="text-align:center;max-width:400px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalOrganizzaOk').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--blue-700);margin-bottom:0.5rem;">Gita Organizzata</h3>
    <p style="color:#475569;">La gita è stata messa in organizzazione con successo. La trovi ora in <a href="mieGite.php" style="color:var(--blue-600);text-decoration:underline;font-weight:bold;">Le mie Gite</a>.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button" onclick="document.getElementById('modalOrganizzaOk').classList.add('hidden')">OK</button>
</div>
</div>
</div>

<!-- modal: riproposta salvata -->
<div class="modal-overlay hidden" id="modalRiproponiOk">
<div class="modal" style="text-align:center;max-width:400px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalRiproponiOk').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--blue-700);margin-bottom:0.5rem;">Proposta Inviata</h3>
    <p style="color:#475569;">La gita è stata rimessa in bozza e inviata per approvazione. Puoi seguirne lo stato in <a href="mieGite.php" style="color:var(--blue-600);text-decoration:underline;font-weight:bold;">Le mie Gite</a>.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button" onclick="document.getElementById('modalRiproponiOk').classList.add('hidden')">OK</button>
</div>
</div>
</div>

<!-- modal: eliminata -->
<div class="modal-overlay hidden" id="modalEliminataOk">
<div class="modal" style="text-align:center;max-width:400px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalEliminataOk').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:#dc2626;margin-bottom:0.5rem;">Gita Eliminata</h3>
    <p style="color:#475569;">La proposta è stata eliminata correttamente.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button" onclick="document.getElementById('modalEliminataOk').classList.add('hidden')">OK</button>
</div>
</div>
</div>

<!-- modal: errore validazione -->
<div class="modal-overlay hidden" id="modalErroreValidazione">
<div class="modal" style="text-align:center;max-width:400px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalErroreValidazione').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:#dc2626;margin-bottom:0.5rem;">Errore di Validazione</h3>
    <p style="color:#475569;">I dati inseriti non sono validi o sono incompleti. Controlla che le date siano corrette e tutti i campi obbligatori siano compilati.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button" onclick="document.getElementById('modalErroreValidazione').classList.add('hidden')">OK</button>
</div>
</div>
</div>

<!-- modal: conferma archiviazione -->
<div class="modal-overlay hidden" id="modalArchivia">
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="chiudiArchivia()">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--hex-red);margin-bottom:0.5rem;">Conferma Rimozione</h3>
    <p style="color:var(--blue-900);margin-bottom:0.5rem;">Vuoi rimuovere questa gita conclusa verso:</p>
    <p style="font-weight:600;color:var(--blue-700);font-size:1.1rem;margin-bottom:0.5rem;" id="archiviaDest"></p>
    <p style="color:#64748b;font-size:0.9rem;">I dati non verranno eliminati dal database.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button type="button" class="button cancel-outline" onclick="chiudiArchivia()">Annulla</button>
    <button type="submit" form="formArchivia" class="button cancel">Rimuovi</button>
</div>
</div>
</div>
<form id="formArchivia" method="POST" action="mieGite.php" style="display:none;">
    <input type="hidden" name="action" value="archivia">
    <input type="hidden" name="id_gita" id="archiviaId">
    <input type="hidden" name="tipo_gita" id="archiviaTipo">
    <input type="hidden" name="sono_autore" id="archiviaAutore">
</form>

<?php include('footer.php'); ?>
</div>

<!-- modal: accompagnatori gita 1g -->
<div class="modal-overlay hidden" id="modalAccompagnatori">
<div class="modal" style="max-width:480px;">
<div class="modal-header">
    <h3 id="accModalTit">Accompagnatori</h3>
    <button class="close-btn" onclick="document.getElementById('modalAccompagnatori').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
    <div id="accModalList"></div>
</div>
<div class="modal-footer">
    <button class="button cancel" onclick="document.getElementById('modalAccompagnatori').classList.add('hidden')">Chiudi</button>
</div>
</div>
</div>

<script>
function apriAccompagnatori(btn) {
    var dest = btn.dataset.dest;
    var acc  = JSON.parse(btn.dataset.acc);
    document.getElementById('accModalTit').textContent = 'Accompagnatori — ' + dest;
    var html = acc.length > 0
        ? '<ul style="list-style:none;padding:0;margin:0;">' + acc.map(function(n){ return '<li style="padding:0.4rem 0;border-bottom:1px solid #e2e8f0;">' + n + '</li>'; }).join('') + '</ul>'
        : '<p style="color:#94a3b8;text-align:center;padding:1rem 0;">Nessun accompagnatore registrato.</p>';
    document.getElementById('accModalList').innerHTML = html;
    document.getElementById('modalAccompagnatori').classList.remove('hidden');
}
</script>

<script>
function apriModOrg(btn) {
    var d = btn.dataset;
    if (d.tipo === '1g') {
        document.getElementById('modOrg1g_id').value         = d.id;
        document.getElementById('modOrg1gTitle').textContent = 'Modifica: ' + d.dest;
        document.getElementById('modOrg1g_dest').value       = d.dest;
        document.getElementById('modOrg1g_desc').value       = d.desc      || '';
        document.getElementById('modOrg1g_mezzo').value      = d.mezzo     || '';
        document.getElementById('modOrg1g_periodo').value    = d.periodo   || '';
        document.getElementById('modOrg1g_classi').value     = d.classi    || '';
        document.getElementById('modOrg1g_giorno').value     = d.giorno    || '';
        document.getElementById('modOrg1g_costoMezzo').value = d.costoMezzo || '';
        document.getElementById('modOrg1g_costoAtt').value   = d.costoAtt  || '';
        document.getElementById('modOrg1g_costoAP').value    = d.costoAp   || '';
        document.getElementById('modOrg1g_numAlunni').value  = d.numAlunni || '';
        document.getElementById('modalModOrg1g').classList.remove('hidden');
    } else {
        document.getElementById('modOrg5g_id').value         = d.id;
        document.getElementById('modOrg5gTitle').textContent = 'Modifica: ' + d.dest;
        document.getElementById('modOrg5g_dest').value       = d.dest;
        document.getElementById('modOrg5g_desc').value       = d.desc      || '';
        document.getElementById('modOrg5g_mezzo').value      = d.mezzo     || '';
        document.getElementById('modOrg5g_periodo').value    = d.periodo   || '';
        document.getElementById('modOrg5g_classi').value     = d.classi    || '';
        document.getElementById('modOrg5g_gi').value         = d.giornoInizio || '';
        document.getElementById('modOrg5g_gf').value         = d.giornoFine   || '';
        document.getElementById('modOrg5g_costoAP').value    = d.costoAp   || '';
        document.getElementById('modOrg5g_numAlunni').value  = d.numAlunni || '';
        document.getElementById('modalModOrg5g').classList.remove('hidden');
    }
}
function domaniISO() {
    var d = new Date();
    d.setDate(d.getDate() + 1);
    var mese = String(d.getMonth() + 1).padStart(2, '0');
    var giorno = String(d.getDate()).padStart(2, '0');
    return d.getFullYear() + '-' + mese + '-' + giorno;
}
function impostaDateMinimeOrganizza() {
    var min = domaniISO();
    ['org1g_giorno', 'org5g_giornoInizio', 'org5g_giornoFine'].forEach(function(id) {
        var campo = document.getElementById(id);
        if (campo) campo.min = min;
    });
}
function mostraErroreData(id, testo) {
    var el = document.getElementById(id);
    if (el) el.textContent = testo;
}
function validaDateOrg1g() {
    var campo = document.getElementById('org1g_giorno');
    var min = domaniISO();
    mostraErroreData('org1g_giorno_error', '');
    if (campo && campo.value && campo.value < min) {
        mostraErroreData('org1g_giorno_error', 'La data deve essere successiva ad oggi.');
        return false;
    }
    return true;
}
function validaDateOrg5g() {
    var inizio = document.getElementById('org5g_giornoInizio');
    var fine = document.getElementById('org5g_giornoFine');
    var min = domaniISO();
    var ok = true;
    mostraErroreData('org5g_giornoInizio_error', '');
    mostraErroreData('org5g_giornoFine_error', '');
    if (inizio && inizio.value && inizio.value < min) {
        mostraErroreData('org5g_giornoInizio_error', 'La data deve essere successiva ad oggi.');
        ok = false;
    }
    if (fine && fine.value && fine.value < min) {
        mostraErroreData('org5g_giornoFine_error', 'La data deve essere successiva ad oggi.');
        ok = false;
    }
    if (inizio && fine && inizio.value && fine.value && fine.value <= inizio.value) {
        mostraErroreData('org5g_giornoFine_error', 'La data di fine deve essere successiva alla data di inizio.');
        ok = false;
    }
    return ok;
}
function apriOrg(btn) {
    var d = btn.dataset;
    if (d.tipo === '1g') {
        document.getElementById('org1g_id').value           = d.id;
        document.getElementById('org1g_title').textContent  = 'Organizza: ' + d.dest;
        document.getElementById('org1g_mezzo_dest').value   = d.dest;
        document.getElementById('org1g_descrizione').value  = d.desc    || '';
        document.getElementById('org1g_mezzo').value        = d.mezzo   || '';
        document.getElementById('org1g_periodo').value      = d.periodo || '';
        document.getElementById('org1g_classe').value       = d.classi  || '';
        document.getElementById('org1g_costoPersona').value = d.costo   || '';
        document.getElementById('org1g_giorno').value       = '';
        document.getElementById('org1g_costoMezzo').value   = '';
        document.getElementById('org1g_costoGiorno').value  = '';
        document.getElementById('org1g_numAlunni').value    = '';
        impostaDateMinimeOrganizza();
        mostraErroreData('org1g_giorno_error', '');
        document.getElementById('modalOrg1g').classList.remove('hidden');
    } else {
        document.getElementById('org5g_id').value              = d.id;
        document.getElementById('org5g_title').textContent     = 'Organizza: ' + d.dest;
        document.getElementById('org5g_mezzo_dest').value      = d.dest;
        document.getElementById('org5g_descrizione').value     = d.desc    || '';
        document.getElementById('org5g_mezzo').value           = d.mezzo   || '';
        document.getElementById('org5g_periodo').value         = d.periodo || '';
        document.getElementById('org5g_classe').value          = d.classi  || '';
        document.getElementById('org5g_costoAPersona').value   = d.costo   || '';
        document.getElementById('org5g_giornoInizio').value    = '';
        document.getElementById('org5g_giornoFine').value      = '';
        document.getElementById('org5g_numAlunni').value       = '';
        impostaDateMinimeOrganizza();
        mostraErroreData('org5g_giornoInizio_error', '');
        mostraErroreData('org5g_giornoFine_error', '');
        document.getElementById('modalOrg5g').classList.remove('hidden');
    }
}
function apriModifica(id, dest, mezzo, periodo, costo, tipo) {
    document.getElementById('modId').value      = id;
    document.getElementById('modDest').value    = dest;
    document.getElementById('modMezzo').value   = mezzo;
    document.getElementById('modPeriodo').value = periodo;
    document.getElementById('modCosto').value   = costo;
    document.getElementById('modAction').value  = tipo === '1g' ? 'riproponi_1g' : 'riproponi_5g';
    document.getElementById('modTitolo').textContent = 'Modifica: ' + dest;
    document.getElementById('modalModifica').classList.remove('hidden');
}
function chiudiModifica() {
    document.getElementById('modalModifica').classList.add('hidden');
}
function apriElimina(id, dest, tipo) {
    document.getElementById('elimId').value        = id;
    document.getElementById('elimAction').value    = tipo === '1g' ? 'elimina_bocciata_1g' : 'elimina_bocciata_5g';
    document.getElementById('elimDestTxt').textContent = dest;
    document.getElementById('modalElimina').classList.remove('hidden');
}
function chiudiElimina() {
    document.getElementById('modalElimina').classList.add('hidden');
}
window.addEventListener('click', function(e) {
    if (e.target === document.getElementById('modalModifica'))  chiudiModifica();
    if (e.target === document.getElementById('modalElimina'))   chiudiElimina();
    if (e.target === document.getElementById('modalOrg1g'))     document.getElementById('modalOrg1g').classList.add('hidden');
    if (e.target === document.getElementById('modalOrg5g'))     document.getElementById('modalOrg5g').classList.add('hidden');
    if (e.target === document.getElementById('modalModOrg1g'))  document.getElementById('modalModOrg1g').classList.add('hidden');
    if (e.target === document.getElementById('modalModOrg5g'))  document.getElementById('modalModOrg5g').classList.add('hidden');
    if (e.target === document.getElementById('modalAccompagnatori')) document.getElementById('modalAccompagnatori').classList.add('hidden');
});
document.addEventListener('DOMContentLoaded', function() {
    impostaDateMinimeOrganizza();
    var form1g = document.getElementById('formOrg1g');
    var form5g = document.getElementById('formOrg5g');
    var giorno1g = document.getElementById('org1g_giorno');
    var giornoInizio5g = document.getElementById('org5g_giornoInizio');
    var giornoFine5g = document.getElementById('org5g_giornoFine');
    if (form1g) {
        form1g.addEventListener('submit', function(e) {
            if (!validaDateOrg1g()) e.preventDefault();
        });
    }
    if (form5g) {
        form5g.addEventListener('submit', function(e) {
            if (!validaDateOrg5g()) e.preventDefault();
        });
    }
    if (giorno1g) giorno1g.addEventListener('input', validaDateOrg1g);
    if (giornoInizio5g) giornoInizio5g.addEventListener('input', validaDateOrg5g);
    if (giornoFine5g) giornoFine5g.addEventListener('input', validaDateOrg5g);
});
</script>
</body>
</html>
