    <div class="footer">
        <hr/>

        <div class="<?= $containerClass ?> text-right">
            <p class="text-muted small">Page rendered in {elapsed_time} seconds using {memory_usage}.</p>
            <p class="text-muted small">PHP <?= phpversion() ?>. CodeIgniter <?= CI_VERSION ?>. SprintPHP <?= SPRINT_VERSION ?></p>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="<?= site_url('assets/js/eldarion/eldarion-ajax.min.js') ?>"></script>
    <script src="<?= site_url('assets/js/ajax.js') ?>"></script>
    <?php foreach ($external_scripts as $script) : ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>

    <div id="ajax-loader" class="alert-warning">Loading...</div>
</body>
</html>