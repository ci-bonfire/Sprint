<?php

class Ajax extends \Myth\Controllers\ThemedController {

	protected $theme = 'foundation';

    protected $tasks = array();

    protected $done_tasks = array();

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        $this->tasks = $this->session->userdata('tasks');
        if (! empty($this->tasks))
        {
            $this->tasks = unserialize($this->tasks);
        }

        $this->done_tasks = $this->session->userdata('done-tasks');
        if (! empty($this->done_tasks))
        {
            $this->done_tasks = unserialize($this->done_tasks);
        }
    }

    //--------------------------------------------------------------------

    public function index()
    {
        $this->setVar('tasks', $this->buildTaskList());
        $this->setVar('done_tasks', $this->buildDoneList());

        $this->setVar('navbar_style', 'navbar-static');
        $this->setVar('containerClass', 'container');

        $this->themer->setView('demos/ajax/index');
        $this->render();
    }

    //--------------------------------------------------------------------
    // AJAX Methods
    //--------------------------------------------------------------------

    public function new_task()
    {
        $response = array();

        $task = $this->input->post('task');

        if (! $task)
        {
            $this->setMessage('The task did not have any text!', 'danger');
        }
        else
        {
            $this->tasks[] = array(
                'id' => count($this->tasks) + count($this->done_tasks) + 1,
                'task' => trim($task)
            );
            $this->setMessage('Task successfully saved.', 'success');
        }

        $this->saveTasks();

        $response['fragments']['#task-list'] = $this->buildTaskList();
        $response['fragments']['#done-list'] = $this->buildDoneList();

        $this->renderJSON($response);
    }
    
    //--------------------------------------------------------------------

    public function complete_task($id)
    {
        $response = array();

        $index = $this->findTask($this->tasks, $id);

        if (! is_numeric($index))
        {
            $this->setMessage('Unable to locate the task to complete!', 'danger');
        }
        else
        {
            $task = $this->tasks[$index];
            unset($this->tasks[$index]);
            $this->done_tasks[] = $task;
            $this->setMessage('Task marked as completed.', 'success');
        }

        $this->saveTasks();

        $response['fragments']['#task-list'] = $this->buildTaskList();
        $response['fragments']['#done-list'] = $this->buildDoneList();

        $this->renderJSON($response);
    }

    //--------------------------------------------------------------------

    public function restore_task($id)
    {
        $response = array();

        $index = $this->findTask($this->done_tasks, $id);

        if (! is_numeric($index))
        {
            $this->setMessage('Unable to locate the task to restore!', 'danger');
        }
        else
        {
            $task = $this->done_tasks[$index];
            unset($this->done_tasks[$index]);
            $this->tasks[] = $task;
            $this->setMessage('Task successfully restored.', 'success');
        }

        $this->saveTasks();

        $response['fragments']['#task-list'] = $this->buildTaskList();
        $response['fragments']['#done-list'] = $this->buildDoneList();

        $this->renderJSON($response);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    private function buildTaskList()
    {
        $list = "<ul id='task-list' class='tasks'>";

        if (! empty($this->tasks)) {
            foreach ($this->tasks as $task) {
                $list .= "<li><a href='" . site_url('demos/ajax/complete_task/'. $task['id']) . "' class='ajax'>{$task['task']}</a></li>";
            }
        }

        $list .= "</ul>";

        return $list;
    }

    //--------------------------------------------------------------------

    private function buildDoneList()
    {
        $list = "<ul id='done-list' class='tasks'>";

        if (! empty($this->done_tasks)) {
            foreach ($this->done_tasks as $task) {
                $list .= "<li><a href='" . site_url('demos/ajax/restore_task/'. $task['id']) . "' class='ajax'>{$task['task']}</a></li>";
            }
        }

        $list .= "</ul>";

        return $list;
    }

    //--------------------------------------------------------------------

    private function saveTasks()
    {
        $this->session->set_userdata('tasks', serialize($this->tasks));
        $this->session->set_userdata('done-tasks', serialize($this->done_tasks));
    }

    //--------------------------------------------------------------------

    /**
     * Searches an array of our tasks to locate a specific task by 'id'.
     * Returns the index of the element in the array.
     *
     * @param $tasks
     * @param $id
     * @return int|null|string
     */
    private function findTask($tasks, $id)
    {
        foreach ($tasks as $index => $task)
        {
            if ($task['id'] == $id)
            {
                return $index;
            }
        }

        return null;
    }

    //--------------------------------------------------------------------

}
