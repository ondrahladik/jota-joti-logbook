</div>

<footer class="jj-footer mt-4">
    <div class="container-fluid px-4">
        <div class="row align-items-center py-3">
            <div class="col-md-6 text-center text-md-start">
                <span class="text-jj-muted small">
                    Copyright &copy; 2026
                    <a href="https://www.ok1kky.cz" class="text-jj-muted text-decoration-none" target="_blank" rel="noopener"><?= h(APP_CALLSIGN) ?></a>
                </span>
            </div>

            <div class="col-md-6 text-center text-md-end">
                <span class="text-jj-muted small">
                    <i class="fa-brands fa-github me-1"></i>
                    <a href="https://github.com/ondrahladik/jota-joti-logbook" class="text-jj-muted text-decoration-none" target="_blank" rel="noopener">
                        jota-joti-logbook
                    </a>
                </span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
<?= $extra_js ?? '' ?>
</body>
</html>