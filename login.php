<?php
    session_start();
    require_once('config.php');

    // Se già loggato, redirect alla home
    if (isset($_SESSION['ruolo']) && $_SESSION['ruolo']) {
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Login - Gestione Gite</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css">
    <script src="vetrina.js" defer></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--my-background);
            margin: 0;
        }

        .login-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        .login-container {
            width: min(100%, 480px);
        }

        .login-container .card {
            width: 100%;
            box-sizing: border-box;
            padding: 2.5rem !important;
            gap: 1.5rem !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .login-container .card-header {
            padding-bottom: 0.5rem !important;
            border-bottom: none !important;
        }

        .login-container .card-header h2 {
            margin-bottom: 0.5rem;
        }

        .login-container .card-header p {
            color: var(--my-gray) !important;
            font-size: 0.95rem;
        }

        .portal-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--blue-500), var(--blue-700));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
        }

        .portal-logo svg {
            width: 44px;
            height: 44px;
            fill: white;
        }

        #btn-portal-login {
            width: 100%;
            height: 3.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            transition: all 0.2s ease;
        }

        #btn-portal-login svg {
            width: 22px;
            height: 22px;
            fill: white;
            flex-shrink: 0;
        }

        #btn-portal-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .login-alert {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
            text-align: left;
            line-height: 1.4;
        }

        .login-alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .login-alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .login-help {
            font-size: 0.85rem;
            color: var(--my-gray);
            line-height: 1.5;
        }

        .login-help a {
            color: var(--blue-600);
            font-weight: 600;
            text-decoration: none;
        }

        .login-help a:hover {
            text-decoration: underline;
        }

        .divider-line {
            width: 100%;
            height: 1px;
            background: var(--blue-100);
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="card">
                <!-- Logo icona -->
                <div class="portal-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                        <path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/>
                    </svg>
                </div>

                <!-- Intestazione -->
                <div class="card-header">
                    <h2>Accedi</h2>
                    <p>Utilizza il tuo account del Portale Calvino per accedere alla gestione gite scolastiche.</p>
                </div>

                <!-- Messaggi di errore/successo -->
                <div id="login-error" class="login-alert error"></div>
                <div id="login-success" class="login-alert success"></div>

                <!-- Pulsante login portale -->
                <button id="btn-portal-login" class="button" onclick="loginPortale()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                        <path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/>
                    </svg>
                    Accedi con Portale Calvino
                </button>

                <div class="divider-line"></div>

                <!-- Aiuto -->
                <p class="login-help">
                    Assicurati di aver effettuato l'accesso su
                    <a href="https://portale.calvino.edu.it" target="_blank">portale.calvino.edu.it</a>
                    prima di procedere.
                </p>
            </div>

            <?php
            $isLocalhost = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);
            if ($isLocalhost):
            ?>
            <!-- PANNELLO SVILUPPATORE (VISIBILE SOLO SU LOCALHOST) -->
            <div class="card dev-panel" style="margin-top: 1.5rem; border: 1px dashed var(--blue-400); background: rgba(37, 99, 235, 0.03); padding: 1.5rem !important; text-align: left; gap: 1rem !important; display: flex; flex-direction: column; width: 100%; box-sizing: border-box;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.2rem;">🔧</span>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600; color: var(--blue-800);">Pannello Sviluppatore (Localhost)</h3>
                </div>
                <p style="margin: 0; font-size: 0.85rem; color: var(--my-gray); line-height: 1.4;">
                    Usa questo pannello in locale per simulare le risposte dell'API del Portale Calvino. Il sistema eseguirà le reali operazioni nel DB e l'avvio della sessione.
                </p>

                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--my-dark);">Seleziona scenario di test:</label>
                    <select id="dev-scenario" onchange="aggiornaCampiDev()" style="padding: 0.6rem; border-radius: var(--radius); border: 1px solid var(--blue-200); font-family: inherit; font-size: 0.9rem; width: 100%; box-sizing: border-box; background: white; cursor: pointer;">
                        <option value="docente_nuovo">Mario Rossi — Nuovo Docente (Ruolo: docente)</option>
                        <option value="commissione_nuovo">Anna Bianchi — Nuova Commissione (Ruolo: admin)</option>
                        <option value="non_consentito">Luigi Verdi — Ruolo Non Consentito (Ruolo: studente)</option>
                        <option value="personalizzato">Personalizzato (collega account esistente per email)</option>
                    </select>
                </div>

                <div id="dev-custom-email-container" style="display: none; flex-direction: column; gap: 0.4rem;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: var(--my-dark);">Email per collegamento account esistente:</label>
                    <input type="email" id="dev-custom-email" placeholder="es. docente.prova@calvino.edu.it" style="padding: 0.6rem; border-radius: var(--radius); border: 1px solid var(--blue-200); font-family: inherit; font-size: 0.9rem; width: 100%; box-sizing: border-box;">
                </div>

                <button class="button" onclick="avviaSimulazioneDev()" style="width: 100%; height: 2.8rem; font-size: 0.95rem; font-weight: 600; background: linear-gradient(135deg, var(--blue-600), var(--blue-800)); border-radius: var(--radius); color: white; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: all 0.2s ease;">
                    Simula Login Portale
                </button>
            </div>

            <script>
                function aggiornaCampiDev() {
                    const scenario = document.getElementById('dev-scenario').value;
                    const container = document.getElementById('dev-custom-email-container');
                    if (scenario === 'personalizzato') {
                        container.style.display = 'flex';
                    } else {
                        container.style.display = 'none';
                    }
                }

                function avviaSimulazioneDev() {
                    const scenario = document.getElementById('dev-scenario').value;
                    let query = '?mock=1';

                    if (scenario === 'docente_nuovo') {
                        query += '&mock_role=docente&mock_nome=Mario&mock_cognome=Rossi&mock_email=mario.rossi@calvino.edu.it&mock_id=8801&mock_foto=https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=120&h=120&fit=crop';
                    } else if (scenario === 'commissione_nuovo') {
                        query += '&mock_role=admin&mock_nome=Anna&mock_cognome=Bianchi&mock_email=anna.bianchi@calvino.edu.it&mock_id=8802&mock_foto=https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=120&h=120&fit=crop';
                    } else if (scenario === 'non_consentito') {
                        query += '&mock_role=studente&mock_nome=Luigi&mock_cognome=Verdi&mock_email=luigi.verdi@calvino.edu.it&mock_id=8803';
                    } else if (scenario === 'personalizzato') {
                        const emailInput = document.getElementById('dev-custom-email').value.trim();
                        if (!emailInput) {
                            alert('Inserisci un indirizzo email valido per testare il collegamento.');
                            return;
                        }
                        const parti = emailInput.split('@')[0].split('.');
                        const nome = parti[0] ? parti[0].charAt(0).toUpperCase() + parti[0].slice(1) : 'Test';
                        const cognome = parti[1] ? parti[1].charAt(0).toUpperCase() + parti[1].slice(1) : 'Collegato';
                        
                        query += `&mock_role=docente&mock_nome=${encodeURIComponent(nome)}&mock_cognome=${encodeURIComponent(cognome)}&mock_email=${encodeURIComponent(emailInput)}&mock_id=8899&mock_foto=https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120&h=120&fit=crop`;
                    }

                    loginPortale(query);
                }
            </script>
            <?php endif; ?>
        </div>
    </div>

    <script>
        async function loginPortale(mockQuery = '') {
            const btn   = document.getElementById('btn-portal-login');
            const errEl = document.getElementById('login-error');
            const okEl  = document.getElementById('login-success');

            // Reset stato
            errEl.style.display = 'none';
            okEl.style.display  = 'none';
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Connessione in corso…';

            try {
                const res = await fetch('portal_login.php' + mockQuery, {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                });

                const data = await res.json();

                if (data.success) {
                    okEl.textContent = '✓ Accesso effettuato! Reindirizzamento…';
                    okEl.style.display = 'flex';
                    setTimeout(() => {
                        window.location.href = data.redirect || 'index.php';
                    }, 600);
                } else {
                    errEl.innerHTML = data.error || 'Errore durante il login.';
                    errEl.style.display = 'flex';
                    btn.disabled = false;
                    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg> Accedi con Portale Calvino';
                }
            } catch (e) {
                errEl.textContent = 'Errore di connessione. Verifica la tua rete e riprova.';
                errEl.style.display = 'flex';
                btn.disabled = false;
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960"><path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/></svg> Accedi con Portale Calvino';
            }
        }
    </script>
</body>
</html>
