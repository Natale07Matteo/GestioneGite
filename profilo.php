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

// Carica i dettagli delle proposte create
$dettagliProposte = [];
$rProp1 = $conn->query("SELECT idGita, destinazione, periodo, idStato, '1g' AS tipo FROM gita1g WHERE idUtente = $idUtente AND idStato IN (1,2,3) ORDER BY idGita DESC");
if ($rProp1) { while ($row = $rProp1->fetch_assoc()) $dettagliProposte[] = $row; }
$rProp5 = $conn->query("SELECT idGita, destinazione, periodo, idStato, '5g' AS tipo FROM gite5 WHERE idUtente = $idUtente AND idStato IN (1,2,3) ORDER BY idGita DESC");
if ($rProp5) { while ($row = $rProp5->fetch_assoc()) $dettagliProposte[] = $row; }

// Carica i dettagli delle gite in organizzazione
$dettagliOrganizza = [];
$rOrg1 = $conn->query("SELECT idGita, destinazione, giorno, idStato, '1g' AS tipo FROM gita1g WHERE idUtente = $idUtente AND idStato IN (4,5) ORDER BY idGita DESC");
if ($rOrg1) { while ($row = $rOrg1->fetch_assoc()) $dettagliOrganizza[] = $row; }
$rOrg5 = $conn->query("SELECT idGita, destinazione, giornoInizio, giornoFine, idStato, '5g' AS tipo FROM gite5 WHERE idUtente = $idUtente AND idStato IN (4,5) ORDER BY idGita DESC");
if ($rOrg5) { while ($row = $rOrg5->fetch_assoc()) $dettagliOrganizza[] = $row; }

// Carica i dettagli delle gite come accompagnatore (ma non autore)
$dettagliAccompagnatore = [];
$rAcc1 = $conn->query("SELECT g.idGita, g.destinazione, g.giorno, g.idStato, '1g' AS tipo FROM gita1g g JOIN accompagnatori a ON a.idgita = g.idGita AND a.tipo_gita = '1g' WHERE a.idutente = $idUtente AND g.idUtente <> $idUtente ORDER BY g.idGita DESC");
if ($rAcc1) { while ($row = $rAcc1->fetch_assoc()) $dettagliAccompagnatore[] = $row; }
$rAcc5 = $conn->query("SELECT g.idGita, g.destinazione, g.giornoInizio, g.giornoFine, g.idStato, '5g' AS tipo FROM gite5 g JOIN accompagnatori a ON a.idgita = g.idGita AND a.tipo_gita = '5g' WHERE a.idutente = $idUtente AND g.idUtente <> $idUtente ORDER BY g.idGita DESC");
if ($rAcc5) { while ($row = $rAcc5->fetch_assoc()) $dettagliAccompagnatore[] = $row; }

// Funzione helper per badge stato nei dettagli
if (!function_exists('getStatoBadge')) {
    function getStatoBadge($idStato) {
        switch ($idStato) {
            case 1: return '<span class="badge badge-secondary" style="font-size:0.75rem;">Bozza</span>';
            case 2: return '<span class="badge badge-success" style="font-size:0.75rem;">Approvata</span>';
            case 3: return '<span class="badge badge-danger" style="font-size:0.75rem;">Bocciata</span>';
            case 4: return '<span class="badge badge-primary" style="font-size:0.75rem; background-color:var(--blue-600);">In Organizzazione</span>';
            case 5: return '<span class="badge badge-primary" style="font-size:0.75rem; background-color:#0284c7;">Conclusa</span>';
            default: return '';
        }
    }
}

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

        <!-- Sezione Dettagli Attività -->
        <div class="profilo-sezione-dettagli" style="margin-top: 2.5rem;">
            <!-- Sezione Proposte -->
            <div class="dettaglio-blocco" style="margin-bottom: 2rem;">
                <h4 style="color:var(--blue-700); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.5rem; font-size:1.1rem; font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M240-80q-33 0-56.5-23.5T160-160v-640q0-33 23.5-56.5T240-880h320l240 240v480q0 33-23.5 56.5T720-80H240Zm280-520v-200H240v640h480v-440H520ZM240-800v200-200 640-640Z"/></svg>
                    Proposte create (<?php echo count($dettagliProposte); ?>)
                </h4>
                <?php if (count($dettagliProposte) === 0): ?>
                    <p style="font-size: 0.9rem; color: var(--my-gray); font-style: italic; margin-left: 0.5rem;">Nessuna proposta creata.</p>
                <?php else: ?>
                    <div class="dettaglio-lista" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($dettagliProposte as $gita): ?>
                            <div class="dettaglio-item" style="background: var(--my-white); border: 1px solid var(--blue-100); border-radius: var(--radius); padding: 0.8rem 1.2rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.04);">
                                <div>
                                    <strong style="color: var(--blue-800); font-size: 0.95rem;"><?php echo htmlspecialchars($gita['destinazione']); ?></strong>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem; display: flex; gap: 1rem;">
                                        <span><strong>Tipo:</strong> <?php echo $gita['tipo'] === '1g' ? '1 Giorno' : 'Più Giorni'; ?></span>
                                        <?php if (!empty($gita['periodo'])): ?><span><strong>Periodo:</strong> <?php echo htmlspecialchars($gita['periodo']); ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <?php echo getStatoBadge($gita['idStato']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sezione In organizzazione -->
            <div class="dettaglio-blocco" style="margin-bottom: 2rem;">
                <h4 style="color:var(--blue-700); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.5rem; font-size:1.1rem; font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T209-763l57 57q-45 39-71 93.5T169-498q4 70 33.5 131.5T282-258q63 53 141.5 78.5T580-169v90q-50 0-100-1Z"/></svg>
                    In organizzazione (<?php echo count($dettagliOrganizza); ?>)
                </h4>
                <?php if (count($dettagliOrganizza) === 0): ?>
                    <p style="font-size: 0.9rem; color: var(--my-gray); font-style: italic; margin-left: 0.5rem;">Nessuna gita in organizzazione.</p>
                <?php else: ?>
                    <div class="dettaglio-lista" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($dettagliOrganizza as $gita): ?>
                            <div class="dettaglio-item" style="background: var(--my-white); border: 1px solid var(--blue-100); border-radius: var(--radius); padding: 0.8rem 1.2rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.04);">
                                <div>
                                    <strong style="color: var(--blue-800); font-size: 0.95rem;"><?php echo htmlspecialchars($gita['destinazione']); ?></strong>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <span><strong>Tipo:</strong> <?php echo $gita['tipo'] === '1g' ? '1 Giorno' : 'Più Giorni'; ?></span>
                                        <?php
                                        $dateStr = '';
                                        if ($gita['tipo'] === '1g' && !empty($gita['giorno'])) {
                                            $dateStr = date('d/m/Y', strtotime($gita['giorno']));
                                        } elseif ($gita['tipo'] === '5g' && !empty($gita['giornoInizio'])) {
                                            $dateStr = date('d/m/Y', strtotime($gita['giornoInizio'])) . ' - ' . date('d/m/Y', strtotime($gita['giornoFine']));
                                        }
                                        if ($dateStr): ?><span><strong>Data:</strong> <?php echo $dateStr; ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <?php echo getStatoBadge($gita['idStato']); ?>
                                    <?php if ($gita['tipo'] === '5g'): ?>
                                        <a href="partecipanti.php?id=<?php echo $gita['idGita']; ?>" class="button xs outline" style="height:2rem; min-width:auto; padding:0 0.8rem; text-decoration:none; font-size:0.75rem; border-width:1px; display:inline-flex; align-items:center;">Partecipanti</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sezione Come accompagnatore -->
            <div class="dettaglio-blocco" style="margin-bottom: 2rem;">
                <h4 style="color:var(--blue-700); margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.5rem; font-size:1.1rem; font-weight:600;">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="currentColor"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM80-160v-112q0-33 17-62t47-44q51-26 115-44t141-18q77 0 141 18t115 44q30 15 47 44t17 62v112H80Zm80-80h480v-32q0-11-5.5-20T620-306q-44-22-101.5-38T380-360q-79 0-136.5 16T142-306q-9 5-14.5 14t-5.5 20v32Zm320-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>
                    Come accompagnatore (<?php echo count($dettagliAccompagnatore); ?>)
                </h4>
                <?php if (count($dettagliAccompagnatore) === 0): ?>
                    <p style="font-size: 0.9rem; color: var(--my-gray); font-style: italic; margin-left: 0.5rem;">Nessuna gita come accompagnatore.</p>
                <?php else: ?>
                    <div class="dettaglio-lista" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($dettagliAccompagnatore as $gita): ?>
                            <div class="dettaglio-item" style="background: var(--my-white); border: 1px solid var(--blue-100); border-radius: var(--radius); padding: 0.8rem 1.2rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.04);">
                                <div>
                                    <strong style="color: var(--blue-800); font-size: 0.95rem;"><?php echo htmlspecialchars($gita['destinazione']); ?></strong>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                                        <span><strong>Tipo:</strong> <?php echo $gita['tipo'] === '1g' ? '1 Giorno' : 'Più Giorni'; ?></span>
                                        <?php
                                        $dateStr = '';
                                        if ($gita['tipo'] === '1g' && !empty($gita['giorno'])) {
                                            $dateStr = date('d/m/Y', strtotime($gita['giorno']));
                                        } elseif ($gita['tipo'] === '5g' && !empty($gita['giornoInizio'])) {
                                            $dateStr = date('d/m/Y', strtotime($gita['giornoInizio'])) . ' - ' . date('d/m/Y', strtotime($gita['giornoFine']));
                                        }
                                        if ($dateStr): ?><span><strong>Data:</strong> <?php echo $dateStr; ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <?php echo getStatoBadge($gita['idStato']); ?>
                                    <?php if ($gita['tipo'] === '5g'): ?>
                                        <a href="partecipanti.php?id=<?php echo $gita['idGita']; ?>" class="button xs outline" style="height:2rem; min-width:auto; padding:0 0.8rem; text-decoration:none; font-size:0.75rem; border-width:1px; display:inline-flex; align-items:center;">Partecipanti</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>

    <?php include('footer.php'); ?>
</div>

</body>
</html>
