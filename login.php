<?php
    session_start();
    require_once('config.php');
    require_once('portal_roles.php');
    require_once('portal_auth.php');

    // Già loggato → vai alla home
    if (isset($_SESSION['ruolo']) && $_SESSION['ruolo']) {
        header("Location: index.php");
        exit;
    }

    // Tenta login automatico via portale (cookie user_token)
    $risultato = tentaAutoLogin($conn, $PORTAL_ROLES);

    if ($risultato === true) {
        // Login riuscito → redirect alla home
        header("Location: index.php");
        exit;
    }

    if ($risultato === false) {
        // Nessun token → manda al portale per autenticazione Google
        header("Location: " . PORTALE_AUTH_GOOGLE);
        exit;
    }

    // $risultato è una stringa di errore (es. ruolo non autorizzato)
    $erroreLogin = $risultato;
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

        .btn-portal {
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
            text-decoration: none;
            color: white;
        }

        .btn-portal svg {
            width: 22px;
            height: 22px;
            fill: white;
            flex-shrink: 0;
        }

        .login-alert {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            display: flex;
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
                    <h2>Accesso non autorizzato</h2>
                    <p>Non è stato possibile completare l'accesso automatico.</p>
                </div>

                <!-- Messaggio di errore -->
                <?php if (isset($erroreLogin)): ?>
                    <div class="login-alert error">
                        <?php echo $erroreLogin; ?>
                    </div>
                <?php endif; ?>

                <div class="divider-line"></div>

                <!-- Link per riprovare -->
                <a href="<?php echo htmlspecialchars(PORTALE_AUTH_GOOGLE); ?>" class="btn-portal button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960">
                        <path d="M480-120v-80h280v-560H480v-80h280q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H480Zm-80-160-55-58 102-102H120v-80h327L345-622l55-58 200 200-200 200Z"/>
                    </svg>
                    Riprova con Portale Calvino
                </a>

                <!-- Aiuto -->
                <p class="login-help">
                    Se il problema persiste, contatta l'amministratore del
                    <a href="https://portale.calvino.edu.it" target="_blank">Portale Calvino</a>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
