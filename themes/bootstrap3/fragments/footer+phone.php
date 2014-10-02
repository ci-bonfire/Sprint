    <div class="footer">
        <hr/>

        <div class="<?= $containerClass ?> text-right">
            <p class="text-muted small">Page rendered in {elapsed_time} seconds using {memory_usage}.</p>
            <p>Rendered just for your mobile phone!</p>
        </div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <?php foreach ($external_scripts as $script) : ?>
        <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
</body>
</html>