<?php

class CronMailer extends \Myth\Mail\BaseMailer {

    protected $from     = null;
    protected $to       = null;

    public function __construct()
    {
        $this->from = [ config_item('site.auth_email'), config_item('site.name') ];
        $this->to   = config_item('site.auth_email');
    }

    //--------------------------------------------------------------------

    /**
     * Sends the output from the cronjob to the admin.
     *
     * @param $output
     */
    public function results($output=null)
    {
        $data = [
            'output' => $output,
            'site_name' => config_item('site.name')
        ];

        // Send it immediately - don't queue.
        return $this->send($this->to, "Cron Results from ". config_item('site.name'), $data);
    }

    //--------------------------------------------------------------------

}
