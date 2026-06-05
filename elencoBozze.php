<?php
include('nav.php');

$messaggio = "";

// azioni approva e boccia gita 1 giorno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'azione_1g') {
            $idGita = (int)$_POST['id_gita'];
            
            $nuovoStato = 3; // default boccia
            if ($_POST['azione'] === 'approva') {
                $nuovoStato = 2; // approva
            }
            
            $motivazione = '';
            if (isset($_POST['motivazione'])) {
                $motivazione = mysqli_real_escape_string($conn, trim($_POST['motivazione']));
            }
            
            $motivazioneSql = "NULL";
            if ($nuovoStato == 3) {
                if ($motivazione !== '') {
                    $motivazioneSql = "'$motivazione'";
                }
            }
            
            if ($conn->query("UPDATE gita1g SET idStato = $nuovoStato, motivazione = $motivazioneSql WHERE idGita = $idGita")) {
                if ($nuovoStato == 2) {
                    $messaggio = "<div class='alert alert-success'>Gita approvata! Ora è visibile in <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Proposte</a>.</div>";
                } else {
                    $messaggio = "<div class='alert alert-success'>Gita bocciata. Il docente vedrà il risultato in <a href='mieGite.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Le mie Gite</a>.</div>";
                }
            } else {
                $messaggio = "<div class='alert alert-error'>Errore aggiornamento.</div>";
            }
        }
        
        // azioni approva e boccia gita 5 giorni
        if ($_POST['action'] === 'azione_5g') {
            $idGita = (int)$_POST['id_gita'];
            
            $nuovoStato = 3; // default boccia
            if ($_POST['azione'] === 'approva') {
                $nuovoStato = 2; // approva
            }
            
            $motivazione = '';
            if (isset($_POST['motivazione'])) {
                $motivazione = mysqli_real_escape_string($conn, trim($_POST['motivazione']));
            }
            
            $motivazioneSql = "NULL";
            if ($nuovoStato == 3) {
                if ($motivazione !== '') {
                    $motivazioneSql = "'$motivazione'";
                }
            }
            
            if ($conn->query("UPDATE gite5 SET idStato = $nuovoStato, motivazione = $motivazioneSql WHERE idGita = $idGita")) {
                if ($nuovoStato == 2) {
                    $messaggio = "<div class='alert alert-success'>Gita approvata! Ora è visibile in <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Proposte</a>.</div>";
                } else {
                    $messaggio = "<div class='alert alert-success'>Gita bocciata. Il docente vedrà il risultato in <a href='mieGite.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Le mie Gite</a>.</div>";
                }
            } else {
                $messaggio = "<div class='alert alert-error'>Errore aggiornamento.</div>";
            }
        }
    }
}

// query bozze gita 1 giorno
$bozze1g = $conn->query("
    SELECT g.idGita, g.destinazione, g.mezzo, g.periodo, g.costoAPersona,
           u.Nome, u.Cognome
    FROM gita1g g
    JOIN utente u ON g.idUtente = u.IDUtente
    WHERE g.idStato = 1
    ORDER BY g.idGita DESC
");

// query bozze gita 5 giorni
$bozze5g = $conn->query("
    SELECT g.idGita, g.destinazione, g.mezzo, g.periodo, g.costoAPersona,
           u.Nome, u.Cognome
    FROM gite5 g
    JOIN utente u ON g.idUtente = u.IDUtente
    WHERE g.idStato = 1
    ORDER BY g.idGita DESC
");

$tot1g = 0;
if ($bozze1g) {
    $tot1g = $bozze1g->num_rows;
}

$tot5g = 0;
if ($bozze5g) {
    $tot5g = $bozze5g->num_rows;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elenco Bozze</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css?v=<?php echo time(); ?>">
    <script src="vetrina.js" defer></script>
    <script>
    function apriConferma(idGita, azione, tabella, destinazione) {
        var modal = document.getElementById('modalConferma');
        var titolo = 'Approva Proposta';
        if (azione !== 'approva') {
            titolo = 'Boccia Proposta';
        }
        
        var testo = 'Sei sicuro di voler <strong>approvare</strong> la gita verso <br><strong style="font-size:1.1rem;color:var(--blue-700);">' + destinazione + '</strong>?';
        if (azione !== 'approva') {
            testo = 'Sei sicuro di voler <strong>bocciare</strong> la gita verso <br><strong style="font-size:1.1rem;color:var(--blue-700);">' + destinazione + '</strong>?';
        }

        document.getElementById('confTitolo').innerHTML = titolo;
        
        var coloreTitolo = 'var(--blue-700)';
        if (azione !== 'approva') {
            coloreTitolo = 'var(--hex-red)';
        }
        document.getElementById('confTitolo').style.color = coloreTitolo;
        
        document.getElementById('confTesto').innerHTML    = testo;
        document.getElementById('confIdGita').value       = idGita;
        document.getElementById('confAzione').value       = azione;
        document.getElementById('confTabella').value      = tabella;

        var motivoContainer = document.getElementById('motivoContainer');
        var motivoTextarea  = document.getElementById('confMotivazione');

        if (azione === 'boccia') {
            motivoContainer.style.display = 'block';
            motivoTextarea.setAttribute('required', 'required');
            motivoTextarea.value = '';
        } else {
            motivoContainer.style.display = 'none';
            motivoTextarea.removeAttribute('required');
        }

        var btnConf = document.getElementById('btnConferma');
        var btnClass = 'button';
        if (azione !== 'approva') {
            btnClass = 'button cancel';
        }
        btnConf.className = btnClass;

        modal.classList.remove('hidden');
    }
    function chiudiConferma() {
        document.getElementById('modalConferma').classList.add('hidden');
    }
    </script>
</head>
<body>
<div class="container">
<main class="content bozze-padding">

<?php echo $messaggio; ?>

<div style="display: flex; justify-content: flex-start; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem; margin-bottom: 1.5rem;">
    <div class="search-bar-wrapper" style="margin-bottom: 0; max-width: 380px; width: 100%;">
        <input type="text" id="cercaBozze" onkeyup="cercaInTabelle('cercaBozze', 'table')" placeholder="Cerca destinazione, mezzo, docente...">
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

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <h3 style="color:var(--blue-700);margin:0;">Bozze gite di un giorno</h3>
</div>

<div class="table-section" style="margin-bottom:3rem;"><div class="table-container">
<table>
<thead><tr>
    <th>Destinazione</th>
    <th>Mezzo</th>
    <th>Periodo</th>
    <th>Costo a Persona</th>
    <th>Proposta da</th>
    <th>Azioni</th>
</tr></thead>
<tbody>
<?php
if ($bozze1g && $bozze1g->num_rows > 0) {
    while ($r = $bozze1g->fetch_assoc()) {
        $dest   = htmlspecialchars($r['destinazione']);
        
        $mezzoVal = '—';
        if (isset($r['mezzo'])) {
            $mezzoVal = $r['mezzo'];
        }
        $mezzo  = htmlspecialchars($mezzoVal);
        
        $perVal = '—';
        if (isset($r['periodo'])) {
            $perVal = $r['periodo'];
        }
        $per    = htmlspecialchars($perVal);
        
        $costo  = number_format($r['costoAPersona'], 2, ',', '.');
        $autore = htmlspecialchars($r['Nome'] . ' ' . $r['Cognome']);
        $id     = (int)$r['idGita'];
        echo "<tr>
            <td>$dest</td>
            <td>$mezzo</td>
            <td>$per</td>
            <td>&euro; $costo</td>
            <td>$autore</td>
            <td><div class='azioni-cell'>
                <button type='button' class='button xs'        onclick=\"apriConferma($id,'approva','azione_1g','$dest')\">Approva</button>
                <button type='button' class='button cancel xs' onclick=\"apriConferma($id,'boccia','azione_1g','$dest')\">Boccia</button>
            </div></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align:center;'>Nessuna bozza di gita 1 giorno in attesa.</td></tr>";
}
?>
</tbody>
</table>
</div></div>

<div id="gite-quinte" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; scroll-margin-top: 5rem;">
    <h3 style="color:var(--blue-700);margin:0;">Bozze gite per le quinte</h3>
</div>

<div class="table-section"><div class="table-container table-quinte">
<table>
<thead><tr>
    <th>Destinazione</th>
    <th>Mezzo</th>
    <th>Periodo</th>
    <th>Costo a Persona</th>
    <th>Proposta da</th>
    <th>Azioni</th>
</tr></thead>
<tbody>
<?php
if ($bozze5g && $bozze5g->num_rows > 0) {
    while ($r = $bozze5g->fetch_assoc()) {
        $dest   = htmlspecialchars($r['destinazione']);
        
        $mezzoVal = '—';
        if (isset($r['mezzo'])) {
            $mezzoVal = $r['mezzo'];
        }
        $mezzo  = htmlspecialchars($mezzoVal);
        
        $perVal = '—';
        if (isset($r['periodo'])) {
            $perVal = $r['periodo'];
        }
        $per    = htmlspecialchars($perVal);
        
        $costo  = number_format($r['costoAPersona'], 2, ',', '.');
        $autore = htmlspecialchars($r['Nome'] . ' ' . $r['Cognome']);
        $id     = (int)$r['idGita'];
        echo "<tr>
            <td>$dest</td>
            <td>$mezzo</td>
            <td>$per</td>
            <td>&euro; $costo</td>
            <td>$autore</td>
            <td><div class='azioni-cell'>
                <button type='button' class='button xs'        onclick=\"apriConferma($id,'approva','azione_5g','$dest')\">Approva</button>
                <button type='button' class='button cancel xs' onclick=\"apriConferma($id,'boccia','azione_5g','$dest')\">Boccia</button>
            </div></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align:center;'>Nessuna bozza di gita 5 giorni in attesa.</td></tr>";
}
?>
</tbody>
</table>
</div></div>

</main>

<!-- modal conferma approva boccia -->
<div class="modal-overlay hidden" id="modalConferma">
<div class="modal" style="max-width:450px;text-align:center;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button type="button" class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="chiudiConferma()">&times;</button>
</div>
<form method="POST" id="formConferma" style="margin:0;">
    <input type="hidden" name="action"   id="confTabella">
    <input type="hidden" name="id_gita"  id="confIdGita">
    <input type="hidden" name="azione"   id="confAzione">
    <div class="modal-body" style="padding-top:0.5rem; text-align: left;">
        <h3 id="confTitolo" style="margin-bottom:0.5rem; text-align: center;">Conferma</h3>
        <p id="confTesto" style="color:var(--blue-900);font-size:1rem;margin-bottom:0.5rem; text-align: center;"></p>
        
        <div id="motivoContainer" style="margin-top: 1.2rem; display: none;">
            <label style="font-weight: 600; color: var(--blue-700); margin-bottom: 0.35rem; display: block;">Motivazione Bocciatura *</label>
            <textarea name="motivazione" id="confMotivazione" class="form-control" style="width: 100%; height: 80px; resize: none; border-radius: 8px; padding: 0.5rem; border: 1px solid var(--blue-200);" placeholder="es. Budget insufficiente, date non disponibili..."></textarea>
        </div>
    </div>
    <div class="modal-footer" style="justify-content:center;">
        <button type="button" class="button cancel-outline" onclick="chiudiConferma()">Annulla</button>
        <button type="submit" class="button" id="btnConferma">Conferma</button>
    </div>
</form>
</div>
</div>

<?php include('footer.php'); ?>
</div><!-- /container -->
</body>
</html>


