<?php
/**
 * Configurazione ruoli autorizzati — Gestione Gite Scolastiche
 *
 * Mappa i nomi dei ruoli restituiti dal Portale Calvino
 * agli IDTipo della tabella `tipoutente` nel DB locale.
 *
 * L'array dei ruoli dell'utente dal portale viene scansionato
 * in ordine: il PRIMO ruolo che corrisponde a una chiave di
 * questa mappa determina l'IDTipo assegnato.
 *
 * Per aggiungere un nuovo ruolo autorizzato basta aggiungere
 * una riga a questo array.
 *
 * IDTipo 1 = Docente
 * IDTipo 2 = Commissione
 */

$PORTAL_ROLES = [
    'docente' => 1,
    // Quando sarà creato il ruolo commissione sul portale, decommentare:
    // 'commissione' => 2,
];
