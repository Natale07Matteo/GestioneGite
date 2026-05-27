<?php
/**
 * Login via Portale Calvino — Gestione Gite Scolastiche
 *
 * Flusso:
 *  1. Passa i cookie del browser all'API del portale tramite cURL
 *  2. Il portale risponde con i dati dell'utente già autenticato con Google
 *  3. Cerca nel DB locale per portale_id (se già collegato) o per email (prima volta)
 *  4. Prima volta: registra automaticamente con i dati del portale
 *     → se il ruolo non è tra quelli consentiti, risponde con errore
 *  5. Ogni volta: aggiorna nome/foto, avvia la sessione
 *
 * Risponde in JSON (chiamato via fetch() da login.php)
 */

session_start();
require_once('config.php');

header('Content-Type: application/json; charset=utf-8');

// ─── Già loggato → niente da fare ────────────────────────────────────────────
if (isset($_SESSION['ruolo']) && $_SESSION['ruolo']) {
    echo json_encode(['success' => true, 'redirect' => 'index.php']);
    exit;
}

// ─── Solo POST ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metodo non consentito.']);
    exit;
}

// ─── Mappa ruoli portale → IDTipo locale ─────────────────────────────────────
// IDTipo 1 = Docente, IDTipo 2 = Commissione
$RUOLI_PORTALE_MAP = [
    'docente'        => 1,
    'admin'          => 2,
    'vicepresidenza' => 2,
    'segreteria'     => 2,
    'personale'      => 2,
];

// ─── SVILUPPO LOCALE: SIMULAZIONE PORTALE (SOLO SU LOCALHOST) ────────────────
$isLocalhost = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);
$mockActive = ($isLocalhost && isset($_GET['mock']));
$data = null;

if ($mockActive) {
    $mockRole = $_GET['mock_role'] ?? 'docente';
    $mockMail = $_GET['mock_email'] ?? 'mario.rossi@calvino.edu.it';
    $mockName = $_GET['mock_nome'] ?? 'Mario';
    $mockSurname = $_GET['mock_cognome'] ?? 'Rossi';
    $mockId = (int)($_GET['mock_id'] ?? 9999);
    $mockFoto = $_GET['mock_foto'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop';

    $data = [
        'success' => true,
        'data' => [
            'user' => [
                'id' => $mockId,
                'email' => $mockMail,
                'name' => $mockName,
                'surname' => $mockSurname,
                'profile_image' => $mockFoto,
                'role' => $mockRole
            ]
        ]
    ];
} else {
    // ─── 1. Costruisce l'header Cookie da passare al portale ─────────────────────
    $cookieHeader = '';
    if (!empty($_COOKIE)) {
        $pairs = [];
        foreach ($_COOKIE as $name => $value) {
            if (is_string($value)) {
                $pairs[] = urlencode($name) . '=' . urlencode($value);
            }
        }
        $cookieHeader = implode('; ', $pairs);
    }

    // ─── 2. Chiama l'API del portale con cURL ────────────────────────────────────
    $ch = curl_init('https://portale.calvino.edu.it/api/tokens/user');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_COOKIE         => $cookieHeader,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // ─── Gestione errore cURL (portale irraggiungibile) ───────────────────────────
    if ($curlError) {
        error_log('[portal_login gite] Errore cURL: ' . $curlError);
        echo json_encode([
            'success' => false,
            'error'   => 'Impossibile contattare il portale Calvino. Riprova più tardi.',
        ]);
        exit;
    }

    // ─── Gestione risposta non valida o non autorizzata ──────────────────────────
    $data = json_decode($response, true);

    if ($httpCode !== 200 || !isset($data['success'])) {
        error_log('[portal_login gite] Risposta portale non valida. HTTP ' . $httpCode . ' — ' . $response);
        echo json_encode([
            'success' => false,
            'error'   => 'Sessione portale non trovata. Assicurati di essere autenticato su <a href="https://portale.calvino.edu.it" target="_blank">portale.calvino.edu.it</a> prima di procedere.',
        ]);
        exit;
    }

    if (!$data['success']) {
        $errorMsg = is_array($data['errors'] ?? null)
            ? implode(', ', $data['errors'])
            : ($data['message'] ?? 'Errore sconosciuto dal portale.');
        error_log('[portal_login gite] Portale: ' . $errorMsg);
        echo json_encode([
            'success' => false,
            'error'   => 'Il portale ha risposto con un errore: ' . htmlspecialchars($errorMsg),
        ]);
        exit;
    }
}

// ─── 3. Estrae i dati utente dalla risposta ───────────────────────────────────
$pUser = $data['data']['user'] ?? null;

if (!$pUser || empty($pUser['id']) || empty($pUser['email'])) {
    error_log('[portal_login gite] Dati utente mancanti nella risposta: ' . $response);
    echo json_encode([
        'success' => false,
        'error'   => 'Dati utente incompleti ricevuti dal portale.',
    ]);
    exit;
}

$portaleId      = (int) $pUser['id'];
$portaleMail    = strtolower(trim($pUser['email']));
$portaleNome    = trim($pUser['name'] ?? '');
$portaleCognome = trim($pUser['surname'] ?? '');
$portaleFoto    = trim($pUser['profile_image'] ?? '');

// Ruolo dal portale → mappa al ruolo locale (IDTipo)
$portaleRuoloRaw = strtolower(trim($pUser['role'] ?? $pUser['ruolo'] ?? ''));
$idTipoLocale    = $RUOLI_PORTALE_MAP[$portaleRuoloRaw] ?? null;

// ─── 4. Cerca utente nel DB per portale_id (accessi successivi) ───────────────
$utente = null;
$stmt = $conn->prepare("SELECT IDUtente, Nome, Cognome, Mail, IDTipo, portale_id, portale_foto FROM utente WHERE portale_id = ? LIMIT 1");
$stmt->bind_param("i", $portaleId);
$stmt->execute();
$result = $stmt->get_result();
$utente = $result->fetch_assoc();
$stmt->close();

// ─── 5. Prima volta: cerca per email o registra il nuovo utente ───────────────
if (!$utente) {
    $stmt = $conn->prepare("SELECT IDUtente, Nome, Cognome, Mail, IDTipo, portale_id, portale_foto FROM utente WHERE LOWER(Mail) = ? LIMIT 1");
    $stmt->bind_param("s", $portaleMail);
    $stmt->execute();
    $result = $stmt->get_result();
    $utente = $result->fetch_assoc();
    $stmt->close();

    if (!$utente) {
        // ── Nuovo utente: verifica che il ruolo sia consentito ────────────────
        if ($idTipoLocale === null) {
            error_log('[portal_login gite] Ruolo non consentito: ' . $portaleMail . ' (ruolo portale: ' . $portaleRuoloRaw . ')');
            echo json_encode([
                'success' => false,
                'error'   => 'Il tuo ruolo sul portale (<strong>' . htmlspecialchars($portaleRuoloRaw ?: 'non specificato') . '</strong>) non è consentito in questa applicazione.',
            ]);
            exit;
        }

        // ── Inserisce il nuovo utente con password inutilizzabile ─────────────
        $passwordFake = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO utente (Nome, Cognome, Mail, Password, IDTipo, portale_id, portale_foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiis", $portaleNome, $portaleCognome, $portaleMail, $passwordFake, $idTipoLocale, $portaleId, $portaleFoto);
        $stmt->execute();
        $nuovoId = $stmt->insert_id;
        $stmt->close();

        $utente = [
            'IDUtente'     => $nuovoId,
            'Nome'         => $portaleNome,
            'Cognome'      => $portaleCognome,
            'Mail'         => $portaleMail,
            'IDTipo'       => $idTipoLocale,
            'portale_id'   => $portaleId,
            'portale_foto' => $portaleFoto,
        ];

        error_log('[portal_login gite] Nuovo utente registrato: ' . $portaleMail . ' (IDTipo: ' . $idTipoLocale . ')');

    } else {
        // Utente locale già esistente per email: collega portale_id e foto
        $stmt = $conn->prepare("UPDATE utente SET portale_id = ?, portale_foto = ? WHERE IDUtente = ?");
        $stmt->bind_param("isi", $portaleId, $portaleFoto, $utente['IDUtente']);
        $stmt->execute();
        $stmt->close();
    }
}

// ─── 6. Aggiorna nome, cognome e foto ad ogni accesso ─────────────────────────
$nomeAggiornato    = $portaleNome    ?: $utente['Nome'];
$cognomeAggiornato = $portaleCognome ?: $utente['Cognome'];
$fotoAggiornata    = $portaleFoto    ?: ($utente['portale_foto'] ?? '');

$stmt = $conn->prepare("UPDATE utente SET Nome = ?, Cognome = ?, portale_foto = ? WHERE IDUtente = ?");
$stmt->bind_param("sssi", $nomeAggiornato, $cognomeAggiornato, $fotoAggiornata, $utente['IDUtente']);
$stmt->execute();
$stmt->close();

// ─── 7. Avvia la sessione (stesse variabili usate da login.php) ───────────────
session_regenerate_id(true);

$_SESSION['id_utente'] = $utente['IDUtente'];
$_SESSION['username']  = $nomeAggiornato . ' ' . $cognomeAggiornato;
$_SESSION['ruolo']     = (int) $utente['IDTipo'];
$_SESSION['foto']      = $fotoAggiornata ?: null;

// ─── 8. Risposta JSON ─────────────────────────────────────────────────────────
echo json_encode([
    'success'  => true,
    'redirect' => 'index.php',
    'nome'     => $_SESSION['username'],
]);
exit;
