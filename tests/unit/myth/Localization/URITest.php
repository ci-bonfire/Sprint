<?php

class URITest extends CodeIgniterTestCase {

    //--------------------------------------------------------------------

    public function _before()
    {

    }

    //--------------------------------------------------------------------

    public function _after()
    {

    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSegmentsUnaffectedWithI18NDisabled()
    {
        $expected = [ 1 => 'en', 2 => 'home'];

        $_SERVER['argv'] = ['script', 'en', 'home'];
        $this->ci->config->set_item('i18n', false);

        $uri = new MY_URI();

        $this->assertEquals($expected, $uri->segments);
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSegmentsStripsLangCodeWithI18nEnabled()
    {
        $_SERVER['argv'] = ['script', 'en', 'home'];
        $this->ci->config->set_item('i18n', true);

        $uri = new MY_URI();

        $this->assertEquals('home', $uri->segment(1));
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testSegmentsStripsAndDoesntAffectURIResponses()
    {
        $_SERVER['argv'] = ['script', 'en', 'home'];
        $this->ci->config->set_item('i18n', true);

        $uri = new MY_URI();

        $this->assertEquals('home', $uri->segment(1));
        $this->assertEquals(null, $uri->segment(0));
        $this->assertEquals([1 => 'home'], $uri->segment_array());
        $this->assertEquals('home', $uri->uri_string());
    }

    //--------------------------------------------------------------------

    /**
     * @group localization
     */
    public function testUriToAssocStillWorks()
    {
        $_SERVER['argv'] = ['script', 'en', 'name', 'john'];
        $this->ci->config->set_item('i18n', true);

        $uri = new MY_URI();

        $expected = ['name' => 'john'];

        $this->assertEquals($expected, $uri->uri_to_assoc(1) );
    }

    //--------------------------------------------------------------------
}