<?php
    session_start();
    require_once('config.php');

    $errore = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // validazione email e password
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errore = "Inserisci un indirizzo email valido.";
        } elseif ($password === '') {
            $errore = "Inserisci la password.";
        } else {
            $email = $conn->real_escape_string($email);
            $risultato = $conn->query("SELECT IDUtente, Nome, Cognome, Password, IDTipo FROM utente WHERE Mail = '$email'");

            if ($risultato && $risultato->num_rows > 0) {
                $riga = $risultato->fetch_assoc();
                if (password_verify($password, $riga['Password'])) {
                    $_SESSION['id_utente'] = $riga['IDUtente'];
                    $_SESSION['username']  = $riga['Nome'] . " " . $riga['Cognome'];
                    $_SESSION['ruolo']     = $riga['IDTipo'];
                    header("Location: index.php");
                    exit;
                } else {
                    $errore = "Password errata.";
                }
            } else {
                $errore = "Nessun account trovato con questa email.";
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
            align-items: flex-start;
            padding: 0.5rem 1rem;
            overflow-y: auto;
        }

        .login-container {
            width: min(100%, 460px);
            margin: auto;
        }

        .login-container .card {
            width: 100%;
            box-sizing: border-box;
            padding: 1.5rem !important;
            gap: 0.5rem !important;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .login-container .card-header {
            padding-bottom: 0.5rem !important;
        }

        .login-container .form-group {
            margin-bottom: 1.2rem !important;
        }

        .login-container .card-footer {
            padding-top: 0.25rem !important;
        }
    </style>
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="card centered">
                <div class="card-header">
                    <h2>Accedi</h2>
                    <p>Inserisci le tue credenziali per accedere</p>
                </div>
                
                <?php if($errore): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 0.9rem;">
                        <?php echo $errore; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" style="width: 100%;">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="mario.rossi@esempio.it" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div style="position: relative; display: flex; align-items: center; width: 100%;">
                            <input type="password" id="password" name="password" placeholder="********" required style="width: 100%; padding-right: 40px; box-sizing: border-box;">
                            <span id="togglePassword" style="position: absolute; right: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #666;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    <div class="checkbox-group" style="justify-content: flex-start; width: 100%; padding-left: 0.5rem;">
                        <input type="checkbox" id="remember">
                        <label for="remember">Ricordami</label>
                    </div>
                    
                    <button type="submit" class="m full-width">Accedi</button>
                    
                </form>
                
                <div class="card-footer" style="justify-content: center; border: none; padding-top: 0;">
                    <p style="font-size: 0.9rem;">Non hai un account? <a href="register.php" style="color: var(--my-blue); font-weight: bold;">Registrati</a></p>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function () {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    if (type === 'text') {
                        this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                    } else {
                        this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                    }
                });
            }
        });
    </script>
</body>
</html>
