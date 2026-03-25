            </div>
        </main>
    </div>
    <script>
        // Restore appointments nav state
        (function() {
            var nav = document.getElementById('appointments-nav');
            if (nav) {
                var state = localStorage.getItem('appointmentsNav');
                if (state === 'expanded') {
                    nav.classList.remove('collapsed');
                }
            }
        })();
    </script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
