-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 28, 2026 alle 12:33
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestionegite`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `accompagnatori`
--

CREATE TABLE `accompagnatori` (
  `id` int(11) NOT NULL,
  `idgita` int(11) NOT NULL,
  `idutente` int(11) NOT NULL,
  `tipo_gita` enum('1g','5g') NOT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `nDocumento` varchar(50) DEFAULT NULL,
  `scadenza` date DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `accompagnatori`
--

INSERT INTO `accompagnatori` (`id`, `idgita`, `idutente`, `tipo_gita`, `documento`, `nDocumento`, `scadenza`, `note`) VALUES
(2, 6, 2, '5g', 'Carta d\'identita', 'rfyrtuuhr', '2026-04-25', 'tfjtdj'),
(3, 6, 1, '5g', NULL, NULL, NULL, NULL),
(4, 8, 2, '1g', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `gita1g`
--

CREATE TABLE `gita1g` (
  `idGita` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `destinazione` varchar(255) NOT NULL,
  `descrizione` varchar(50) DEFAULT NULL,
  `mezzo` varchar(100) DEFAULT NULL,
  `periodo` varchar(100) DEFAULT NULL,
  `giorno` date DEFAULT NULL,
  `costoMezzo` decimal(10,2) DEFAULT NULL,
  `costoAttivita` decimal(10,2) DEFAULT NULL,
  `costoAPersona` decimal(10,2) DEFAULT NULL,
  `numAlunni` int(11) DEFAULT NULL,
  `classi` varchar(20) DEFAULT NULL,
  `idStato` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `gita1g`
--

INSERT INTO `gita1g` (`idGita`, `idUtente`, `destinazione`, `descrizione`, `mezzo`, `periodo`, `giorno`, `costoMezzo`, `costoAttivita`, `costoAPersona`, `numAlunni`, `classi`, `idStato`) VALUES
(2, 1, 'Roma - Musei Vaticani', NULL, 'treno', 'Novembre 2025', NULL, NULL, NULL, 13.00, NULL, NULL, 2),
(5, 2, 'porto antico', NULL, 'autobus', 'gnnaio', NULL, NULL, NULL, 25.00, NULL, NULL, 2),
(8, 1, 'porto antico', '', 'autobus', 'gnnaio', NULL, NULL, NULL, 25.00, NULL, '', 4),
(9, 1, 'Roma - Musei Vaticani', '', 'Bus', 'Novembre 2025', NULL, NULL, NULL, 13.00, NULL, '', 4),
(10, 1, 'Roma - Musei Vaticani', 'vedere colosseo', 'Treno', 'd', NULL, NULL, NULL, 12.00, NULL, 'classi 4', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `gite5`
--

CREATE TABLE `gite5` (
  `idGita` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `destinazione` varchar(255) NOT NULL,
  `descrizione` varchar(50) DEFAULT NULL,
  `mezzo` varchar(100) DEFAULT NULL,
  `periodo` varchar(100) DEFAULT NULL,
  `giornoInizio` date DEFAULT NULL,
  `giornoFine` date DEFAULT NULL,
  `costoAPersona` decimal(10,2) DEFAULT NULL,
  `numAlunni` int(11) DEFAULT NULL,
  `classi` varchar(20) DEFAULT NULL,
  `idStato` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `gite5`
--

INSERT INTO `gite5` (`idGita`, `idUtente`, `destinazione`, `descrizione`, `mezzo`, `periodo`, `giornoInizio`, `giornoFine`, `costoAPersona`, `numAlunni`, `classi`, `idStato`) VALUES
(1, 1, 'praga', NULL, 'aereo', 'Maggio', NULL, NULL, 345.00, NULL, NULL, 3),
(2, 1, 'praga', NULL, 'aereo', 'Maggio', NULL, NULL, 345.00, NULL, NULL, 2),
(4, 1, 'Genova', NULL, 'treno', 'aprile', NULL, NULL, 12.00, NULL, NULL, 2),
(5, 1, 'Roma - Musei Vaticani', NULL, 'aereo', 'Maggio', NULL, NULL, 12.00, NULL, NULL, 2),
(6, 1, 'Roma - Musei Vaticani', NULL, 'aereo', 'Maggio', NULL, NULL, 12.00, NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `partecipanti`
--

CREATE TABLE `partecipanti` (
  `id` int(11) NOT NULL,
  `idgita` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `classe` varchar(10) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `documento` varchar(50) DEFAULT NULL,
  `nDocumento` varchar(50) DEFAULT NULL,
  `scadenza` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `partecipanti`
--

INSERT INTO `partecipanti` (`id`, `idgita`, `nome`, `cognome`, `classe`, `descrizione`, `documento`, `nDocumento`, `scadenza`) VALUES
(4, 6, 'sofia', 'Molly', '5BII', '', 'Carta d\'identità', 'sdfsd', NULL),
(5, 6, 'sofia', 'Molly', '5DIT', '', 'Passaporto', 'sdfsd', '2026-04-07');

-- --------------------------------------------------------

--
-- Struttura della tabella `statogita`
--

CREATE TABLE `statogita` (
  `IDStato` int(11) NOT NULL,
  `Stato` enum('Bozza','Approvata','Bocciata','Organizzazione','Conclusa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `statogita`
--

INSERT INTO `statogita` (`IDStato`, `Stato`) VALUES
(1, 'Bozza'),
(2, 'Approvata'),
(3, 'Bocciata'),
(4, 'Organizzazione'),
(5, 'Conclusa');

-- --------------------------------------------------------

--
-- Struttura della tabella `tipoutente`
--

CREATE TABLE `tipoutente` (
  `IDTipo` int(11) NOT NULL,
  `Descrizione` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tipoutente`
--

INSERT INTO `tipoutente` (`IDTipo`, `Descrizione`) VALUES
(1, 'Docente'),
(2, 'Commissione');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `IDUtente` int(11) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Mail` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IDTipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`IDUtente`, `Nome`, `Cognome`, `Mail`, `Password`, `IDTipo`) VALUES
(1, 'elisa', 'stanizzi', 'elisa.stanizzi.2007@calvino.edu.it', '$2y$10$PZt7aoV4cgla70ditsreVeNCs0gjmjVqSgd49VS5/6PUdyqs75nc.', 2),
(2, 'a', 'a', 'ale@ete.it', '$2y$10$7AWk7Gv9O3zhZMBR1JDflO0EsUcuCj5XFJaIZCul6h0S71bsUkFYq', 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `accompagnatori`
--
ALTER TABLE `accompagnatori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_acc` (`idgita`,`idutente`,`tipo_gita`);

--
-- Indici per le tabelle `gita1g`
--
ALTER TABLE `gita1g`
  ADD PRIMARY KEY (`idGita`),
  ADD KEY `fk_gita1g_utente` (`idUtente`),
  ADD KEY `fk_gita1g_stato` (`idStato`);

--
-- Indici per le tabelle `gite5`
--
ALTER TABLE `gite5`
  ADD PRIMARY KEY (`idGita`),
  ADD KEY `fk_gite5_utente` (`idUtente`),
  ADD KEY `fk_gite5_stato` (`idStato`);

--
-- Indici per le tabelle `partecipanti`
--
ALTER TABLE `partecipanti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_partecipanti_gite5` (`idgita`);

--
-- Indici per le tabelle `statogita`
--
ALTER TABLE `statogita`
  ADD PRIMARY KEY (`IDStato`);

--
-- Indici per le tabelle `tipoutente`
--
ALTER TABLE `tipoutente`
  ADD PRIMARY KEY (`IDTipo`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`IDUtente`),
  ADD UNIQUE KEY `Mail` (`Mail`),
  ADD KEY `IDTipo` (`IDTipo`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `accompagnatori`
--
ALTER TABLE `accompagnatori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `gita1g`
--
ALTER TABLE `gita1g`
  MODIFY `idGita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `gite5`
--
ALTER TABLE `gite5`
  MODIFY `idGita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `partecipanti`
--
ALTER TABLE `partecipanti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `statogita`
--
ALTER TABLE `statogita`
  MODIFY `IDStato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `tipoutente`
--
ALTER TABLE `tipoutente`
  MODIFY `IDTipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `IDUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `gita1g`
--
ALTER TABLE `gita1g`
  ADD CONSTRAINT `fk_gita1g_stato` FOREIGN KEY (`idStato`) REFERENCES `statogita` (`IDStato`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gita1g_utente` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`IDUtente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `gite5`
--
ALTER TABLE `gite5`
  ADD CONSTRAINT `fk_gite5_stato` FOREIGN KEY (`idStato`) REFERENCES `statogita` (`IDStato`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gite5_utente` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`IDUtente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `partecipanti`
--
ALTER TABLE `partecipanti`
  ADD CONSTRAINT `fk_partecipanti_gite5` FOREIGN KEY (`idgita`) REFERENCES `gite5` (`idGita`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `utente`
--
ALTER TABLE `utente`
  ADD CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`IDTipo`) REFERENCES `tipoutente` (`IDTipo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
