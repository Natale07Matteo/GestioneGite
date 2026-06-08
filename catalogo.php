<?php
include('nav.php');
include('utils.php');

$messaggio = "";

// funzione per validare le date in modo sicuro
function validaData($str) {
    if (empty($str)) return false;
    $ts = strtotime($str);
    if ($ts === false) return false;
    $y = (int)date('Y', $ts);
    return ($y >= 2000 && $y <= 2100);
}

// nuova proposta gita 1 giorno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'nuova_1g') {
    $idUtente = $_SESSION['id_utente'];
    
    $destinazione = '';
    if (isset($_POST['destinazione'])) {
        $destinazione = trim($_POST['destinazione']);
    }
    
    $descrizione = '';
    if (isset($_POST['descrizione'])) {
        $descrizione = trim($_POST['descrizione']);
    }
    
    $mezzo = '';
    if (isset($_POST['mezzo'])) {
        $mezzo = trim($_POST['mezzo']);
    }
    
    $periodo = '';
    if (isset($_POST['periodo'])) {
        $periodo = trim($_POST['periodo']);
    }
    
    $classi = '';
    if (isset($_POST['classi'])) {
        $classi = trim($_POST['classi']);
    }
    
    $costo = 0;
    if (isset($_POST['costo'])) {
        $costo = (float)$_POST['costo'];
    }

    // validazione input
    if ($destinazione === '' || strlen($destinazione) > 255) {
        $messaggio = "<div class='alert alert-error'>Destinazione obbligatoria (max 255 caratteri).</div>";
    } elseif ($costo < 0) {
        $messaggio = "<div class='alert alert-error'>Il costo non puo essere negativo.</div>";
    } else {
        $destinazione = $conn->real_escape_string($destinazione);
        $descrizione  = $conn->real_escape_string($descrizione);
        $mezzo        = $conn->real_escape_string($mezzo);
        $periodo      = $conn->real_escape_string($periodo);
        $classi       = $conn->real_escape_string($classi);

        if ($conn->query("INSERT INTO gita1g (idUtente, destinazione, descrizione, mezzo, periodo, classi, costoAPersona, idStato) VALUES ($idUtente, '$destinazione', '$descrizione', '$mezzo', '$periodo', '$classi', $costo, 1)")) {
            $messaggio = "<div class='alert alert-success'>Proposta gita 1 giorno salvata come bozza in <a href='mieGite.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Le mie Gite</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-error'>Errore durante il salvataggio: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// nuova proposta gita piu giorni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'nuova_5g') {
    $idUtente = $_SESSION['id_utente'];
    
    $destinazione = '';
    if (isset($_POST['destinazione'])) {
        $destinazione = trim($_POST['destinazione']);
    }
    
    $descrizione = '';
    if (isset($_POST['descrizione'])) {
        $descrizione = trim($_POST['descrizione']);
    }
    
    $mezzo = '';
    if (isset($_POST['mezzo'])) {
        $mezzo = trim($_POST['mezzo']);
    }
    
    $periodo = '';
    if (isset($_POST['periodo'])) {
        $periodo = trim($_POST['periodo']);
    }
    
    $classi = '';
    if (isset($_POST['classi'])) {
        $classi = trim($_POST['classi']);
    }
    
    $costo = 0;
    if (isset($_POST['costo'])) {
        $costo = (float)$_POST['costo'];
    }

    // validazione input
    if ($destinazione === '' || strlen($destinazione) > 255) {
        $messaggio = "<div class='alert alert-error'>Destinazione obbligatoria (max 255 caratteri).</div>";
    } elseif ($costo < 0) {
        $messaggio = "<div class='alert alert-error'>Il costo non puo essere negativo.</div>";
    } else {
        $destinazione = $conn->real_escape_string($destinazione);
        $descrizione  = $conn->real_escape_string($descrizione);
        $mezzo        = $conn->real_escape_string($mezzo);
        $periodo      = $conn->real_escape_string($periodo);
        $classi       = $conn->real_escape_string($classi);

        if ($conn->query("INSERT INTO gite5 (idUtente, destinazione, descrizione, mezzo, periodo, classi, costoAPersona, idStato) VALUES ($idUtente, '$destinazione', '$descrizione', '$mezzo', '$periodo', '$classi', $costo, 1)")) {
            $messaggio = "<div class='alert alert-success'>Proposta gita di piu giorni salvata come bozza in <a href='mieGite.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Le mie Gite</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-error'>Errore durante il salvataggio: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// organizza gita 1 giorno (copia con stato 4)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'organizza_1g') {
    $idGita   = (int)$_POST['id_gita'];
    $idUtente = $_SESSION['id_utente'];
    
    $periodo = null;
    if (isset($_POST['org_periodo']) && $_POST['org_periodo'] !== '') {
        $periodo = $_POST['org_periodo'];
    }
    
    $mezzo = '';
    if (isset($_POST['org_mezzo']) && $_POST['org_mezzo'] !== '') {
        $mezzo = $_POST['org_mezzo'];
    }
    
    $descrizione = '';
    if (isset($_POST['org_descrizione']) && $_POST['org_descrizione'] !== '') {
        $descrizione = $_POST['org_descrizione'];
    }
    
    $classi = '';
    if (isset($_POST['org_classe']) && $_POST['org_classe'] !== '') {
        $classi = $_POST['org_classe'];
    }
    
    $giorno = null;
    if (isset($_POST['org_giorno']) && $_POST['org_giorno'] !== '') {
        $giorno = $_POST['org_giorno'];
    }
    
    $costoMezzo = null;
    if (isset($_POST['org_costoMezzo']) && $_POST['org_costoMezzo'] !== '') {
        $costoMezzo = (float)str_replace(',', '.', $_POST['org_costoMezzo']);
    }
    
    $costoGiorno = null;
    if (isset($_POST['org_costoGiorno']) && $_POST['org_costoGiorno'] !== '') {
        $costoGiorno = (float)str_replace(',', '.', $_POST['org_costoGiorno']);
    }
    
    $numAlunni = null;
    if (isset($_POST['org_numAlunni']) && $_POST['org_numAlunni'] !== '') {
        $numAlunni = (int)$_POST['org_numAlunni'];
    }

    $valido = true;
    if ($giorno) {
        if (strtotime($giorno) === false || (int)date('Y', strtotime($giorno)) < 2024 || (int)date('Y', strtotime($giorno)) > 2030) {
            $valido = false;
        }
    }
    if ($giorno) {
        if (strtotime($giorno) <= strtotime(date('Y-m-d'))) {
            $valido = false;
        }
    }

    if (!$valido) {
        $messaggio = "<div class='alert alert-error'>Errore di validazione: la data della gita deve essere successiva ad oggi.</div>";
    } else {
        // Leggi la riga originale
        $orig = $conn->query("SELECT * FROM gita1g WHERE idGita = $idGita")->fetch_assoc();
        if ($orig) {
            $dest_s = $conn->real_escape_string($orig['destinazione']);
            
            // Check if already organizing
            $check = $conn->query("SELECT idGita FROM gita1g WHERE idUtente = $idUtente AND destinazione = '$dest_s' AND idStato = 4");
            if ($check && $check->num_rows > 0) {
                $messaggio = "<div class='alert alert-warning'>Stai già organizzando questa gita.</div>";
            } else {
                $desc_s = $conn->real_escape_string($descrizione);
                
                $mezzoTmp = '';
                if (!empty($mezzo)) {
                    $mezzoTmp = $mezzo;
                } else if (isset($orig['mezzo'])) {
                    $mezzoTmp = $orig['mezzo'];
                }
                $mezzoFin_s = $conn->real_escape_string($mezzoTmp);
                
                $perTmp = '';
                if (!empty($periodo)) {
                    $perTmp = $periodo;
                } else if (isset($orig['periodo'])) {
                    $perTmp = $orig['periodo'];
                }
                $perFin_s = $conn->real_escape_string($perTmp);
                
                $classi_s = $conn->real_escape_string($classi);
                $costoA_s = (float)$orig['costoAPersona'];
                
                $giorno_s = "NULL";
                if ($giorno) {
                    $giorno_s = "'" . $conn->real_escape_string($giorno) . "'";
                }
                
                $costoMezzo_s = "NULL";
                if ($costoMezzo !== null) {
                    $costoMezzo_s = (float)$costoMezzo;
                }
                
                $costoGiorno_s = "NULL";
                if ($costoGiorno !== null) {
                    $costoGiorno_s = (float)$costoGiorno;
                }
                
                $numAlunni_s = "NULL";
                if ($numAlunni !== null) {
                    $numAlunni_s = (int)$numAlunni;
                }

                $sql = "INSERT INTO gita1g (idUtente, destinazione, descrizione, mezzo, periodo, classi, giorno, costoMezzo, costoAttivita, costoAPersona, numAlunni, idStato)
                        VALUES ($idUtente, '$dest_s', '$desc_s', '$mezzoFin_s', '$perFin_s', '$classi_s', $giorno_s, $costoMezzo_s, $costoGiorno_s, $costoA_s, $numAlunni_s, 4)";
                if ($conn->query($sql)) {
                    $messaggio = "organizza_ok";
                } else {
                    $messaggio = "<div class='alert alert-error'>Errore: " . htmlspecialchars($conn->error) . "</div>";
                }
            } // end of check if already organizing
        }
    }
}

// organizza gita piu giorni (copia con stato 4)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'organizza_5g') {
    $idGita   = (int)$_POST['id_gita'];
    $idUtente = $_SESSION['id_utente'];
    
    $periodo = null;
    if (isset($_POST['org_periodo']) && $_POST['org_periodo'] !== '') {
        $periodo = $_POST['org_periodo'];
    }
    
    $mezzo = '';
    if (isset($_POST['org_mezzo']) && $_POST['org_mezzo'] !== '') {
        $mezzo = $_POST['org_mezzo'];
    }
    
    $descrizione = '';
    if (isset($_POST['org_descrizione']) && $_POST['org_descrizione'] !== '') {
        $descrizione = $_POST['org_descrizione'];
    }
    
    $classi = '';
    if (isset($_POST['org_classe']) && $_POST['org_classe'] !== '') {
        $classi = $_POST['org_classe'];
    }
    
    $giornoInizio = null;
    if (isset($_POST['org_giornoInizio']) && $_POST['org_giornoInizio'] !== '') {
        $giornoInizio = $_POST['org_giornoInizio'];
    }
    
    $giornoFine = null;
    if (isset($_POST['org_giornoFine']) && $_POST['org_giornoFine'] !== '') {
        $giornoFine = $_POST['org_giornoFine'];
    }
    
    $costoAPersona = null;
    if (isset($_POST['org_costoAPersona']) && $_POST['org_costoAPersona'] !== '') {
        $costoAPersona = (float)str_replace(',', '.', $_POST['org_costoAPersona']);
    }
    
    $numAlunni = null;
    if (isset($_POST['org_numAlunni']) && $_POST['org_numAlunni'] !== '') {
        $numAlunni = (int)$_POST['org_numAlunni'];
    }

    $valido = true;
    if ($giornoInizio) {
        if (strtotime($giornoInizio) === false || (int)date('Y', strtotime($giornoInizio)) < 2024 || (int)date('Y', strtotime($giornoInizio)) > 2030) {
            $valido = false;
        }
    }
    if ($giornoFine) {
        if (strtotime($giornoFine) === false || (int)date('Y', strtotime($giornoFine)) < 2024 || (int)date('Y', strtotime($giornoFine)) > 2030) {
            $valido = false;
        }
    }
    if ($giornoInizio) {
        if (strtotime($giornoInizio) <= strtotime(date('Y-m-d'))) {
            $valido = false;
        }
    }
    if ($giornoInizio && $giornoFine) {
        if (strtotime($giornoInizio) >= strtotime($giornoFine)) {
            $valido = false;
        }
    }

    if (!$valido) {
        $messaggio = "<div class='alert alert-error'>Errore di validazione: la data di inizio deve essere successiva ad oggi e precedente alla data di fine.</div>";
    } else {
        $orig = $conn->query("SELECT * FROM gite5 WHERE idGita = $idGita")->fetch_assoc();
        if ($orig) {
            $dest_s = $conn->real_escape_string($orig['destinazione']);
            
            // Check if already organizing
            $check = $conn->query("SELECT idGita FROM gite5 WHERE idUtente = $idUtente AND destinazione = '$dest_s' AND idStato = 4");
            if ($check && $check->num_rows > 0) {
                $messaggio = "<div class='alert alert-warning'>Stai già organizzando questa gita.</div>";
            } else {
                $desc_s = $conn->real_escape_string($descrizione);
                
                $mezzoTmp = '';
                if (!empty($mezzo)) {
                    $mezzoTmp = $mezzo;
                } else if (isset($orig['mezzo'])) {
                    $mezzoTmp = $orig['mezzo'];
                }
                $mezzoFin_s = $conn->real_escape_string($mezzoTmp);
                
                $perTmp = '';
                if (!empty($periodo)) {
                    $perTmp = $periodo;
                } else if (isset($orig['periodo'])) {
                    $perTmp = $orig['periodo'];
                }
                $perFin_s = $conn->real_escape_string($perTmp);
                
                $classi_s = $conn->real_escape_string($classi);
                
                $gi_s = "NULL";
                if ($giornoInizio) {
                    $gi_s = "'" . $conn->real_escape_string($giornoInizio) . "'";
                }
                
                $gf_s = "NULL";
                if ($giornoFine) {
                    $gf_s = "'" . $conn->real_escape_string($giornoFine) . "'";
                }
                
                $costoOrig = 0;
                if (isset($orig['costoAPersona'])) {
                    $costoOrig = (float)$orig['costoAPersona'];
                }
                
                $costoFin = $costoOrig;
                if ($costoAPersona !== null) {
                    $costoFin = (float)$costoAPersona;
                }
                
                $numAlunni_s = "NULL";
                if ($numAlunni !== null) {
                    $numAlunni_s = (int)$numAlunni;
                }

                $sql = "INSERT INTO gite5 (idUtente, destinazione, descrizione, mezzo, periodo, classi, giornoInizio, giornoFine, costoAPersona, numAlunni, idStato)
                        VALUES ($idUtente, '$dest_s', '$desc_s', '$mezzoFin_s', '$perFin_s', '$classi_s', $gi_s, $gf_s, $costoFin, $numAlunni_s, 4)";
                if ($conn->query($sql)) {
                    $messaggio = "organizza_ok";
                } else {
                    $messaggio = "<div class='alert alert-error'>Errore: " . htmlspecialchars($conn->error) . "</div>";
                }
            } // end of check if already organizing
        }
    }
}

// modifica gita 1 giorno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_1g') {
    if ($_SESSION['ruolo'] == 2) {
        $idGita = (int)$_POST['id_gita'];
        
        $destTmp = '';
        if (isset($_POST['mod_destinazione'])) { $destTmp = $_POST['mod_destinazione']; }
        $destinazione = $conn->real_escape_string($destTmp);
        
        $descTmp = '';
        if (isset($_POST['mod_descrizione'])) { $descTmp = $_POST['mod_descrizione']; }
        $descrizione = $conn->real_escape_string($descTmp);
        
        $mezzoTmp = '';
        if (isset($_POST['mod_mezzo'])) { $mezzoTmp = $_POST['mod_mezzo']; }
        $mezzo = $conn->real_escape_string($mezzoTmp);
        
        $perTmp = '';
        if (isset($_POST['mod_periodo'])) { $perTmp = $_POST['mod_periodo']; }
        $periodo = $conn->real_escape_string($perTmp);
        
        $classiTmp = '';
        if (isset($_POST['mod_classi'])) { $classiTmp = $_POST['mod_classi']; }
        $classi = $conn->real_escape_string($classiTmp);
        
        $costo = 0;
        if (isset($_POST['mod_costo'])) {
            $costo = (float)$_POST['mod_costo'];
        }
        
        if ($conn->query("UPDATE gita1g SET destinazione='$destinazione', descrizione='$descrizione', mezzo='$mezzo', periodo='$periodo', classi='$classi', costoAPersona=$costo WHERE idGita=$idGita")) {
            $messaggio = "<div class='alert alert-success'>Gita 1 giorno modificata. Visualizza in <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Proposte</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-error'>Errore modifica: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// elimina gita 1 giorno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina_1g') {
    if ($_SESSION['ruolo'] == 2) {
        $idGita = (int)$_POST['id_gita'];
        if ($conn->query("DELETE FROM gita1g WHERE idGita=$idGita")) {
            $messaggio = "<div class='alert alert-success'>Gita 1 giorno eliminata dal <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Catalogo Proposte</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-error'>Errore eliminazione: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// modifica gita piu giorni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_5g') {
    if ($_SESSION['ruolo'] == 2) {
        $idGita = (int)$_POST['id_gita'];
        
        $destTmp = '';
        if (isset($_POST['mod_destinazione'])) { $destTmp = $_POST['mod_destinazione']; }
        $destinazione = $conn->real_escape_string($destTmp);
        
        $descTmp = '';
        if (isset($_POST['mod_descrizione'])) { $descTmp = $_POST['mod_descrizione']; }
        $descrizione = $conn->real_escape_string($descTmp);
        
        $mezzoTmp = '';
        if (isset($_POST['mod_mezzo'])) { $mezzoTmp = $_POST['mod_mezzo']; }
        $mezzo = $conn->real_escape_string($mezzoTmp);
        
        $perTmp = '';
        if (isset($_POST['mod_periodo'])) { $perTmp = $_POST['mod_periodo']; }
        $periodo = $conn->real_escape_string($perTmp);
        
        $classiTmp = '';
        if (isset($_POST['mod_classi'])) { $classiTmp = $_POST['mod_classi']; }
        $classi = $conn->real_escape_string($classiTmp);
        
        $costo = 0;
        if (isset($_POST['mod_costo'])) {
            $costo = (float)$_POST['mod_costo'];
        }
        
        if ($conn->query("UPDATE gite5 SET destinazione='$destinazione', descrizione='$descrizione', mezzo='$mezzo', periodo='$periodo', classi='$classi', costoAPersona=$costo WHERE idGita=$idGita")) {
            $messaggio = "<div class='alert alert-success'>Gita di più giorni modificata. Visualizza in <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Proposte</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-error'>Errore modifica: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// elimina gita piu giorni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina_5g') {
    if ($_SESSION['ruolo'] == 2) {
        $idGita = (int)$_POST['id_gita'];
        if ($conn->query("DELETE FROM gite5 WHERE idGita=$idGita")) {
            $messaggio = "<div class='alert alert-success'>Gita di più giorni eliminata dal <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Catalogo Proposte</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-error'>Errore eliminazione: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// query gite 1 giorno (stato 2 = approvate)
$gite1g = $conn->query("
    SELECT g.idGita, g.destinazione, g.descrizione, g.mezzo, g.periodo, g.costoAPersona, g.classi,
           u.Nome, u.Cognome
    FROM gita1g g
    JOIN utente u ON g.idUtente = u.IDUtente
    WHERE g.idStato = 2
    ORDER BY g.idGita DESC
");

// query gite piu giorni (stato 2 = approvate)
$gite5g = $conn->query("
    SELECT g.idGita, g.destinazione, g.descrizione, g.mezzo, g.periodo, g.costoAPersona, g.classi,
           u.Nome, u.Cognome
    FROM gite5 g
    JOIN utente u ON g.idUtente = u.IDUtente
    WHERE g.idStato = 2
    ORDER BY g.idGita DESC
");

$tot1g = 0;
if ($gite1g) {
    $tot1g = $gite1g->num_rows;
}

$tot5g = 0;
if ($gite5g) {
    $tot5g = $gite5g->num_rows;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposte Gite</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css?v=<?php echo time(); ?>">
    <script src="vetrina.js" defer></script>
    <script>
    function selezionaMezzo(selectId, valore) {
        var sel = document.getElementById(selectId);
        if (!sel || !valore) return;
        var valLower = valore.toLowerCase().trim();
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value.toLowerCase().trim() === valLower) {
                sel.selectedIndex = i;
                return;
            }
        }
        // fallback: cerca corrispondenza parziale (es. 'autobus' contiene 'bus')
        for (var j = 0; j < sel.options.length; j++) {
            var optLower = sel.options[j].value.toLowerCase().trim();
            if (optLower && (valLower.indexOf(optLower) !== -1 || optLower.indexOf(valLower) !== -1)) {
                sel.selectedIndex = j;
                return;
            }
        }
        sel.selectedIndex = 0;
    }
    function apriOrg1g(btn) {
        var d = btn.dataset;
        document.getElementById('org1g_id').value             = d.id;
        document.getElementById('org1g_title').textContent    = 'Organizza: ' + d.dest;
        document.getElementById('org1g_mezzo_dest').value     = d.dest;
        document.getElementById('org1g_descrizione').value    = d.descrizione || '';
        selezionaMezzo('org1g_mezzo', d.mezzo || '');
        document.getElementById('org1g_periodo').value        = d.periodo  || '';
        document.getElementById('org1g_classe').value         = d.classi   || '';
        document.getElementById('org1g_costoPersona').value   = d.costo    || '';
        document.getElementById('org1g_giorno').value         = '';
        document.getElementById('org1g_costoMezzo').value     = '';
        document.getElementById('org1g_costoGiorno').value    = '';
        document.getElementById('org1g_numAlunni').value      = '';
        impostaDateMinimeOrganizza();
        mostraErroreData('org1g_giorno_error', '');
        document.getElementById('modalOrg1g').classList.remove('hidden');
    }
    function apriOrg5g(btn) {
        var d = btn.dataset;
        document.getElementById('org5g_id').value             = d.id;
        document.getElementById('org5g_title').textContent    = 'Organizza: ' + d.dest;
        document.getElementById('org5g_mezzo_dest').value     = d.dest;
        document.getElementById('org5g_descrizione').value    = d.descrizione || '';
        selezionaMezzo('org5g_mezzo', d.mezzo || '');
        document.getElementById('org5g_periodo').value        = d.periodo  || '';
        document.getElementById('org5g_classe').value         = d.classi   || '';
        document.getElementById('org5g_costoAPersona').value  = d.costo    || '';
        document.getElementById('org5g_giornoInizio').value   = '';
        document.getElementById('org5g_giornoFine').value     = '';
        document.getElementById('org5g_numAlunni').value      = '';
        impostaDateMinimeOrganizza();
        mostraErroreData('org5g_giornoInizio_error', '');
        mostraErroreData('org5g_giornoFine_error', '');
        document.getElementById('modalOrg5g').classList.remove('hidden');
    }
    function apriModifica1g(btn) {
        var d = btn.dataset;
        document.getElementById('mod1g_id').value           = d.id;
        document.getElementById('mod1g_destinazione').value = d.dest;
        document.getElementById('mod1g_descrizione').value  = d.descrizione || '';
        selezionaMezzo('mod1g_mezzo', d.mezzo || '');
        document.getElementById('mod1g_periodo').value      = d.periodo || '';
        document.getElementById('mod1g_classi').value       = d.classi  || '';
        document.getElementById('mod1g_costo').value        = d.costo   || '';
        document.getElementById('modalMod1g').classList.remove('hidden');
    }
    function apriModifica5g(btn) {
        var d = btn.dataset;
        document.getElementById('mod5g_id').value           = d.id;
        document.getElementById('mod5g_destinazione').value = d.dest;
        document.getElementById('mod5g_descrizione').value  = d.descrizione || '';
        document.getElementById('mod5g_mezzo').value        = d.mezzo   || '';
        document.getElementById('mod5g_periodo').value      = d.periodo || '';
        document.getElementById('mod5g_classi').value       = d.classi  || '';
        document.getElementById('mod5g_costo').value        = d.costo   || '';
        document.getElementById('modalMod5g').classList.remove('hidden');
    }
    function apriElimina(id, dest, tabella) {
        document.getElementById('elimId').value      = id;
        document.getElementById('elimTabella').value = tabella;
        document.getElementById('elimDest').textContent = dest;
        document.getElementById('modalElimina').classList.remove('hidden');
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
</head>
<body>
<div class="container">
<main class="content bozze-padding">


<?php
// mostra il messaggio di errore o successo
if ($messaggio && $messaggio !== 'organizza_ok') {
    echo $messaggio;
}
// se l'organizzazione e andata bene reindirizza alle mie gite
if ($messaggio === 'organizza_ok') {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.location.href = "mieGite.php?organizzata=1";
    });
    </script>';
}
?>
<div style="display: flex; justify-content: flex-start; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem; margin-bottom: 1.5rem;">
    <div class="search-bar-wrapper" style="margin-bottom: 0; max-width: 380px; width: 100%;">
        <input type="text" id="cercaProposte" onkeyup="cercaInTabelle('cercaProposte', 'table')" placeholder="Cerca destinazione, mezzo, periodo, docente...">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
            <path d="M784-120 533-371q-30 24-74 37.5T367-320q-101 0-171-70t-70-171q0-101 70-171t171-70q101 0 171 70t70 171q0 48-13.5 92T533-533l251 251-50 50ZM367-400q67 0 113.5-46.5T527-560q0-67-46.5-113.5T367-720q-67 0-113.5 46.5T207-560q0 67 46.5 113.5T367-400Z"/>
        </svg>
    </div>
    <a href="#gite-quinte" class="button outline-quinte" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; border-radius: 99px; padding: 0.6rem 1.2rem; font-size: 0.88rem; transition: all 0.2s ease; height: auto; min-width: 0; line-height: 1;">
        <span>vai a gite per le quinte</span>
        <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor">
            <path d="M440-800v487L216-537l-56 57 320 320 320-320-56-57-224 224v-487h-80Z"/>
        </svg>
    </a>
</div>

<!-- sezione gite 1 giorno -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <h3 style="color:var(--blue-700);margin:0;">Proposte gite di un giorno</h3>
    <button class="button" onclick="document.getElementById('modal1g').classList.remove('hidden')">+ Nuova Proposta</button>
</div>

<div class="table-section" style="margin-bottom:3rem;"><div class="table-container">
<table>
<thead><tr>
    <th>Destinazione</th>
    <th>Mezzo</th>
    <th>Periodo</th>
    <th>Costo a Persona</th>
    <th>Proposta da</th>
    <th>Organizza</th>
    <?php if ($_SESSION['ruolo'] == 2) { echo '<th>Azioni</th>'; } ?>
</tr></thead>
<tbody>
<?php
if ($gite1g && $gite1g->num_rows > 0) {
    while ($r = $gite1g->fetch_assoc()) {
        $dest = htmlspecialchars($r['destinazione']);
        
        $mezzo = '—';
        if (isset($r['mezzo'])) { $mezzo = htmlspecialchars($r['mezzo']); }
        
        $per = '—';
        if (isset($r['periodo'])) { $per = htmlspecialchars($r['periodo']); }
        
        $costo = number_format($r['costoAPersona'], 2, ',', '.');
        $autore = htmlspecialchars($r['Nome'] . ' ' . $r['Cognome']);
        $id = (int)$r['idGita'];
        $destJs = htmlspecialchars($r['destinazione']);
        
        $descJs = '';
        if (isset($r['descrizione'])) { $descJs = htmlspecialchars($r['descrizione']); }
        
        $mezzoJs = '';
        if (isset($r['mezzo'])) { $mezzoJs = htmlspecialchars($r['mezzo']); }
        
        $perJs = '';
        if (isset($r['periodo'])) { $perJs = htmlspecialchars($r['periodo']); }
        
        $classiJs = '';
        if (isset($r['classi'])) { $classiJs = htmlspecialchars($r['classi']); }
        
        $costoJs = (float)$r['costoAPersona'];
        $azioniCol = '';
        if ($_SESSION['ruolo'] == 2) {
            $azioniCol = "<td><div class='azioni-cell'>
                <button type='button' class='button xs'
                    data-id='$id' data-dest='$destJs' data-descrizione='$descJs'
                    data-mezzo='$mezzoJs' data-periodo='$perJs'
                    data-classi='$classiJs' data-costo='$costoJs'
                    onclick=\"apriModifica1g(this)\">Modifica</button>
                <button type='button' class='button cancel xs'
                    onclick=\"apriElimina($id,'$destJs','elimina_1g')\">Elimina</button>
            </div></td>";
        }
        echo "<tr>
            <td>$dest</td>
            <td>$mezzo</td>
            <td>$per</td>
            <td>&euro; $costo</td>
            <td>$autore</td>
            <td><button type='button' class='button xs'
                data-id='$id'
                data-dest='$destJs'
                data-descrizione='$descJs'
                data-mezzo='$mezzoJs'
                data-periodo='$perJs'
                data-classi='$classiJs'
                data-costo='$costoJs'
                onclick=\"apriOrg1g(this)\">Organizza</button></td>
            $azioniCol
        </tr>";
    }
} else {
    echo "<tr><td colspan='7' style='text-align:center;'>Nessuna gita di 1 giorno approvata al momento.</td></tr>";
}
?>
</tbody>
</table>
</div></div>

<!-- sezione gite piu giorni -->
<div id="gite-quinte" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; scroll-margin-top: 5rem;">
    <h3 style="color:var(--blue-700);margin:0;">Proposte gite per le quinte</h3>
    <button class="button" onclick="document.getElementById('modal5g').classList.remove('hidden')">+ Nuova Proposta</button>
</div>

<div class="table-section"><div class="table-container table-quinte">
<table>
<thead><tr>
    <th>Destinazione</th>
    <th>Mezzo</th>
    <th>Periodo</th>
    <th>Costo a Persona</th>
    <th>Proposta da</th>
    <th>Organizza</th>
    <?php if ($_SESSION['ruolo'] == 2) { echo '<th>Azioni</th>'; } ?>
</tr></thead>
<tbody>
<?php
if ($gite5g && $gite5g->num_rows > 0) {
    while ($r = $gite5g->fetch_assoc()) {
        $dest = htmlspecialchars($r['destinazione']);
        
        $mezzo = '—';
        if (isset($r['mezzo'])) { $mezzo = htmlspecialchars($r['mezzo']); }
        
        $per = '—';
        if (isset($r['periodo'])) { $per = htmlspecialchars($r['periodo']); }
        
        $costo = number_format($r['costoAPersona'], 2, ',', '.');
        $autore = htmlspecialchars($r['Nome'] . ' ' . $r['Cognome']);
        $id = (int)$r['idGita'];
        $destJs = htmlspecialchars($r['destinazione']);
        
        $descJs = '';
        if (isset($r['descrizione'])) { $descJs = htmlspecialchars($r['descrizione']); }
        
        $mezzoJs = '';
        if (isset($r['mezzo'])) { $mezzoJs = htmlspecialchars($r['mezzo']); }
        
        $perJs = '';
        if (isset($r['periodo'])) { $perJs = htmlspecialchars($r['periodo']); }
        
        $classiJs = '';
        if (isset($r['classi'])) { $classiJs = htmlspecialchars($r['classi']); }
        
        $costoJs = (float)$r['costoAPersona'];
        $azioniCol = '';
        if ($_SESSION['ruolo'] == 2) {
            $azioniCol = "<td><div class='azioni-cell'>
                <button type='button' class='button xs'
                    data-id='$id' data-dest='$destJs' data-descrizione='$descJs'
                    data-mezzo='$mezzoJs' data-periodo='$perJs'
                    data-classi='$classiJs' data-costo='$costoJs'
                    onclick=\"apriModifica5g(this)\">Modifica</button>
                <button type='button' class='button cancel xs'
                    onclick=\"apriElimina($id,'$destJs','elimina_5g')\">Elimina</button>
            </div></td>";
        }
        echo "<tr>
            <td>$dest</td>
            <td>$mezzo</td>
            <td>$per</td>
            <td>&euro; $costo</td>
            <td>$autore</td>
            <td><button type='button' class='button xs'
                data-id='$id'
                data-dest='$destJs'
                data-descrizione='$descJs'
                data-mezzo='$mezzoJs'
                data-periodo='$perJs'
                data-classi='$classiJs'
                data-costo='$costoJs'
                onclick=\"apriOrg5g(this)\">Organizza</button></td>
            $azioniCol
        </tr>";
    }
} else {
    echo "<tr><td colspan='7' style='text-align:center;'>Nessuna gita di più giorni approvata al momento.</td></tr>";
}
?>
</tbody>
</table>
</div></div>

</main>


<!-- modal: nuova proposta gita 1 giorno -->
<div class="modal-overlay hidden" id="modal1g">
<div class="modal">
<div class="modal-header">
    <h3>Nuova Proposta — Gita 1 Giorno</h3>
    <button class="close-btn" onclick="document.getElementById('modal1g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<p style="font-size:0.9rem; color:var(--hex-red); margin-bottom:1rem; text-align:center;">I campi contrassegnati con l'asterisco (*) devono essere compilati obbligatoriamente.</p>
<form method="POST" action="catalogo.php" id="form1g">
    <input type="hidden" name="action" value="nuova_1g">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="destinazione" class="form-control" required placeholder="es. Roma">
        </div>
        <div class="form-group">
            <label>Descrizione *</label>
            <input type="text" name="descrizione" class="form-control" required placeholder="Breve descrizione della gita">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto *</label>
            <select name="mezzo" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="Autobus">Autobus</option>
                <option value="Bus">Bus</option>
                <option value="Treno">Treno</option>
                <option value="Ci incontriamo direttamente lì">Ci incontriamo direttamente lì</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo *</label>
            <input type="text" name="periodo" class="form-control" required placeholder="es. Marzo 2026">
        </div>
        <div class="form-group">
            <label>Classi consigliate *</label>
            <input type="text" name="classi" class="form-control" required placeholder="es. 3A, 3B">
        </div>
        <div class="form-group">
            <label>Costo a persona (&euro;) *</label>
            <input type="number" name="costo" class="form-control" step="0.50" min="0" required placeholder="es. 45.00">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modal1g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="form1g" class="button">Salva Proposta</button>
</div>
</div>
</div>


<!-- modal: nuova proposta gita piu giorni -->
<div class="modal-overlay hidden" id="modal5g">
<div class="modal">
<div class="modal-header">
    <h3>Nuova Proposta — Gita più Giorni</h3>
    <button class="close-btn" onclick="document.getElementById('modal5g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<p style="font-size:0.9rem; color:var(--hex-red); margin-bottom:1rem; text-align:center;">I campi contrassegnati con l'asterisco (*) devono essere compilati obbligatoriamente.</p>
<form method="POST" action="catalogo.php" id="form5g">
    <input type="hidden" name="action" value="nuova_5g">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="destinazione" class="form-control" required placeholder="es. Parigi">
        </div>
        <div class="form-group">
            <label>Descrizione *</label>
            <input type="text" name="descrizione" class="form-control" required placeholder="Breve descrizione della gita">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto *</label>
            <select name="mezzo" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="Bus GT">Bus GT</option>
                <option value="Treno">Treno</option>
                <option value="Aereo">Aereo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo *</label>
            <input type="text" name="periodo" class="form-control" required placeholder="es. Maggio 2026">
        </div>
        <div class="form-group">
            <label>Classe/i *</label>
            <input type="text" name="classi" class="form-control" required placeholder="es. 4A, 4B">
        </div>
        <div class="form-group">
            <label>Costo a persona (&euro;) *</label>
            <input type="number" name="costo" class="form-control" step="0.50" min="0" required placeholder="es. 350.00">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modal5g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="form5g" class="button">Salva Proposta</button>
</div>
</div>
</div>


<!-- modal: organizza gita 1 giorno -->
<div class="modal-overlay hidden" id="modalOrg1g">
<div class="modal">
<div class="modal-header">
    <h3 id="org1g_title">Organizza Gita 1 Giorno</h3>
    <button class="close-btn" onclick="document.getElementById('modalOrg1g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<p style="font-size:0.9rem; color:var(--hex-red); margin-bottom:1rem; text-align:center;">I campi contrassegnati con l'asterisco (*) devono essere compilati obbligatoriamente.</p>
<form method="POST" action="catalogo.php" id="formOrg1g">
    <input type="hidden" name="action"   value="organizza_1g">
    <input type="hidden" name="id_gita"  id="org1g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione</label>
            <input type="text" name="org_mezzo_dest" id="org1g_mezzo_dest" class="form-control" readonly style="background:#f3f4f6;">
        </div>
        <div class="form-group">
            <label>Descrizione *</label>
            <input type="text" name="org_descrizione" id="org1g_descrizione" class="form-control" placeholder="Breve descrizione" required>
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto *</label>
            <select name="org_mezzo" id="org1g_mezzo" class="form-control" required>
                <option value="">— Seleziona —</option>
                <option value="Autobus">Autobus</option>
                <option value="Bus">Bus</option>
                <option value="Treno">Treno</option>
                <option value="Ci incontriamo direttamente lì">Ci incontriamo direttamente lì</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo *</label>
            <input type="text" name="org_periodo" id="org1g_periodo" class="form-control" placeholder="es. Aprile 2026" required>
        </div>
        <div class="form-group">
            <label>Classe/i *</label>
            <input type="text" name="org_classe" id="org1g_classe" class="form-control" placeholder="es. 3A" required>
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;) *</label>
            <input type="number" name="org_costoAPersona" id="org1g_costoPersona" class="form-control" step="0.50" min="0" placeholder="es. 45.00" required>
        </div>
        <div class="form-group">
            <label>Giorno *</label>
            <input type="date" name="org_giorno" id="org1g_giorno" class="form-control" max="2030-12-31" required>
            <small id="org1g_giorno_error" style="color:var(--hex-red);display:block;margin-top:0.25rem;"></small>
        </div>
        <div class="form-group">
            <label>Costo Mezzo (&euro;) *</label>
            <input type="number" name="org_costoMezzo" id="org1g_costoMezzo" class="form-control" step="0.50" min="0" placeholder="es. 200.00" required>
        </div>
        <div class="form-group">
            <label>Costo Attività/Giorno (&euro;) *</label>
            <input type="number" name="org_costoGiorno" id="org1g_costoGiorno" class="form-control" step="0.50" min="0" placeholder="es. 15.00" required>
        </div>
        <div class="form-group">
            <label>Num. Alunni *</label>
            <input type="number" name="org_numAlunni" id="org1g_numAlunni" class="form-control" min="0" placeholder="es. 25" required>
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalOrg1g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formOrg1g" class="button">Metti in Organizzazione</button>
</div>
</div>
</div>


<!-- modal: organizza gita piu giorni -->
<div class="modal-overlay hidden" id="modalOrg5g">
<div class="modal">
<div class="modal-header">
    <h3 id="org5g_title">Organizza Gita più Giorni</h3>
    <button class="close-btn" onclick="document.getElementById('modalOrg5g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<p style="font-size:0.9rem; color:var(--hex-red); margin-bottom:1rem; text-align:center;">I campi contrassegnati con l'asterisco (*) devono essere compilati obbligatoriamente.</p>
<form method="POST" action="catalogo.php" id="formOrg5g">
    <input type="hidden" name="action"   value="organizza_5g">
    <input type="hidden" name="id_gita"  id="org5g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione</label>
            <input type="text" id="org5g_mezzo_dest" class="form-control" readonly style="background:#f3f4f6;">
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
            <input type="text" name="org_periodo" id="org5g_periodo" class="form-control" placeholder="es. Maggio 2026" required>
        </div>
        <div class="form-group">
            <label>Classe/i *</label>
            <input type="text" name="org_classe" id="org5g_classe" class="form-control" placeholder="es. 4A" required>
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;) *</label>
            <input type="number" name="org_costoAPersona" id="org5g_costoAPersona" class="form-control" step="0.50" min="0" placeholder="es. 350.00" required>
        </div>
        <div class="form-group">
            <label>Giorno Inizio *</label>
            <input type="date" name="org_giornoInizio" id="org5g_giornoInizio" class="form-control" max="2030-12-31" required>
            <small id="org5g_giornoInizio_error" style="color:var(--hex-red);display:block;margin-top:0.25rem;"></small>
        </div>
        <div class="form-group">
            <label>Giorno Fine *</label>
            <input type="date" name="org_giornoFine" id="org5g_giornoFine" class="form-control" max="2030-12-31" required>
            <small id="org5g_giornoFine_error" style="color:var(--hex-red);display:block;margin-top:0.25rem;"></small>
        </div>
        <div class="form-group">
            <label>Num. Alunni *</label>
            <input type="number" name="org_numAlunni" id="org5g_numAlunni" class="form-control" min="0" placeholder="es. 50" required>
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalOrg5g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formOrg5g" class="button">Metti in Organizzazione</button>
</div>
</div>
</div>

<!-- modal: modifica gita 1 giorno (solo commissione) -->
<div class="modal-overlay hidden" id="modalMod1g">
<div class="modal">
<div class="modal-header">
    <h3>Modifica Gita 1 Giorno</h3>
    <button class="close-btn" onclick="document.getElementById('modalMod1g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<form method="POST" action="catalogo.php" id="formMod1g">
    <input type="hidden" name="action"  value="modifica_1g">
    <input type="hidden" name="id_gita" id="mod1g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="mod_destinazione" id="mod1g_destinazione" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="mod_descrizione" id="mod1g_descrizione" class="form-control" placeholder="Breve descrizione">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <select name="mod_mezzo" id="mod1g_mezzo" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Autobus">Autobus</option>
                <option value="Bus">Bus</option>
                <option value="Treno">Treno</option>
                <option value="Ci incontriamo direttamente lì">Ci incontriamo direttamente lì</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="mod_periodo" id="mod1g_periodo" class="form-control">
        </div>
        <div class="form-group">
            <label>Classe/i</label>
            <input type="text" name="mod_classi" id="mod1g_classi" class="form-control" placeholder="es. 3A">
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;)</label>
            <input type="number" name="mod_costo" id="mod1g_costo" class="form-control" step="0.50" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalMod1g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formMod1g" class="button">Salva Modifiche</button>
</div>
</div>
</div>


<!-- modal: modifica gita piu giorni -->
<div class="modal-overlay hidden" id="modalMod5g">
<div class="modal">
<div class="modal-header">
    <h3>Modifica Gita più Giorni</h3>
    <button class="close-btn" onclick="document.getElementById('modalMod5g').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body">
<form method="POST" action="catalogo.php" id="formMod5g">
    <input type="hidden" name="action"  value="modifica_5g">
    <input type="hidden" name="id_gita" id="mod5g_id">
    <div class="form-grid">
        <div class="form-group">
            <label>Destinazione *</label>
            <input type="text" name="mod_destinazione" id="mod5g_destinazione" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Descrizione</label>
            <input type="text" name="mod_descrizione" id="mod5g_descrizione" class="form-control" placeholder="Breve descrizione">
        </div>
        <div class="form-group">
            <label>Mezzo di trasporto</label>
            <select name="mod_mezzo" id="mod5g_mezzo" class="form-control">
                <option value="">— Seleziona —</option>
                <option value="Bus GT">Bus GT</option>
                <option value="Treno">Treno</option>
                <option value="Aereo">Aereo</option>
            </select>
        </div>
        <div class="form-group">
            <label>Periodo</label>
            <input type="text" name="mod_periodo" id="mod5g_periodo" class="form-control">
        </div>
        <div class="form-group">
            <label>Classe/i</label>
            <input type="text" name="mod_classi" id="mod5g_classi" class="form-control" placeholder="es. 4A">
        </div>
        <div class="form-group">
            <label>Costo a Persona (&euro;)</label>
            <input type="number" name="mod_costo" id="mod5g_costo" class="form-control" step="0.50" min="0">
        </div>
    </div>
</form>
</div>
<div class="modal-footer">
    <button type="button" class="button cancel" onclick="document.getElementById('modalMod5g').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formMod5g" class="button">Salva Modifiche</button>
</div>
</div>
</div>


<!-- modal: conferma elimina (solo commissione) -->
<div class="modal-overlay hidden" id="modalElimina">
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalElimina').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--hex-red);margin-bottom:0.5rem;">Conferma Eliminazione</h3>
    <p style="color:var(--blue-900);margin-bottom:0.5rem;">Sei sicuro di voler eliminare la gita verso:</p>
    <p style="font-weight:600;color:var(--blue-700);font-size:1.1rem;margin-bottom:0.5rem;" id="elimDest"></p>
    <p style="color:#64748b;font-size:0.9rem;">L'operazione non è reversibile.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button type="button" class="button cancel-outline" onclick="document.getElementById('modalElimina').classList.add('hidden')">Annulla</button>
    <button type="submit" form="formElimina" class="button cancel">Elimina</button>
</div>
</div>
</div>
<form method="POST" id="formElimina" style="display:none;">
    <input type="hidden" name="action"  id="elimTabella">
    <input type="hidden" name="id_gita" id="elimId">
</form>

<!-- modal: organizzazione completata -->
<div class="modal-overlay hidden" id="modalOrganizzaOk">
<div class="modal" style="text-align:center;max-width:420px;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="document.getElementById('modalOrganizzaOk').classList.add('hidden')">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 style="color:var(--blue-700);margin-bottom:0.5rem;">Gita Organizzata</h3>
    <p style="color:#475569;">La gita e stata messa in organizzazione con successo.</p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button" onclick="document.getElementById('modalOrganizzaOk').classList.add('hidden')">OK</button>
</div>
</div>
</div>

<?php include('footer.php'); ?>
</div><!-- /container -->
</body>
</html>


