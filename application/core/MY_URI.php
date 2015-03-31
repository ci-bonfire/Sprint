<?php

class MY_URI extends \CI_URI {

    /**
     * Class constructor
     */
	public function __construct()
	{
        parent::__construct();

        // Remove localization segment, if set
        $this->config->load('application');

        if ($this->config->item('i18n') === true)
        {
            $i18n = new \Myth\Localization\I18n();
            $this->segments = $i18n->setLanguage($this->segments);
        }
	}

    //--------------------------------------------------------------------

}
