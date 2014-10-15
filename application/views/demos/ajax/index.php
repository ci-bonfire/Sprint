<style>
    .tasks {
        margin-left: 0;
        padding-left: 0;
        -webkit-margin-before: 0;
    }
    .tasks li {
        list-style: none;
        list-style-position: inside;
        border-bottom: 1px solid #eee;
        padding: 0 1em;
    }
    .tasks a {
        line-height: 3.0;
        display: inline-block;
        width: 100%;
    }
</style>

<h2>AJAX ToDo List Demo</h2>

<p>This demo showcases some of the capabilities of the <a href="https://github.com/eldarion/eldarion-ajax" target="_blank">eldarion-ajax</a> library
that is integrated into SprintPHP.</p>

<div class="row">

    <div class="col-md-5">
        <h3>Current Tasks</h3>

        <form action="<?= site_url('tests/ajax/new_task') ?>" method="post" class="ajax" role="form">

            <div class="form-group">
                <input type="text" name="task" class="form-control" placeholder="New Task..." style="display: inline-block; width: 80%;" />
                <button type="submit" class="btn btn-default btn-primary">Create</button>
            </div>

        </form>

        <?= $tasks ?>
    </div>


    <div class="col-md-5 col-md-offset-1">
        <h3>Completed Tasks</h3>

        <?= $done_tasks ?>
    </div>

</div>