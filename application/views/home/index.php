<div id="container">
	<h1>Welcome to SprintPHP!</h1>

    <br/>

    <?= $uikit->row([], function() use($uikit) {

        echo $uikit->column(['sizes' => ['l'=>6]], function() use($uikit) { ?>
            <h3>What Is Sprint?</h3>

            <p>SprintPHP is a souped-up version of <a href="http://codeigniter.com">CodeIgniter <?= CI_VERSION ?></a>. And soon to be the heart and soul
                of <a href="" target="_blank">Bonfire Next</a>. </p>

            <p>If you would like to edit this page you'll find it located at:</p>

            <code>application/views/home/index.php</code>

            <p>The corresponding controller for this page is found at:</p>

            <code>application/controllers/Home.php</code>

        <?php });





        echo $uikit->column(['sizes' => ['l'=>6]], function() use($uikit) { ?>
            <h3>Get To Know Sprint</h3>

            <p>The following resources will help you as you explore the power and flexibility that SprintPHP provides. Feel free to dig into source code of the controllers and views
                to really discover how things are working. You never know what buried treasure you'll find!</p>

            <ul>
                <li><a href="http://ci3docs.cibonfire.com" target="_blank">CodeIgniter 3 User Guide</a></li>
                <li><a href="<?= site_url('docs') ?>">SprintPHP Documentation</a></li>
                <li><a href="<?= site_url('demos/ajax') ?>">Simple AJAX Demo</a></li>
                <li><a href="<?= site_url('demos/callbacks') ?>">View Callbacks Demo</a></li>
                <li><a href="<?= site_url('demos/uikits') ?>">UIKits Demo</a></li>
            </ul>

            <p>And don't forget to explore the themes and layouts that come with Sprint by exploring the menu above.</p>
        <?php });

    }); ?>

</div>
