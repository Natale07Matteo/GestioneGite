<?php
/**
 * Autenticazione via Portale Calvino — Gestione Gite Scolastiche
 *
 * Funzioni per l'integrazione SSO con il Portale Calvino.
 * Il cookie `user_token` è condiviso su .calvino.edu.it e viene
 * inoltrato via cURL all'API del portale per verificare l'identità.
 *
 * Questo file definisce solo funzioni, nessun side-effect all'inclusione.
 *
 * Funzioni:
 *  - chiamaPortaleAPI()         → cURL GET all'API del portale
 *  - verificaTokenValido()      → controlla che il token sia ancora valido (per utenti già loggati)
 *  - tentaAutoLogin($conn, $r)  → tenta login automatico dal cookie (per utenti non loggati)
 */

// ─── URL del Portale ─────────────────────────────────────────────────────────
define('PORTALE_API_USER',   'https://portale.calvino.edu.it/api/tokens/user');
define('PORTALE_AUTH_GOOGLE', 'https://portale.calvino.edu.it/api/auth/google?origin=gite.calvino.edu.it');
define('PORTALE_LOGOUT',     'https://portale.calvino.edu.it/api/auth/logout');


/**
 * Chiama l'API del portale via cURL GET, inoltrando il cookie user_token.
 *
 * @return array|null  Array decodificato della risposta JSON, oppure null in caso di errore.
 */
function chiamaPortaleAPI()
{
    try {
        if (!function_exists('curl_init')) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> L\'estensione PHP <strong>cURL</strong> non è installata o abilitata su questa VM! Attiva l\'estensione in php.ini.';
            echo '</div>';
            return null;
        }

        // Costruisce l'header Cookie da inoltrare (solo user_token)
        if (empty($_COOKIE['user_token'])) {
            return null;
        }

        $cookieHeader = 'user_token=' . urlencode($_COOKIE['user_token']);

        $ch = curl_init(PORTALE_API_USER);
        if ($ch === false) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> curl_init() ha restituito false per l\'URL: ' . htmlspecialchars(PORTALE_API_USER);
            echo '</div>';
            return null;
        }

        $opts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array(
                'Accept: application/json',
            ),
            CURLOPT_COOKIE         => $cookieHeader,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        );

        if (!curl_setopt_array($ch, $opts)) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> curl_setopt_array() ha fallito nell\'impostare i parametri.';
            echo '</div>';
        }

        $response  = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlError) {
            echo '<div style="padding: 15px; background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG WARNING - portal_auth]</strong> Errore cURL durante il contatto col Portale Calvino:<br>';
            echo '<em>' . htmlspecialchars($curlError) . '</em>';
            echo '</div>';
            error_log('[portal_auth gite] Errore cURL: ' . $curlError);
            return null;
        }

        if ($httpCode !== 200) {
            echo '<div style="padding: 15px; background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG WARNING - portal_auth]</strong> Il portale SSO ha risposto con codice HTTP ' . $httpCode . ' (invece di 200).<br>';
            echo 'Risposta server: <pre style="margin:5px 0 0; background:#fff; padding:5px; border:1px solid #ddd;">' . htmlspecialchars($response) . '</pre>';
            echo '</div>';
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> Risposta non JSON o non valida ricevuta dal portale SSO:<br>';
            echo '<pre style="margin:5px 0 0; background:#fff; padding:5px; border:1px solid #ddd;">' . htmlspecialchars($response) . '</pre>';
            echo '</div>';
            error_log('[portal_auth gite] Risposta non valida dal portale: ' . $response);
            return null;
        }

        return $data;

    } catch (Exception $e) {
        echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
        echo '<strong>[DEBUG EXCEPTION - portal_auth]</strong> Eccezione catturata in chiamaPortaleAPI(): ' . htmlspecialchars($e->getMessage()) . '<br>';
        echo 'File: ' . htmlspecialchars($e->getFile()) . ' alla riga ' . $e->getLine();
        echo '</div>';
        return null;
    } catch (Throwable $t) {
        echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
        echo '<strong>[DEBUG EXCEPTION - portal_auth]</strong> Errore fatale catturato in chiamaPortaleAPI(): ' . htmlspecialchars($t->getMessage()) . '<br>';
        echo 'File: ' . htmlspecialchars($t->getFile()) . ' alla riga ' . $t->getLine();
        echo '</div>';
        return null;
    }
}


/**
 * Verifica che il token dell'utente già loggato sia ancora valido.
 *
 * Se il token non è più valido, cancella la sessione e redirige al logout del portale.
 * Se è valido, aggiorna nome e foto in sessione con i dati freschi del portale.
 *
 * Questa funzione viene chiamata ad ogni caricamento di pagina per utenti con sessione attiva.
 *
 * @return void  (redirige e fa exit se il token non è valido)
 */
function verificaTokenValido()
{
    try {
        $data = chiamaPortaleAPI();

        // Token non valido o portale irraggiungibile → logout
        if (!$data || empty($data['success']) || !isset($data['data']['user'])) {
            session_unset();
            session_destroy();
            header('Location: ' . PORTALE_LOGOUT);
            exit;
        }

        // Token valido → aggiorna nome e foto in sessione (dati sempre freschi)
        $user = $data['data']['user'];
        $nome    = trim(isset($user['name']) ? $user['name'] : '');
        $cognome = trim(isset($user['surname']) ? $user['surname'] : '');
        $foto    = trim(isset($user['profile_image']) ? $user['profile_image'] : '');

        if ($nome || $cognome) {
            $_SESSION['username'] = trim($nome . ' ' . $cognome);
        }
        if ($foto) {
            $_SESSION['foto'] = $foto;
        }
    } catch (Exception $e) {
        echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
        echo '<strong>[DEBUG EXCEPTION - portal_auth]</strong> Eccezione catturata in verificaTokenValido(): ' . htmlspecialchars($e->getMessage()) . '<br>';
        echo 'File: ' . htmlspecialchars($e->getFile()) . ' alla riga ' . $e->getLine();
        echo '</div>';
        exit;
    } catch (Throwable $t) {
        echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
        echo '<strong>[DEBUG EXCEPTION - portal_auth]</strong> Errore fatale catturato in verificaTokenValido(): ' . htmlspecialchars($t->getMessage()) . '<br>';
        echo 'File: ' . htmlspecialchars($t->getFile()) . ' alla riga ' . $t->getLine();
        echo '</div>';
        exit;
    }
}


/**
 * Tenta il login automatico dall'cookie user_token del portale.
 *
 * @param  mysqli  $conn           Connessione al DB locale.
 * @param  array   $PORTAL_ROLES   Mappa ruoli portale → IDTipo (da portal_roles.php).
 *
 * @return true|false|string
 *         true    → login riuscito (sessione avviata)
 *         false   → nessun token presente o portale non raggiungibile
 *         string  → messaggio di errore (es. ruolo non autorizzato)
 */
function tentaAutoLogin($conn, $PORTAL_ROLES)
{
    try {
        // ─── 1. Chiama l'API del portale ─────────────────────────────────────────
        $data = chiamaPortaleAPI();

        if (!$data || empty($data['success'])) {
            return false;
        }

        // ─── 2. Estrae i dati utente ─────────────────────────────────────────────
        $pUser = isset($data['data']['user']) ? $data['data']['user'] : null;

        if (!$pUser || empty($pUser['id']) || empty($pUser['email'])) {
            error_log('[portal_auth gite] Dati utente mancanti nella risposta del portale.');
            return false;
        }

        $portaleMail    = strtolower(trim($pUser['email']));
        $portaleNome    = trim(isset($pUser['name']) ? $pUser['name'] : '');
        $portaleCognome = trim(isset($pUser['surname']) ? $pUser['surname'] : '');
        $portaleFoto    = trim(isset($pUser['profile_image']) ? $pUser['profile_image'] : '');
        $portaleRuoli   = isset($pUser['roles']) ? $pUser['roles'] : [];

        // ─── 3. Cerca il primo ruolo autorizzato ─────────────────────────────────
        $idTipoLocale = null;
        $ruoloTrovato = '';

        foreach ($portaleRuoli as $ruolo) {
            $nomeRuolo = strtolower(trim(isset($ruolo['role_name']) ? $ruolo['role_name'] : ''));
            if (isset($PORTAL_ROLES[$nomeRuolo])) {
                $idTipoLocale = $PORTAL_ROLES[$nomeRuolo];
                $ruoloTrovato = $nomeRuolo;
                break;
            }
        }

        if ($idTipoLocale === null) {
            $ruoliUtente = array_map(function ($r) {
                return isset($r['role_name']) ? $r['role_name'] : '?';
            }, $portaleRuoli);
            error_log('[portal_auth gite] Ruolo non autorizzato per ' . $portaleMail . ': ' . implode(', ', $ruoliUtente));
            return 'Il tuo ruolo sul portale (<strong>' . htmlspecialchars(implode(', ', $ruoliUtente)) . '</strong>) non è autorizzato per questa applicazione. Contatta un amministratore.';
        }

        // Verifica che la connessione al database esista
        if (!$conn) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> La connessione al database ($conn) è NULL o non valida!<br>';
            echo 'Verifica la configurazione in <strong>config.php</strong>.';
            echo '</div>';
            return false;
        }

        // ─── 4. Cerca utente nel DB per email ────────────────────────────────────
        $utente = null;
        $stmt = $conn->prepare("SELECT IDUtente, Nome, Cognome, Mail, IDTipo FROM utente WHERE LOWER(Mail) = ? LIMIT 1");
        if ($stmt === false) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> prepare SELECT fallito: ' . htmlspecialchars($conn->error);
            echo '</div>';
            return false;
        }

        $stmt->bind_param("s", $portaleMail);
        if (!$stmt->execute()) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> execute SELECT fallito: ' . htmlspecialchars($stmt->error);
            echo '</div>';
            $stmt->close();
            return false;
        }

        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $dbId = null;
            $dbNome = null;
            $dbCognome = null;
            $dbMail = null;
            $dbTipo = null;
            $stmt->bind_result($dbId, $dbNome, $dbCognome, $dbMail, $dbTipo);
            $stmt->fetch();
            $utente = [
                'IDUtente' => $dbId,
                'Nome'     => $dbNome,
                'Cognome'  => $dbCognome,
                'Mail'     => $dbMail,
                'IDTipo'   => $dbTipo,
            ];
        }
        $stmt->close();

        // ─── 5. Utente non trovato → crea un nuovo account ──────────────────────
        if (!$utente) {
            $rawBytes = '';
            if (function_exists('random_bytes')) {
                try {
                    $rawBytes = random_bytes(16);
                } catch (Exception $e) {
                    $rawBytes = uniqid(mt_rand(), true);
                }
            } else {
                $rawBytes = uniqid(mt_rand(), true);
            }
            $passwordFake = password_hash(bin2hex($rawBytes), PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO utente (Nome, Cognome, Mail, Password, IDTipo) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
                echo '<strong>[DEBUG ERROR - portal_auth]</strong> prepare INSERT fallito: ' . htmlspecialchars($conn->error);
                echo '</div>';
                return false;
            }

            $stmt->bind_param("ssssi", $portaleNome, $portaleCognome, $portaleMail, $passwordFake, $idTipoLocale);
            if (!$stmt->execute()) {
                echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
                echo '<strong>[DEBUG ERROR - portal_auth]</strong> execute INSERT fallito: ' . htmlspecialchars($stmt->error);
                echo '</div>';
                $stmt->close();
                return false;
            }

            $nuovoId = $stmt->insert_id;
            $stmt->close();

            $utente = [
                'IDUtente' => $nuovoId,
                'Nome'     => $portaleNome,
                'Cognome'  => $portaleCognome,
                'Mail'     => $portaleMail,
                'IDTipo'   => $idTipoLocale,
            ];

            error_log('[portal_auth gite] Nuovo utente creato: ' . $portaleMail . ' (IDTipo: ' . $idTipoLocale . ')');
        }

        // ─── 6. Aggiorna nome e cognome nel DB ───────────────────────────────────
        $nomeAggiornato    = $portaleNome    ?: $utente['Nome'];
        $cognomeAggiornato = $portaleCognome ?: $utente['Cognome'];

        $stmt = $conn->prepare("UPDATE utente SET Nome = ?, Cognome = ? WHERE IDUtente = ?");
        if ($stmt === false) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> prepare UPDATE fallito: ' . htmlspecialchars($conn->error);
            echo '</div>';
            return false;
        }

        $stmt->bind_param("ssi", $nomeAggiornato, $cognomeAggiornato, $utente['IDUtente']);
        if (!$stmt->execute()) {
            echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>[DEBUG ERROR - portal_auth]</strong> execute UPDATE fallito: ' . htmlspecialchars($stmt->error);
            echo '</div>';
            $stmt->close();
            return false;
        }
        $stmt->close();

        // ─── 7. Avvia la sessione ────────────────────────────────────────────────
        session_regenerate_id(true);

        $_SESSION['id_utente'] = $utente['IDUtente'];
        $_SESSION['username']  = $nomeAggiornato . ' ' . $cognomeAggiornato;
        $_SESSION['ruolo']     = (int) $utente['IDTipo'];
        $_SESSION['foto']      = $portaleFoto ?: null;

        error_log('[portal_auth gite] Login riuscito: ' . $portaleMail . ' (IDTipo: ' . $utente['IDTipo'] . ')');

        return true;

    } catch (Exception $e) {
        echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
        echo '<strong>[DEBUG EXCEPTION - portal_auth]</strong> Eccezione in tentaAutoLogin(): ' . htmlspecialchars($e->getMessage()) . '<br>';
        echo 'File: ' . htmlspecialchars($e->getFile()) . ' alla riga ' . $e->getLine();
        echo '</div>';
        return false;
    } catch (Throwable $t) {
        echo '<div style="padding: 15px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; font-family: monospace; margin: 10px 0; border-radius: 6px;">';
        echo '<strong>[DEBUG EXCEPTION - portal_auth]</strong> Errore fatale catturato in tentaAutoLogin(): ' . htmlspecialchars($t->getMessage()) . '<br>';
        echo 'File: ' . htmlspecialchars($t->getFile()) . ' alla riga ' . $t->getLine();
        echo '</div>';
        return false;
    }
}

