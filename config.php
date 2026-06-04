<?php
// ABILITAZIONE TEMPORANEA DEBUG 500
// Queste righe servono per mostrare a schermo eventuali errori PHP
// È utile durante lo sviluppo, ma andrebbe disabilitato in produzione
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Parametri di connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionegite";

// Carica configurazione locale se presente (es. su VM di produzione)
// Questo permette di sovrascrivere i parametri qui sopra senza modificare questo file
if (file_exists(dirname(__FILE__) . '/config_local.php')) {
    include(dirname(__FILE__) . '/config_local.php');
}

// Creazione della connessione al database utilizzando le variabili definite
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Controllo se la connessione è fallita
if (!$conn) {
    // Interrompe l'esecuzione e mostra l'errore
    die("Connessione fallita: " . mysqli_connect_error());
}

// ------------------------------------------------------------------
// AGGIORNAMENTO AUTOMATICO GITE CONCLUSE
// ------------------------------------------------------------------
// Questo script viene eseguito ogni volta che config.php viene incluso (in ogni pagina)
// Controlla se ci sono gite in organizzazione (stato 4) la cui data è passata
// e in caso affermativo le sposta nello stato 5 (Conclusa).

// Recupera la data di oggi nel formato Anno-Mese-Giorno
$oggi = date('Y-m-d');

// Aggiorna le gite di 1 giorno
$query_aggiornamento_1g = "UPDATE gita1g SET idStato = 5 WHERE idStato = 4 AND giorno < '$oggi'";
$conn->query($query_aggiornamento_1g);

// Aggiorna le gite di 5 giorni
$query_aggiornamento_5g = "UPDATE gite5 SET idStato = 5 WHERE idStato = 4 AND giornoFine < '$oggi'";
$conn->query($query_aggiornamento_5g);
?>
