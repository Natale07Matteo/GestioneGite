<?php
// ABILITAZIONE TEMPORANEA DEBUG 500
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionegite";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connessione fallita: " . mysqli_connect_error());
}

// Aggiornamento automatico gite concluse
$oggi = date('Y-m-d');
$conn->query("UPDATE gita1g SET idStato = 5 WHERE idStato = 4 AND giorno < '$oggi'");
$conn->query("UPDATE gite5  SET idStato = 5 WHERE idStato = 4 AND giornoFine < '$oggi'");
?>
