
        /* ===================== SEARCH ===================== */
        var searchOverlay = document.getElementById('searchOverlay');
        var searchInput = document.getElementById('searchInput');
        var searchResults = document.getElementById('searchResults');
        var allSearchItems = searchResults.querySelectorAll('.search-item');

        function openSearch() {
            searchOverlay.classList.add('active');
            searchInput.value = '';
            filterSearch('');
            setTimeout(function() { searchInput.focus(); }, 100);
        }

        function closeSearch(e) {
            if (e && e.target !== searchOverlay) return;
            searchOverlay.classList.remove('active');
        }

        function filterSearch(query) {
            var q = query.toLowerCase().trim();
            var visible = 0;
            allSearchItems.forEach(function(item) {
                var title = (item.getAttribute('data-title') || '').toLowerCase();
                var desc = (item.getAttribute('data-desc') || '').toLowerCase();
                var match = !q || title.indexOf(q) !== -1 || desc.indexOf(q) !== -1;
                item.style.display = match ? 'flex' : 'none';
                if (match) visible++;
            });
            var groups = searchResults.querySelectorAll('.search-group-label');
            groups.forEach(function(g) {
                var next = g.nextElementSibling;
                var hasVisible = false;
                while (next && !next.classList.contains('search-group-label')) {
                    if (next.style.display !== 'none') hasVisible = true;
                    next = next.nextElementSibling;
                }
                g.style.display = hasVisible ? 'block' : 'none';
            });
        }
        var sheetOverlay = document.getElementById('sheetOverlay');
        var sheetPanel = document.getElementById('sheetPanel');

        function openSheet() {
            sheetOverlay.classList.add('active');
            sheetPanel.classList.add('active');
        }

        function closeSheet() {
            sheetOverlay.classList.remove('active');
            sheetPanel.classList.remove('active');
        }
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                if (searchOverlay.classList.contains('active')) {
                    searchOverlay.classList.remove('active');
                } else {
                    openSearch();
                }
            }
            if (e.key === 'Escape') {
                searchOverlay.classList.remove('active');
                closeSheet();
            }
        });
    