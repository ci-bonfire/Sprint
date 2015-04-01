<?php

use \Mockery as m;

class I18nTest extends CodeIgniterTestCase {

    protected $i18n;

    protected $segments = [
        1 => 'en',
        2 => 'home'
    ];

    //--------------------------------------------------------------------

    public function _before()
    {
        $this->i18n = new \Myth\Localization\I18n();

        $langs = [
            'en' => 'english',
            'ru' => 'russian'
        ];
        $this->ci->config->set_item('i18n.languages', $langs);
    }

    //--------------------------------------------------------------------

    public function _after()
    {

    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // SetLanguage
    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSetLanguageStripsLanguageCode()
    {
        $expected = [ 1 => 'home' ];

        $this->assertEquals($expected, $this->i18n->setLanguage($this->segments));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSetLanguageDoesntStripMultipleLanguageCode()
    {
        $segments = [ 1 => 'en', 2 => 'en', 3 => 'home'];

        $expected = [ 1 => 'en', 2 => 'home' ];

        $this->assertEquals($expected, $this->i18n->setLanguage($segments));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSetLanguageDoesntStripMultipleLanguageCodes()
    {
        $segments = [ 1 => 'en', 2 => 'ru', 3 => 'home'];

        $expected = [ 1 => 'ru', 2 => 'home' ];

        $this->assertEquals($expected, $this->i18n->setLanguage($segments));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSetLanguageDoesntMessWithStandardSegments()
    {
        $segments = [ 1 => 'home', 2 => 'run'];

        $expected = [ 1 => 'home', 2 => 'run'];

        $this->assertEquals($expected, $this->i18n->setLanguage($segments));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSetLanguageWorksWithEmtpyArray()
    {
        $segments = [];

        $expected = [];

        $this->assertEquals($expected, $this->i18n->setLanguage($segments));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Clean URIString
    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testCleanLeavesStringIntactByDefault()
    {
        $this->i18n->setLanguage( [1 => 'at', 2 => 'home'] );
        $expected = $uristring = 'at/home';

        $this->assertEquals($expected, $this->i18n->cleanUriString($uristring));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testCleanStripsLangString()
    {
        $this->i18n->setLanguage( [1 => 'en', 2 => 'home'] );
        $uristring = 'en/home';
        $expected = 'home';

        $this->assertEquals($expected, $this->i18n->cleanUriString($uristring));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testCleanStripsLangStringWithShortSegments()
    {
        $this->i18n->setLanguage( [1 => 'en'] );
        $uristring = 'en';
        $expected = '';

        $this->assertEquals($expected, $this->i18n->cleanUriString($uristring));
    }

    //--------------------------------------------------------------------

}