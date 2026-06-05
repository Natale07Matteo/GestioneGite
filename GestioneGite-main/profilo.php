<?php
include('nav.php');
require_once('utils.php');

// protezione per utenti non loggati
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit;
}

$idUtente = (int)$_SESSION['id_utente'];

// carica dati utente dal database
$risultato = $conn->query("SELECT Nome, Cognome, Mail, IDTipo FROM utente WHERE IDUtente = $idUtente");

$utente = null;
if ($risultato) {
    $utente = $risultato->fetch_assoc();
}

if (!$utente) {
    header("Location: login.php");
    exit;
}

// conta proposte e organizzazione per utente (1g)
$conta1g = $conn->query("SELECT COUNT(CASE WHEN idStato IN (1,2,3) THEN 1 END) AS proposte, COUNT(CASE WHEN idStato = 4 THEN 1 END) AS organizza FROM gita1g WHERE idUtente = $idUtente");
$c1 = array();
if ($conta1g) {
    $c1 = $conta1g->fetch_assoc();
}

// conta proposte e organizzazione per utente (5g)
$conta5g = $conn->query("SELECT COUNT(CASE WHEN idStato IN (1,2,3) THEN 1 END) AS proposte, COUNT(CASE WHEN idStato = 4 THEN 1 END) AS organizza FROM gite5 WHERE idUtente = $idUtente");
$c5 = array();
if ($conta5g) {
    $c5 = $conta5g->fetch_assoc();
}

// Calcolo proposte
$prop1 = 0;
if (isset($c1['proposte'])) { $prop1 = $c1['proposte']; }

$prop5 = 0;
if (isset($c5['proposte'])) { $prop5 = $c5['proposte']; }

$totProposte = $prop1 + $prop5;

// Calcolo organizzazione
$org1 = 0;
if (isset($c1['organizza'])) { $org1 = $c1['organizza']; }

$org5 = 0;
if (isset($c5['organizza'])) { $org5 = $c5['organizza']; }

$totOrganizzazione = $org1 + $org5;

// conta gite dove e accompagnatore (ma non autore)
$accomp1g = $conn->query("SELECT COUNT(*) AS tot FROM accompagnatori a JOIN gita1g g ON a.idgita = g.idGita AND a.tipo_gita = '1g' WHERE a.idutente = $idUtente AND g.idUtente <> $idUtente");
$totAccomp1 = 0;
if ($accomp1g) {
    $r1 = $accomp1g->fetch_assoc();
    $totAccomp1 = $r1['tot'];
}

$accomp5g = $conn->query("SELECT COUNT(*) AS tot FROM accompagnatori a JOIN gite5 g ON a.idgita = g.idGita AND a.tipo_gita = '5g' WHERE a.idutente = $idUtente AND g.idUtente <> $idUtente");
$totAccomp5 = 0;
if ($accomp5g) {
    $r5 = $accomp5g->fetch_assoc();
    $totAccomp5 = $r5['tot'];
}

$totAccompagnatore = $totAccomp1 + $totAccomp5;

// Preparazione variabile tipo utente
$idTipoUtente = 1;
if (isset($utente['IDTipo'])) {
    $idTipoUtente = $utente['IDTipo'];
}
$nomeRuoloProfilo = nomeRuolo($idTipoUtente);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo - Gestione Gite</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css?v=<?php echo time(); ?>">
    <script src="vetrina.js" defer></script>
    <style>
        .profilo-wrapper {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .profilo-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .profilo-avatar {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: var(--blue-200);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .profilo-avatar span {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--blue-700);
        }
        .profilo-nome {
            color: var(--blue-700);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .profilo-ruolo {
            display: inline-block;
            background: var(--blue-100);
            color: var(--blue-700);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.2rem 0.7rem;
            border-radius: 99px;
            margin-top: 0.3rem;
        }
        .profilo-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .profilo-info-item {
            background: var(--my-white);
            border: 1px solid var(--blue-100);
            border-radius: var(--radius-1);
            padding: 1rem 1.2rem;
        }
        .profilo-info-item label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--blue-400);
            text-transform: uppercase;
            letter-spacing: 0.03em;
            display: block;
            margin-bottom: 0.3rem;
        }
        .profilo-info-item span {
            font-size: 1rem;
            color: var(--blue-900);
            font-weight: 500;
        }
        .profilo-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .profilo-stat-card {
            background: var(--my-white);
            border: 1px solid var(--blue-100);
            border-radius: var(--radius-1);
            padding: 1.2rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.08);
        }
        .profilo-stat-card .stat-numero {
            font-size: 2rem;
            font-weight: 700;
            color: var(--blue-600);
            display: block;
        }
        .profilo-stat-card .stat-label {
            font-size: 0.85rem;
            color: var(--my-gray);
            margin-top: 0.3rem;
            display: block;
        }
        @media (max-width: 600px) {
            .profilo-info-grid {
                grid-template-columns: 1fr;
            }
            .profilo-stats {
                grid-template-columns: 1fr;
            }
            .profilo-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
<main class="content">

    <div class="profilo-wrapper">
        <!-- intestazione profilo -->
        <div class="profilo-header">
            <?php if ($foto_utente): ?>
                <img src="<?php echo htmlspecialchars($foto_utente); ?>" alt="Foto profilo" class="profilo-avatar" style="object-fit:cover;">
            <?php else: ?>
                <div class="profilo-avatar">
                    <span><?php echo strtoupper(substr($utente['Nome'], 0, 1) . substr($utente['Cognome'], 0, 1)); ?></span>
                </div>
            <?php endif; ?>
            <div>
                <h2 class="profilo-nome"><?php echo htmlspecialchars($utente['Nome'] . ' ' . $utente['Cognome']); ?></h2>
                <span class="profilo-ruolo"><?php echo $nomeRuoloProfilo; ?></span>
            </div>
        </div>

        <!-- dati personali -->
        <h3 style="color:var(--blue-700);margin-bottom:1rem;">Dati personali</h3>
        <div class="profilo-info-grid">
            <div class="profilo-info-item">
                <label>Nome</label>
                <span><?php echo htmlspecialchars($utente['Nome']); ?></span>
            </div>
            <div class="profilo-info-item">
                <label>Cognome</label>
                <span><?php echo htmlspecialchars($utente['Cognome']); ?></span>
            </div>
            <div class="profilo-info-item" style="grid-column: span 2;">
                <label>Email</label>
                <span><?php echo htmlspecialchars($utente['Mail']); ?></span>
            </div>
        </div>

        <!-- statistiche -->
        <h3 style="color:var(--blue-700);margin-bottom:1rem;">Riepilogo attivita</h3>
        <div class="profilo-stats">
            <div class="profilo-stat-card">
                <span class="stat-numero"><?php echo $totProposte; ?></span>
                <span class="stat-label">Proposte create</span>
            </div>
            <div class="profilo-stat-card">
                <span class="stat-numero"><?php echo $totOrganizzazione; ?></span>
                <span class="stat-label">In organizzazione</span>
            </div>
            <div class="profilo-stat-card">
                <span class="stat-numero"><?php echo $totAccompagnatore; ?></span>
                <span class="stat-label">Come accompagnatore</span>
            </div>
        </div>
    </div>

</main>

    <?php include('footer.php'); ?>
</div>

</body>
</html>
