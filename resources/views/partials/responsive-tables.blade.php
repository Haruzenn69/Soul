{{-- Semua tabel (class "card-table") selalu tampil sebagai tabel di semua ukuran layar. Scroll horizontal disediakan oleh wrapper .overflow-x-auto. --}}
<style>
    .responsive-table-scroll {
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 767px) {
        table.card-table {
            display: table;
            width: 100%;
        }
    }
</style>
<script>
    (function () {
        document.addEventListener('click', function (event) {
            if (!window.matchMedia('(max-width: 767px)').matches) return;
            var row = event.target.closest('tr[data-card-href]');
            if (!row || event.target.closest('a, button, form, input, select, textarea')) return;
            window.location.href = row.getAttribute('data-card-href');
        });
    })();
</script>