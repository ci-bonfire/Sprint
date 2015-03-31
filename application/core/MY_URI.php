<?php

class MY_URI extends \CI_URI {
	
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
            parent::__construct();
			
			\Myth\Localization\I18n::setLanguage($this->segments);
	}
}
