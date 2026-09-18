{{-- Ubah tabel menjadi kartu bertumpuk di layar kecil (mobile). Tambahkan class "card-table" pada <table>. Label kolom otomatis diambil dari <th>. --}}
<style>
    @media (max-width: 767px) {
        table.card-table {
            display: block;
            width: 100%;
            border-collapse: collapse;
        }
        table.card-table thead {
            display: none;
        }
        table.card-table tbody {
            display: block;
            width: 100%;
        }
        table.card-table tr {
            display: block;
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.5rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        }
        table.card-table td {
            display: flex;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.45rem 0.25rem;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal;
            word-break: break-word;
        }
        table.card-table td:last-child {
            border-bottom: none;
        }
        table.card-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #64748b;
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            flex-shrink: 0;
            min-width: 40%;
            max-width: 45%;
            word-break: break-word;
        }

        /* Baris "tidak ada data" / sel dengan colspan: tampil penuh, tanpa label kolom */
        table.card-table td[colspan] {
            display: block;
            text-align: center;
            color: #94a3b8;
        }
        table.card-table td[colspan]::before {
            content: none;
        }
    }
</style>
<script>
    (function () {
        function applyCardLabels(table) {
            var headCells = [];
            var headerRow = table.querySelector('thead tr');
            if (headerRow) {
                headerRow.querySelectorAll('th').forEach(function (th) {
                    headCells.push(th.textContent.trim());
                });
            }
            table.querySelectorAll('tbody tr').forEach(function (tr) {
                var cells = tr.querySelectorAll('td');
                cells.forEach(function (td, i) {
                    // Sel yang merentang beberapa kolom (mis. "Tidak ada data")
                    // ditampilkan apa adanya, tanpa label kolom.
                    if (td.hasAttribute('colspan')) {
                        td.removeAttribute('data-label');
                        return;
                    }
                    var label = headCells[i] || '';
                    if (label) {
                        td.setAttribute('data-label', label);
                    } else if (!td.hasAttribute('data-label')) {
                        td.setAttribute('data-label', ' ');
                    }
                });
            });
            table.setAttribute('data-card-labels-applied', 'true');
        }

        function scanAllTables() {
            document.querySelectorAll('table.card-table').forEach(applyCardLabels);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', scanAllTables);
        } else {
            scanAllTables();
        }

        // Tabel yang dimuat belakangan (AJAX, Livewire, pagination, dsb.)
        // ikut diberi label secara otomatis.
        var observer = new MutationObserver(function (mutations) {
            var needsScan = false;
            for (var i = 0; i < mutations.length; i++) {
                if (mutations[i].addedNodes && mutations[i].addedNodes.length) {
                    needsScan = true;
                    break;
                }
            }
            if (needsScan) {
                scanAllTables();
            }
        });

        if (document.body) {
            observer.observe(document.body, { childList: true, subtree: true });
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                observer.observe(document.body, { childList: true, subtree: true });
            });
        }
    })();
</script>