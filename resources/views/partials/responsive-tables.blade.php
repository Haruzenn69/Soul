{{-- Ubah tabel menjadi kartu bertumpuk di layar kecil (mobile). Tambahkan class "card-table" pada <table>. Label kolom otomatis diambil dari <th>. --}}
<style>
    @media (max-width: 767px) {
        table.card-table {
            display: block;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            min-width: 0;
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
            border: 1px solid #dbeafe;
            border-radius: 1rem;
            padding: 0.55rem 0.7rem;
            margin-bottom: 0.8rem;
            box-shadow: 0 3px 10px rgba(14, 165, 233, 0.08);
        }
        table.card-table td {
            display: flex;
            width: 100%;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.45rem 0.25rem;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            color: #334155;
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
        table.card-table td > * {
            min-width: 0;
            max-width: 60%;
            text-align: right;
        }
        table.card-table td:last-child > * {
            max-width: 100%;
        }
        table.card-table td:last-child {
            display: block;
        }
        table.card-table td:last-child::before {
            display: block;
            margin-bottom: 0.35rem;
        }
        table.card-table td:last-child .flex {
            width: 100%;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: flex-start;
        }
        table.card-table td:last-child .flex > * {
            text-align: left;
        }
        table.card-table td:last-child form,
        table.card-table td:last-child a,
        table.card-table td:last-child button {
            max-width: 100%;
        }
        table.card-table td[colspan] {
            display: block;
            text-align: center;
        }
        table.card-table td[colspan]::before {
            display: none;
        }

        .ketua-layout table.card-table td {
            display: block;
            padding: 0.65rem 0.25rem;
            text-align: left;
        }
        .ketua-layout table.card-table td::before {
            display: block;
            min-width: 0;
            max-width: none;
            margin-bottom: 0.25rem;
            font-size: 0.625rem;
            letter-spacing: 0.08em;
            color: #64748b;
        }
        .ketua-layout table.card-table td > * {
            display: inline-flex;
            max-width: 100%;
            text-align: left;
            vertical-align: top;
        }
        .ketua-layout table.card-table td > :not(.flex) {
            overflow-wrap: anywhere;
        }
        .ketua-layout table.card-table td:last-child {
            padding-top: 0.8rem;
            margin-top: 0.15rem;
            border-top: 1px solid #f1f5f9;
        }
        .ketua-layout table.card-table td:last-child::before {
            margin-bottom: 0.5rem;
        }
        .ketua-layout table.card-table td:last-child .flex {
            align-items: stretch;
        }
        .ketua-layout table.card-table td:last-child form,
        .ketua-layout table.card-table td:last-child a,
        .ketua-layout table.card-table td:last-child button {
            width: auto;
        }
        .ketua-layout table.card-table td[colspan] {
            padding: 1.25rem 0.5rem;
            text-align: center;
        }

        .ketua-layout .ketua-card-list {
            background: transparent;
            border: 0;
            box-shadow: none;
            overflow: visible;
        }
        .ketua-layout .ketua-card-list > .overflow-x-auto {
            overflow: visible;
        }
        .ketua-layout .ketua-card-list table.card-table tbody.divide-y > :not([hidden]) ~ :not([hidden]) {
            border-top-width: 0;
        }
        .ketua-layout .ketua-card-footer {
            display: none;
        }

        .ketua-layout table.card-table tr[data-card-href] {
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .ketua-layout table.card-table tr[data-card-href]:active {
            transform: scale(.99);
        }
        .ketua-layout table.card-table tr[data-card-href] .card-detail-link {
            display: none;
        }
        .ketua-layout table.card-table tr[data-card-href] td::before {
            display: none;
        }
        .ketua-layout table.card-table tr[data-card-href] td {
            border: 0;
        }

        .ketua-layout table.ketua-card-pendaftaran tr,
        .ketua-layout table.ketua-card-pengajuan tr,
        .ketua-layout table.ketua-card-laporan tr {
            display: grid;
            gap: 0;
        }
        .ketua-layout table.ketua-card-pendaftaran tr {
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "title status"
                "subtitle status"
                "content content"
                "date date";
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(1),
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(7) {
            display: none;
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(3) {
            grid-area: title;
            padding: .15rem 0 0;
            font-size: .9rem;
            font-weight: 800;
            color: #0f172a;
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(2) {
            grid-area: subtitle;
            padding: .1rem 0 .65rem;
            font-size: .7rem;
            color: #64748b;
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(4),
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(5) {
            grid-area: content;
            padding: .55rem 0;
            border-top: 1px solid #f1f5f9;
            font-size: .75rem;
            color: #475569;
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(5) {
            grid-area: date;
            padding-top: .55rem;
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(6) {
            grid-area: status;
            align-self: start;
            justify-self: end;
            padding: .05rem 0 0 .5rem;
        }
        .ketua-layout table.ketua-card-pendaftaran td:nth-child(6) > * {
            margin: 0;
        }

        .ketua-layout table.ketua-card-pengajuan tr {
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "title status"
                "content status"
                "action action";
        }
        .ketua-layout table.ketua-card-pengajuan td:nth-child(1) {
            display: none;
        }
        .ketua-layout table.ketua-card-pengajuan td:nth-child(2) {
            grid-area: title;
            padding: .15rem 0 0;
            font-size: .9rem;
            font-weight: 800;
            color: #0f172a;
        }
        .ketua-layout table.ketua-card-pengajuan td:nth-child(3),
        .ketua-layout table.ketua-card-pengajuan td:nth-child(4) {
            grid-area: content;
            padding: .4rem 0 .6rem;
            font-size: .75rem;
            color: #64748b;
        }
        .ketua-layout table.ketua-card-pengajuan td:nth-child(4) {
            padding-top: 0;
        }
        .ketua-layout table.ketua-card-pengajuan td:nth-child(5) {
            grid-area: status;
            align-self: start;
            justify-self: end;
            padding: .05rem 0 0 .5rem;
        }
        .ketua-layout table.ketua-card-pengajuan td:nth-child(6) {
            grid-area: action;
            padding: .65rem 0 0;
            border-top: 1px solid #f1f5f9;
        }

        .ketua-layout table.ketua-card-laporan tr {
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "title status"
                "content status"
                "action action";
        }
        .ketua-layout table.ketua-card-laporan td:nth-child(1) {
            display: none;
        }
        .ketua-layout table.ketua-card-laporan td:nth-child(2) {
            grid-area: title;
            padding: .15rem 0 0;
            font-size: .9rem;
            font-weight: 800;
            color: #0f172a;
        }
        .ketua-layout table.ketua-card-laporan td:nth-child(3) {
            grid-area: content;
            padding: .4rem 0 .6rem;
            font-size: .75rem;
            color: #64748b;
        }
        .ketua-layout table.ketua-card-laporan td:nth-child(4) {
            grid-area: status;
            align-self: start;
            justify-self: end;
            padding: .05rem 0 0 .5rem;
        }
        .ketua-layout table.ketua-card-laporan td:nth-child(5) {
            grid-area: action;
            padding: .65rem 0 0;
            border-top: 1px solid #f1f5f9;
        }

        .ketua-layout table.ketua-card-kegiatan tr {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "title attendance"
                "meta attendance"
                "actions actions";
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(1) {
            display: none;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(2) {
            grid-area: meta;
            padding: .1rem 0 .65rem;
            font-size: .7rem;
            color: #64748b;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(3) {
            display: none;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(4) {
            grid-area: title;
            padding: .15rem 0 0;
            font-size: .9rem;
            font-weight: 800;
            color: #0f172a;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(5) {
            grid-area: attendance;
            align-self: start;
            justify-self: end;
            padding: .05rem 0 0 .5rem;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(6) {
            grid-area: actions;
            padding: .65rem 0 0;
            border-top: 1px solid #f1f5f9;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(6) .flex {
            gap: .4rem;
        }
        .ketua-layout table.ketua-card-kegiatan td:nth-child(6) .card-detail-link {
            display: none;
        }

        .ketua-layout table.ketua-card-anggota tr {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "title status"
                "subtitle status"
                "content content"
                "actions actions";
        }
        .ketua-layout table.ketua-card-anggota td {
            border-bottom: 0;
        }
        .ketua-layout table.ketua-card-anggota td:nth-child(1) {
            display: none;
        }
        .ketua-layout table.ketua-card-anggota td:nth-child(2) {
            grid-area: subtitle;
            padding: .1rem 0 .65rem;
            font-size: .7rem;
            color: #64748b;
        }
        .ketua-layout table.ketua-card-anggota td:nth-child(3) {
            grid-area: title;
            padding: .15rem 0 0;
            font-size: .9rem;
            font-weight: 800;
            color: #0f172a;
        }
        .ketua-layout table.ketua-card-anggota td:nth-child(4) {
            grid-area: content;
            padding: .55rem 0;
            border-top: 1px solid #f1f5f9;
            font-size: .75rem;
            color: #475569;
        }
        .ketua-layout table.ketua-card-anggota td:nth-child(5) {
            grid-area: status;
            align-self: start;
            justify-self: end;
            padding: .05rem 0 0 .5rem;
        }
        .ketua-layout table.ketua-card-anggota td:nth-child(6) {
            grid-area: actions;
            padding: .65rem 0 0;
            border-top: 1px solid #f1f5f9;
        }
    }
</style>
<script>
    (function () {
        function applyCardLabels() {
            document.querySelectorAll('table.card-table').forEach(function (table) {
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
                        var label = headCells[i] || '';
                        if (label) {
                            td.setAttribute('data-label', label);
                        } else if (!td.hasAttribute('data-label')) {
                            td.setAttribute('data-label', ' ');
                        }
                    });
                });
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyCardLabels);
        } else {
            applyCardLabels();
        }

        document.addEventListener('click', function (event) {
            if (!window.matchMedia('(max-width: 767px)').matches) return;
            var row = event.target.closest('tr[data-card-href]');
            if (!row || event.target.closest('a, button, form, input, select, textarea')) return;
            window.location.href = row.getAttribute('data-card-href');
        });
    })();
</script>
