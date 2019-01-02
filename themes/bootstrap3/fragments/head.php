<!DOCTYPE html>
<html lang="en">
<head>
    <?= $html_meta->renderTags() ?>

    <link rel="icon" href="../../favicon.ico">

    <title><?= config_item('site.name') ?></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= site_url('themes/bootstrap3/css/ajax.css') ?>">
    <?php foreach ($stylesheets as $style) :?>
        <link rel="stylesheet" href="<?= $style ?>" />
    <?php endforeach; ?>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>