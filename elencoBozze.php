<?php
include('nav.php');

$messaggio = "";

// azioni approva e boccia gita 1 giorno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'azione_1g') {
    $idGita     = (int)$_POST['id_gita'];
    $nuovoStato = ($_POST['azione'] === 'approva') ? 2 : 3;
    if ($conn->query("UPDATE gita1g SET idStato = $nuovoStato WHERE idGita = $idGita")) {
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'azione_5g') {
    $idGita     = (int)$_POST['id_gita'];
    $nuovoStato = ($_POST['azione'] === 'approva') ? 2 : 3;
    if ($conn->query("UPDATE gite5 SET idStato = $nuovoStato WHERE idGita = $idGita")) {
        if ($nuovoStato == 2) {
            $messaggio = "<div class='alert alert-success'>Gita approvata! Ora è visibile in <a href='catalogo.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Proposte</a>.</div>";
        } else {
            $messaggio = "<div class='alert alert-success'>Gita bocciata. Il docente vedrà il risultato in <a href='mieGite.php' style='color:inherit;text-decoration:underline;font-weight:bold;'>Le mie Gite</a>.</div>";
        }
    } else {
        $messaggio = "<div class='alert alert-error'>Errore aggiornamento.</div>";
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

$tot1g = $bozze1g ? $bozze1g->num_rows : 0;
$tot5g = $bozze5g ? $bozze5g->num_rows : 0;
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
    <link rel="stylesheet" href="style_custom.css">
    <script src="vetrina.js" defer></script>
    <script>
    function apriConferma(idGita, azione, tabella, destinazione) {
        var modal = document.getElementById('modalConferma');
        var titolo = azione === 'approva' ? 'Approva Proposta' : 'Boccia Proposta';
        var testo  = azione === 'approva'
            ? 'Sei sicuro di voler <strong>approvare</strong> la gita verso <br><strong style="font-size:1.1rem;color:var(--blue-700);">' + destinazione + '</strong>?'
            : 'Sei sicuro di voler <strong>bocciare</strong> la gita verso <br><strong style="font-size:1.1rem;color:var(--blue-700);">' + destinazione + '</strong>?';

        document.getElementById('confTitolo').innerHTML   = titolo;
        document.getElementById('confTitolo').style.color = azione === 'approva' ? 'var(--blue-700)' : 'var(--hex-red)';
        document.getElementById('confTesto').innerHTML    = testo;
        document.getElementById('confIdGita').value       = idGita;
        document.getElementById('confAzione').value       = azione;
        document.getElementById('confTabella').value      = tabella;

        var btnConf = document.getElementById('btnConferma');
        btnConf.className = azione === 'approva' ? 'button' : 'button cancel';

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
        $mezzo  = htmlspecialchars(isset($r['mezzo']) ? $r['mezzo'] : '—');
        $per    = htmlspecialchars(isset($r['periodo']) ? $r['periodo'] : '—');
        $costo  = number_format($r['costoAPersona'], 2, ',', '.');
        $autore = htmlspecialchars($r['Nome'] . ' ' . $r['Cognome']);
        $id     = (int)$r['idGita'];
        echo "<tr>
            <td>$dest</td>
            <td>$mezzo</td>
            <td>$per</td>
            <td>&euro; $costo</td>
            <td>$autore</td>
            <td style='display:flex;gap:0.4rem;'>
                <button type='button' class='button xs'        onclick=\"apriConferma($id,'approva','azione_1g','$dest')\">Approva</button>
                <button type='button' class='button cancel xs' onclick=\"apriConferma($id,'boccia','azione_1g','$dest')\">Boccia</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' style='text-align:center;'>Nessuna bozza di gita 1 giorno in attesa.</td></tr>";
}
?>
</tbody>
</table>
</div></div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
    <h3 style="color:var(--blue-700);margin:0;">Bozze gite per le quinte</h3>
</div>

<div class="table-section"><div class="table-container">
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
        $mezzo  = htmlspecialchars(isset($r['mezzo']) ? $r['mezzo'] : '—');
        $per    = htmlspecialchars(isset($r['periodo']) ? $r['periodo'] : '—');
        $costo  = number_format($r['costoAPersona'], 2, ',', '.');
        $autore = htmlspecialchars($r['Nome'] . ' ' . $r['Cognome']);
        $id     = (int)$r['idGita'];
        echo "<tr>
            <td>$dest</td>
            <td>$mezzo</td>
            <td>$per</td>
            <td>&euro; $costo</td>
            <td>$autore</td>
            <td style='display:flex;gap:0.4rem;'>
                <button type='button' class='button xs'        onclick=\"apriConferma($id,'approva','azione_5g','$dest')\">Approva</button>
                <button type='button' class='button cancel xs' onclick=\"apriConferma($id,'boccia','azione_5g','$dest')\">Boccia</button>
            </td>
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
<div class="modal" style="max-width:400px;text-align:center;">
<div class="modal-header" style="justify-content:center;border-bottom:none;padding-bottom:0;">
    <button class="close-btn" style="position:absolute;right:1rem;top:1rem;" onclick="chiudiConferma()">&times;</button>
</div>
<div class="modal-body" style="padding-top:0.5rem;">
    <h3 id="confTitolo" style="margin-bottom:0.5rem;">Conferma</h3>
    <p id="confTesto" style="color:var(--blue-900);font-size:1rem;margin-bottom:0.5rem;"></p>
</div>
<div class="modal-footer" style="justify-content:center;">
    <button class="button cancel-outline" onclick="chiudiConferma()">Annulla</button>
    <button class="button" id="btnConferma" onclick="document.getElementById('formConferma').submit()">Conferma</button>
</div>
</div>
</div>

<!-- form nascosto per invio post -->
<form method="POST" id="formConferma" style="display:none;">
    <input type="hidden" name="action"   id="confTabella">
    <input type="hidden" name="id_gita"  id="confIdGita">
    <input type="hidden" name="azione"   id="confAzione">
</form>

<footer><div class="footer-container"><div class="footer-left"><p><strong>Gestione Gite Scolastiche</strong></p></div></div></footer>
</div><!-- /container -->
</body>
</html>


