function toggleProfile(e) {
    if (e) e.stopPropagation();
    var freccia = document.querySelector(".profile-arrow");
    var menu = document.querySelector(".profile-modal");
    if (!freccia || !menu) return;

    if (freccia.classList.contains("open")) {
        freccia.classList.remove("open");
        menu.classList.add("hidden");
    } else {
        freccia.classList.add("open");
        menu.classList.remove("hidden");
    }
}

document.addEventListener("click", function(e) {
    var contenitore = document.querySelector(".profile-container");
    var menu = document.querySelector(".profile-modal");
    var freccia = document.querySelector(".profile-arrow");
    if (!contenitore || !menu) return;
    if (!contenitore.contains(e.target)) {
        menu.classList.add("hidden");
        if (freccia) freccia.classList.remove("open");
    }
});

function openModal(idModale) {
    var modale = document.getElementById(idModale);
    if (modale) {
        modale.classList.remove("hidden");
        document.body.style.overflow = "hidden";
    }
}

function closeModal(idModale) {
    var modale = document.getElementById(idModale);
    if (modale) {
        modale.classList.add("hidden");
        document.body.style.overflow = "";
    }
}

window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.add('hidden');
        document.body.style.overflow = "";
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var bottoniChiudi = document.querySelectorAll('.close-btn');
    for (var i = 0; i < bottoniChiudi.length; i++) {
        bottoniChiudi[i].addEventListener('click', function(e) {
            var modale = e.target.closest('.modal-overlay');
            if (modale) {
                modale.classList.add('hidden');
                document.body.style.overflow = "";
            }
        });
    }

    // Gestione automatica blocco scorrimento dello sfondo quando un modale è aperto
    function aggiornaBloccoScorrimento() {
        var modaliVisibili = document.querySelectorAll('.modal-overlay:not(.hidden)');
        if (modaliVisibili.length > 0) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
        }
    }

    // Controllo iniziale al caricamento della pagina
    aggiornaBloccoScorrimento();

    // Creazione del MutationObserver per monitorare i cambiamenti della classe "hidden"
    var observerModali = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                aggiornaBloccoScorrimento();
            }
        });
    });

    // Avvio dell'osservazione su tutti i modali presenti nella pagina
    document.querySelectorAll('.modal-overlay').forEach(function(modale) {
        observerModali.observe(modale, { attributes: true, attributeFilter: ['class'] });
    });
});

// Funzione di ricerca istantanea premium in tempo reale per le tabelle
function cercaInTabelle(inputId, tableSelector) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const filter = input.value.toLowerCase();
    
    // Seleziona tutte le tabelle corrispondenti
    const tables = document.querySelectorAll(tableSelector);
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Ignora le righe che rappresentano messaggi di vuoto o tabelle vuote all'inizio
            if (row.cells.length === 1 && row.cells[0].getAttribute('colspan')) {
                // Se è la nostra riga custom "nessun risultato", non contarla
                if (row.classList.contains('no-results-row')) return;
                // Altrimenti, se è la riga nativa "nessuna gita...", gestisci visibilità
                row.style.display = filter === '' ? '' : 'none';
                return;
            }
            
            let match = false;
            // Cerca in tutte le celle tranne l'ultima colonna che contiene le azioni/bottoni
            const cellsToSearch = row.cells.length > 1 ? row.cells.length - 1 : row.cells.length;
            for (let i = 0; i < cellsToSearch; i++) {
                const cellText = row.cells[i].textContent || row.cells[i].innerText;
                if (cellText.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
            
            if (match) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Gestione del messaggio "Nessun risultato trovato"
        let emptyRow = table.querySelector('.no-results-row');
        if (visibleCount === 0 && filter !== '') {
            if (!emptyRow) {
                const colsCount = table.querySelectorAll('thead th').length || 8;
                emptyRow = document.createElement('tr');
                emptyRow.className = 'no-results-row';
                emptyRow.innerHTML = `<td colspan="${colsCount}" style="text-align:center;color:#94a3b8;padding: 1.5rem 1rem;">Nessun risultato trovato per "${input.value}"</td>`;
                table.querySelector('tbody').appendChild(emptyRow);
            } else {
                emptyRow.style.display = '';
                emptyRow.querySelector('td').textContent = `Nessun risultato trovato per "${input.value}"`;
            }
            
            // Nascondi anche l'eventuale messaggio nativo di "nessun elemento" per evitare sovrapposizioni
            const nativeEmpty = Array.from(rows).find(r => r.cells.length === 1 && r.cells[0].getAttribute('colspan') && !r.classList.contains('no-results-row'));
            if (nativeEmpty) nativeEmpty.style.display = 'none';
        } else {
            if (emptyRow) emptyRow.style.display = 'none';
            // Se la ricerca è vuota, ripristina la riga nativa di "nessun elemento" se le altre righe non ci sono
            const nativeEmpty = Array.from(rows).find(r => r.cells.length === 1 && r.cells[0].getAttribute('colspan') && !r.classList.contains('no-results-row'));
            const actualRows = Array.from(rows).filter(r => !(r.cells.length === 1 && r.cells[0].getAttribute('colspan')));
            if (nativeEmpty && actualRows.length === 0) {
                nativeEmpty.style.display = '';
            }
        }
    });
}
