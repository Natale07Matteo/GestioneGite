
<?php
// Funzione per formattare la data nel formato italiano (giorno/mese/anno)
function formattaData($data) {
    // Se la data è vuota o nulla, restituisce una stringa vuota
    if ($data === null) {
        return '';
    }
    if ($data === '') {
        return '';
    }
    
    // Converte la stringa in un formato di tempo (timestamp)
    $d = strtotime($data);
    
    // Se la conversione è andata a buon fine, formatta la data, altrimenti stringa vuota
    if ($d !== false) {
        return date('d/m/Y', $d);
    } else {
        return '';
    }
}

// Funzione per ottenere il nome testuale dello stato a partire dal suo ID
function nomeStato($id) {
    // Array associativo che mappa gli ID ai nomi testuali
    $nomi = array(
        1 => 'Bozza', 
        2 => 'Approvata', 
        3 => 'Bocciata', 
        4 => 'Organizzazione', 
        5 => 'Conclusa'
    );
    
    // Controlla se l'ID esiste nell'array
    if (isset($nomi[$id])) {
        return $nomi[$id];
    } else {
        return 'Sconosciuto';
    }
}

// Funzione per ottenere il nome del ruolo in base all'ID utente
function nomeRuolo($id) {
    // Il ruolo 2 corrisponde alla Commissione
    if ($id == 2) {
        return 'Commissione';
    } else {
        // Tutti gli altri casi (di solito 1) corrispondono al Docente
        return 'Docente';
    }
}

/**
 * Genera l'elemento HTML (badge) per mostrare lo stato di una gita in modo colorato.
 * Per le gite in fase di Organizzazione (stato 4), controlla le date per capire
 * se la gita è attualmente "In Corso" oppure è pianificata per il futuro ("In Programma").
 * 
 * @param int    $idStato      L'ID dello stato memorizzato nel database
 * @param string $giornoInizio La data di inizio (formato Anno-Mese-Giorno)
 * @param string $giornoFine   La data di fine (opzionale, solo per gite di più giorni)
 * @return string              Il codice HTML per visualizzare il badge
 */
function badgeStatoHtml($idStato, $giornoInizio = null, $giornoFine = null) {
    // Recupera la data di oggi nel formato Anno-Mese-Giorno
    $oggi = date('Y-m-d');

    // Se lo stato è 5 (Conclusa), restituisce il badge grigio
    if ($idStato == 5) {
        return '<span class="badge-stato badge-stato-conclusa">Conclusa</span>';
    }

    // Se lo stato è 4 (Organizzazione), dobbiamo controllare le date
    if ($idStato == 4) {
        $inCorso = false; // Variabile per tracciare se la gita si sta svolgendo oggi
        
        if ($giornoFine !== null && $giornoInizio !== null) {
            // Caso gita di più giorni: è in corso se oggi è compreso tra inizio e fine
            if ($oggi >= $giornoInizio && $oggi <= $giornoFine) {
                $inCorso = true;
            }
        } else {
            // Caso gita di 1 giorno: è in corso se oggi è esattamente il giorno di inizio
            if ($giornoInizio !== null) {
                if ($oggi === $giornoInizio) {
                    $inCorso = true;
                }
            }
        }

        // Se la gita è in corso, restituisce il badge verde acceso
        if ($inCorso == true) {
            return '<span class="badge-stato badge-stato-incorso">In Corso</span>';
        } else {
            // Altrimenti restituisce il badge blu (In Programma)
            return '<span class="badge-stato badge-stato-programma">In Programma</span>';
        }
    }

    // Altri stati
    if ($idStato == 1) {
        return '<span class="badge-stato badge-stato-bozza">Bozza</span>';
    }
    
    if ($idStato == 2) {
        return '<span class="badge-stato badge-stato-approvata">Approvata</span>';
    }
    
    if ($idStato == 3) {
        return '<span class="badge-stato badge-stato-bocciata">Bocciata</span>';
    }

    // Se nessun ID corrisponde, restituisce Sconosciuto
    return '<span class="badge-stato badge-stato-conclusa">Sconosciuto</span>';
}
?>
