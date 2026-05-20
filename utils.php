<?php
// funzione per formattare la data (trovata dall'ia per un bug di formattazione)
function formattaData($data) {
    if ($data === null || $data === '') return '';
    $d = strtotime($data);
    return $d ? date('d/m/Y', $d) : '';
}

// funzione per ottenere il nome dello stato
function nomeStato($id) {
    $nomi = [1 => 'Bozza', 2 => 'Approvata', 3 => 'Bocciata', 4 => 'Organizzazione', 5 => 'Conclusa'];
    return isset($nomi[$id]) ? $nomi[$id] : 'Sconosciuto';
}

// funzione per ottenere il nome del ruolo
function nomeRuolo($id) {
    return $id == 2 ? 'Commissione' : 'Docente';
}

/**
 * Genera il badge HTML per lo stato di una gita.
 * Per le gite con stato 4 (Organizzazione) controlla le date per capire se è "In Corso" o "In Programma".
 * @param int    $idStato      ID dello stato nel DB
 * @param string $giornoInizio Data inizio (o giorno per 1g) — formato Y-m-d
 * @param string $giornoFine   Data fine (opzionale, per gite 5g) — formato Y-m-d
 * @return string HTML del badge
 */
function badgeStatoHtml($idStato, $giornoInizio = null, $giornoFine = null) {
    $oggi = date('Y-m-d');

    if ($idStato == 5) {
        return '<span class="badge-stato badge-stato-conclusa">Conclusa</span>';
    }

    if ($idStato == 4) {
        // determina se la gita è "In Corso" (oggi è nel range delle date)
        $inCorso = false;
        if ($giornoFine && $giornoInizio) {
            // gita più giorni: in corso se oggi >= inizio && oggi <= fine
            $inCorso = ($oggi >= $giornoInizio && $oggi <= $giornoFine);
        } elseif ($giornoInizio) {
            // gita 1 giorno: in corso se oggi == giorno
            $inCorso = ($oggi === $giornoInizio);
        }

        if ($inCorso) {
            return '<span class="badge-stato badge-stato-incorso">In Corso</span>';
        }
        return '<span class="badge-stato badge-stato-programma">In Programma</span>';
    }

    if ($idStato == 1) return '<span class="badge-stato badge-stato-bozza">Bozza</span>';
    if ($idStato == 2) return '<span class="badge-stato badge-stato-approvata">Approvata</span>';
    if ($idStato == 3) return '<span class="badge-stato badge-stato-bocciata">Bocciata</span>';

    return '<span class="badge-stato badge-stato-conclusa">Sconosciuto</span>';
}
?>
