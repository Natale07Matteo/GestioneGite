<?php
    session_start();
    require_once('config.php');

    $errore = "";
    $successo = "";
    $errori_campi = [
        'nome' => '',
        'cognome' => '',
        'email' => '',
        'password' => '',
        'confirm-password' => ''
    ];

    $nome = "";
    $cognome = "";
    $email = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
        $cognome = trim(isset($_POST['cognome']) ? $_POST['cognome'] : '');
        $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $conferma_password = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';

        // validazione campi obbligatori
        if ($nome === '' || strlen($nome) > 50) {
            $errori_campi['nome'] = "Nome obbligatorio (max 50 caratteri).";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/', $nome)) {
            $errori_campi['nome'] = "Il nome può contenere solo lettere.";
        }

        if ($cognome === '' || strlen($cognome) > 50) {
            $errori_campi['cognome'] = "Cognome obbligatorio (max 50 caratteri).";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/', $cognome)) {
            $errori_campi['cognome'] = "Il cognome può contenere solo lettere.";
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errori_campi['email'] = "Inserisci un indirizzo email valido.";
        } elseif (!preg_match('/^[a-zA-Z]+\.[a-zA-Z]+@calvino\.edu\.it$/i', $email)) {
            $errori_campi['email'] = "Solo email scolastiche (nome.cognome@calvino.edu.it) sono ammesse.";
        }

        if ($password === '') {
            $errori_campi['password'] = "La password è obbligatoria.";
        } elseif (strlen($password) < 6) {
            $errori_campi['password'] = "La password deve contenere almeno 6 caratteri.";
        }

        if ($conferma_password === '') {
            $errori_campi['confirm-password'] = "Conferma la password.";
        } elseif ($password !== $conferma_password) {
            $errori_campi['confirm-password'] = "Le password non coincidono.";
        }

        // Procedi se non ci sono errori nei singoli campi
        if (empty(array_filter($errori_campi))) {
            $email = $conn->real_escape_string($email);
            
            // controllo email esistente
            $controllo = $conn->query("SELECT Mail FROM utente WHERE Mail = '$email'");

            if ($controllo && $controllo->num_rows > 0) {
                $errori_campi['email'] = "L'indirizzo email è già in uso.";
            } else {
                $nome = $conn->real_escape_string($nome);
                $cognome = $conn->real_escape_string($cognome);
                $psw_hash = password_hash($password, PASSWORD_DEFAULT);
                $tipo = 1;

                // inserimento nuovo utente
                $sql = "INSERT INTO utente (Nome, Cognome, Mail, Password, IDTipo) VALUES ('$nome', '$cognome', '$email', '$psw_hash', $tipo)";
                if ($conn->query($sql)) {
                    $successo = "Registrazione completata! Puoi ora accedere.";
                    $nome = $cognome = $email = ""; // Resetta i campi solo in caso di successo
                } else {
                    $errore = "Errore durante la registrazione.";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Registrati - Gestione Gite</title>
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

        .register-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 0.5rem 1rem;
            overflow-y: auto;
        }

        .register-container {
            width: min(100%, 460px);
            margin: auto;
        }

        .register-container .card {
            width: 100%;
            box-sizing: border-box;
            padding: 1.5rem !important;
            gap: 0.5rem !important;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .register-container .card-header {
            padding-bottom: 0.5rem !important;
            margin-bottom: 0.25rem !important;
        }

        .register-container .card-header h2 {
            margin-bottom: 0.25rem !important;
        }

        .register-container .form-group {
            margin-bottom: 1.2rem !important;
        }

        .register-container .card-footer {
            padding-top: 0.25rem !important;
            margin-top: 0.25rem !important;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="register-wrapper">
        <div class="register-container">
            <div class="card centered">
                <div class="card-header">
                    <h2>Crea un Account</h2>
                    <p>Inserisci i tuoi dati per registrarti</p>
                </div>
                
                <?php if($errore): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 0.9rem;">
                        <?php echo $errore; ?>
                    </div>
                <?php endif; ?>
                <?php if($successo): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 0.9rem;">
                        <?php echo $successo; ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" style="width: 100%;">
                    <div class="form-group" style="display: flex; gap: 0.5rem; flex-direction: row !important;">
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <label for="nome">Nome</label>
                            <input type="text" id="nome" name="nome" placeholder="Mario" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required class="<?php echo !empty($errori_campi['nome']) ? 'input-error' : ''; ?>">
                            <?php if (!empty($errori_campi['nome'])): ?>
                                <span class="field-error"><?php echo $errori_campi['nome']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column;">
                            <label for="cognome">Cognome</label>
                            <input type="text" id="cognome" name="cognome" placeholder="Rossi" value="<?php echo htmlspecialchars($cognome ?? ''); ?>" required class="<?php echo !empty($errori_campi['cognome']) ? 'input-error' : ''; ?>">
                            <?php if (!empty($errori_campi['cognome'])): ?>
                                <span class="field-error"><?php echo $errori_campi['cognome']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email scolastica</label>
                        <input type="email" id="email" name="email" placeholder="nome.cognome@calvino.edu.it" value="<?php echo htmlspecialchars($email ?? ''); ?>" required
                               pattern="[a-zA-Z]+\.[a-zA-Z]+@calvino\.edu\.it"
                               title="Usa la tua email scolastica: nome.cognome@calvino.edu.it" class="<?php echo !empty($errori_campi['email']) ? 'input-error' : ''; ?>">
                        <?php if (!empty($errori_campi['email'])): ?>
                            <span class="field-error"><?php echo $errori_campi['email']; ?></span>
                        <?php endif; ?>
                        <small style="color: var(--my-text-muted, #aaa); font-size: 0.78rem; margin-top: 0.2rem;">Solo email @calvino.edu.it sono ammesse</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div style="position: relative; display: flex; align-items: center; width: 100%;">
                            <input type="password" id="password" name="password" placeholder="********" required style="width: 100%; padding-right: 40px; box-sizing: border-box;" class="<?php echo !empty($errori_campi['password']) ? 'input-error' : ''; ?>">
                            <span id="togglePassword1" style="position: absolute; right: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #666;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                        </div>
                        <?php if (!empty($errori_campi['password'])): ?>
                            <span class="field-error"><?php echo $errori_campi['password']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Conferma Password</label>
                        <div style="position: relative; display: flex; align-items: center; width: 100%;">
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="********" required style="width: 100%; padding-right: 40px; box-sizing: border-box;" class="<?php echo !empty($errori_campi['confirm-password']) ? 'input-error' : ''; ?>">
                            <span id="togglePassword2" style="position: absolute; right: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #666;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                        </div>
                        <?php if (!empty($errori_campi['confirm-password'])): ?>
                            <span class="field-error"><?php echo $errori_campi['confirm-password']; ?></span>
                        <?php endif; ?>
                    </div>
                    

                    <button type="submit" class="m full-width">Registrati</button>

                </form>
                
                <div class="card-footer" style="justify-content: center; border: none; padding-top: 0;">
                    <p style="font-size: 0.9rem;">Hai già un account? <a href="login.php" style="color: var(--my-blue); font-weight: bold;">Accedi</a></p>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupToggle(toggleId, passwordId) {
                const toggleBtn = document.querySelector(toggleId);
                const passField = document.querySelector(passwordId);
                
                if (toggleBtn && passField) {
                    toggleBtn.addEventListener('click', function () {
                        const type = passField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passField.setAttribute('type', type);
                        
                        if (type === 'text') {
                            this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                        } else {
                            this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                        }
                    });
                }
            }

            setupToggle('#togglePassword1', '#password');
            setupToggle('#togglePassword2', '#confirm-password');
        });
    </script>
</body>
</html>
