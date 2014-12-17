    <div class="footer row <?= $containerClass ?> padded">
        <hr/>

        <div style="text-align: right">
            <p><small>Page rendered in {elapsed_time} seconds using {memory_usage}.</small></p>
            <p><small>PHP <?= phpversion() ?>. CodeIgniter <?= CI_VERSION ?>. SprintPHP <?= SPRINT_VERSION ?></small></p>
        </div>

    </div>

    <script src="/themes/bonfire/assets/bower_components/jquery/dist/jquery.js"></script>
    <script src="/themes/bonfire/assets/bower_components/foundation/js/foundation.js"></script>
    <script src="/themes/bonfire/assets/bower_components/foundation/js/foundation/foundation.alert.js"></script>
    <?php foreach ($external_scripts as $script) : ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>

    <script>
//        $(document).foundation();
    </script>
</body>
</html>