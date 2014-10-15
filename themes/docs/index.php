<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <title><?= !empty($page_title) ? $page_title : 'Docs'; echo ' - '. config_item('site.name') ?></title>

    <meta name=viewport content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/themes/docs/css/custom.css" />
    <link rel="stylesheet" type="text/css" href="/themes/docs/css/codestyles/rainbow.css" />
</head>
<body>

    <a name="top"></a>

    <!-- Navbar -->
    <header class="navbar navbar-inverse navbar-static-top" role="banner">
        <div class="container">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>


            <div class="collapse navbar-collapse" id="main-nav-collapse">
                <ul class="nav navbar-nav navbar-left">
                    <?php foreach (config_item('docs.folders') as $group => $path) : ?>
                        <li <?php if ($this->uri->segment(2) == $group) echo 'class="active"'; ?>>
                            <a href="<?= site_url("docs/{$group}"); ?>"><?= ucwords($group) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Search Form -->
                <?= form_open( site_url('docs/search'), 'class="navbar-form navbar-right"' ); ?>
                <div class="form-group">
                        <input type="search" class="form-control" name="search_terms" placeholder="<?= lang('docs_search_for') ?>"/>
                    </div>
                    <input type="submit" name="submit" class="btn btn-default" value="<?= lang('docs_search') ?>">
                </form>
            </div>

        </div>
    </header>

    <?php if ($this->uri->segment(2) !== 'search') : ?>
    <div class="toc-wrapper">
        <div class="container">

            <div class="toc" style="display:none">
                <?= $toc ?>
            </div>
            <a href="#" id="toc-btn" style="margin: 5px 10px 0 0"><?= lang('docs_toc') ?></a>
        </div>
    </div>
    <?php endif; ?>


    <!-- Content Area -->
    <div class="container">

        <?= isset($notice) ? $notice : ''; ?>

        <div class="row">

            <div class="col-md-9 main">
                <?php if (! empty($view_content)) : ?>
                    <?= $view_content; ?>
                <?php else: ?>
                    <div class="alert">
                        <?= lang('docs_not_found') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-3 sidebar">
                <div data-spy="affix" data-offset-top="120" data-offset-bottom="120">
                <?php if (isset($sidebar)) : ?>
                    <?= $sidebar; ?>
                <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

    <div class="container footer">
        <small>Page rendered in {elapsed_time} sec. using {memory_usage}.</small>
    </div>

    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="/themes/docs/js/ajax.js"></script>
    <script src="/themes/docs/js/highlight.min.js"></script>
    <script src="/themes/docs/js/docs.js"></script>
</body>
</html>