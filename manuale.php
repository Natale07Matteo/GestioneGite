<?php
include('nav.php');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manuale d'Uso - Gestione Gite</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vetrina.css">
    <link rel="stylesheet" href="style_custom.css">
    <script src="vetrina.js" defer></script>
    <style>
        .manuale-container {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 2rem;
            box-sizing: border-box;
        }

        .manuale-sidebar {
            position: sticky;
            top: 6.5rem;
            height: fit-content;
            background: var(--my-white);
            border: 1px solid var(--blue-100);
            border-radius: var(--radius-1);
            padding: 1.2rem;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.04);
        }

        .sidebar-titolo {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--blue-400);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.8rem;
            border-bottom: 1px solid var(--blue-100);
            padding-bottom: 0.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .sidebar-link {
            display: block;
            padding: 0.5rem 0.6rem;
            color: var(--blue-800);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background: var(--blue-50);
            color: var(--blue-700);
            transform: translateX(3px);
        }

        .manuale-content {
            background: var(--my-white);
            border: 1px solid var(--blue-100);
            border-radius: var(--radius-1);
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.04);
            min-width: 0;
            overflow-x: hidden;
        }

        .manuale-content h1 {
            color: var(--blue-700);
            font-size: 1.7rem;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .manuale-content h2 {
            color: var(--blue-700);
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--blue-50);
            padding-bottom: 0.4rem;
            scroll-margin-top: 6.5rem;
        }

        .manuale-content h3 {
            color: var(--blue-600);
            font-size: 1.05rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .manuale-content p,
        .manuale-content li {
            font-size: 0.95rem;
            line-height: 1.65;
            color: var(--blue-900);
        }

        .manuale-content p {
            margin-bottom: 1rem;
        }

        .manuale-content ul,
        .manuale-content ol {
            padding-left: 1.4rem;
            margin-bottom: 1rem;
        }

        .manuale-content li {
            margin-bottom: 0.4rem;
        }

        .manuale-alert {
            padding: 1rem 1.2rem;
            border-radius: var(--radius-1);
            margin: 1.5rem 0;
            font-size: 0.9rem;
            line-height: 1.55;
            display: flex;
            gap: 0.8rem;
            align-items: flex-start;
        }

        .manuale-alert-tip {
            background: #eff6ff;
            border-left: 4px solid var(--blue-500);
            color: var(--blue-900);
        }

        .manuale-alert-important {
            background: #fffbeb;
            border-left: 4px solid #d97706;
            color: #78350f;
        }

        .manuale-alert svg {
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        @media (max-width: 820px) {
            .manuale-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .manuale-sidebar {
                position: relative;
                top: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
<main class="content bozze-padding">

    <div class="manuale-container">
        <!-- Sidebar -->
        <aside class="manuale-sidebar">
            <div class="sidebar-titolo">Indice</div>
            <ul class="sidebar-menu">
                <li><a href="#accesso" class="sidebar-link">1. Accesso al Sistema</a></li>
                <li><a href="#creazione" class="sidebar-link">2. Creazione di una Gita</a></li>
                <li><a href="#organizzazione" class="sidebar-link">3. Organizzazione di una Gita</a></li>
                <li><a href="#partecipazione" class="sidebar-link">4. Partecipazione a una Gita</a></li>
                <li><a href="#strumenti" class="sidebar-link">5. Strumenti di Navigazione</a></li>
            </ul>
        </aside>

        <!-- Contenuto Principale -->
        <article class="manuale-content">
            <h1>Manuale d'Uso – Portale Gestione Gite Scolastiche</h1>

            <!-- 1. ACCESSO -->
            <section>
                <h2 id="accesso">1. Accesso al Sistema</h2>

                <h3>Registrazione</h3>
                <p>Per accedere alla piattaforma è necessario effettuare la registrazione inserendo le proprie credenziali istituzionali.</p>

                <div class="manuale-alert manuale-alert-tip">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="var(--blue-600)">
                        <path d="M480-120q-33 0-56.5-23.5T400-200q0-33 23.5-56.5T480-280q33 0 56.5 23.5T560-200q0 33-23.5 56.5T480-120Zm-80-240v-480h160v480H400Z"/>
                    </svg>
                    <div>
                        <strong>Validazione dei campi:</strong> In caso di errore durante la compilazione (es. email non valida o password non corrispondenti), i campi <em>Nome</em>, <em>Cognome</em> ed <em>Email</em> rimarranno compilati. Verrà evidenziato solo il campo contenente l'errore, con un messaggio esplicativo posizionato subito sotto.
                    </div>
                </div>

                <h3>Login</h3>
                <p>Dopo la registrazione, accedere al sistema tramite le credenziali personali.</p>
            </section>

            <!-- 2. CREAZIONE -->
            <section>
                <h2 id="creazione">2. Creazione di una Gita</h2>

                <h3>Fase 1 – Proposta della gita</h3>
                <ol>
                    <li>Accedere alla sezione <strong>"Proposte"</strong> dal menu principale.</li>
                    <li>Selezionare <strong>"+ Nuova Proposta"</strong>.</li>
                    <li>Compilare tutti i campi richiesti con le informazioni della gita.</li>
                    <li>Al termine, la proposta verrà salvata automaticamente come bozza nella sezione <strong>"Le mie gite"</strong>.</li>
                </ol>

                <h3>Fase 2 – Approvazione della proposta</h3>
                <p>Una volta inviata la proposta, un docente membro della Commissione Gite provvederà alla valutazione:</p>
                <ul>
                    <li><strong>In caso di approvazione:</strong> la gita diventerà visibile nella pagina pubblica <strong>"Proposte"</strong> e sarà disponibile per l'organizzazione.</li>
                    <li><strong>In caso di bocciatura:</strong> la gita tornerà nella sezione <strong>"Le mie gite"</strong> come bozza, permettendo di apportare le modifiche necessarie e ripresentarla.</li>
                </ul>
            </section>

            <!-- 3. ORGANIZZAZIONE -->
            <section>
                <h2 id="organizzazione">3. Organizzazione di una Gita</h2>
                <p>Dalla pagina <strong>"Proposte"</strong>, ogni docente può scegliere una gita approvata e procedere all'organizzazione.</p>

                <h3>Procedura</h3>
                <ol>
                    <li>Accedere alla sezione <strong>"Proposte"</strong>.</li>
                    <li>Individuare la gita desiderata e cliccare sul pulsante <strong>"Organizza"</strong>.</li>
                    <li>Inserire i dettagli operativi e logistici specifici (orari, mezzi, costi, ecc.).</li>
                    <li>La gita verrà trasferita nella sezione <strong>"Le mie gite"</strong>.</li>
                </ol>

                <h3>Gestione successiva</h3>
                <ul>
                    <li><strong>Modifica:</strong> permette di aggiornare i dati della gita in qualsiasi momento.</li>
                    <li><strong>Partecipa:</strong> consente al docente di iscriversi come accompagnatore della gita.</li>
                    <li><strong>Partecipanti</strong> (disponibile solo per le gite di quinto anno): permette di inserire manualmente i dati di tutti gli alunni e accompagnatori previsti.</li>
                </ul>
            </section>

            <!-- 4. PARTECIPAZIONE -->
            <section>
                <h2 id="partecipazione">4. Partecipazione a una Gita (per i Docenti)</h2>
                <ol>
                    <li>Accedere alla sezione <strong>"In Programma"</strong>.</li>
                    <li>Individuare la gita a cui si desidera partecipare.</li>
                    <li>Cliccare sul pulsante <strong>"Partecipa"</strong>.</li>
                </ol>
                <p>La gita verrà aggiunta alla sezione <strong>"Le mie gite"</strong>, dove sarà possibile gestire la propria iscrizione e, in caso di necessità, procedere alla disiscrizione.</p>
            </section>

            <!-- 5. STRUMENTI DI NAVIGAZIONE -->
            <section>
                <h2 id="strumenti">5. Strumenti di Navigazione</h2>
                <p>In tutte le pagine che contengono tabelle (<strong>Proposte</strong>, <strong>In Programma</strong>, <strong>Bozze</strong>) sono disponibili i seguenti strumenti per agevolare la consultazione:</p>
                <ul>
                    <li><strong>Ricerca istantanea:</strong> la barra di ricerca in alto consente di filtrare le righe delle tabelle in tempo reale digitando una destinazione, un mezzo di trasporto, un periodo o il nome di un docente.</li>
                    <li><strong>"Vai a gite per le quinte":</strong> il pulsante posizionato accanto alla barra di ricerca permette di scorrere rapidamente fino alla tabella delle gite di più giorni, evitando di dover navigare manualmente l'intera pagina.</li>
                </ul>

                <div class="manuale-alert manuale-alert-important">
                    <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20" fill="#d97706">
                        <path d="M480-120q-33 0-56.5-23.5T400-200q0-33 23.5-56.5T480-280q33 0 56.5 23.5T560-200q0 33-23.5 56.5T480-120Zm-80-240v-480h160v480H400Z"/>
                    </svg>
                    <div>
                        <strong>Nota:</strong> La sezione <strong>"Bozze"</strong> nel menu di navigazione è visibile esclusivamente ai docenti con ruolo di <strong>Commissione</strong>. I docenti con ruolo standard non dispongono dell'accesso a questa area.
                    </div>
                </div>
            </section>
        </article>
    </div>

</main>

<footer>
    <div class="footer-container">
        <div class="footer-left">
            <p><strong>Gestione Gite Scolastiche</strong> &copy; 2026</p>
        </div>
    </div>
</footer>
</div>
</body>
</html>
