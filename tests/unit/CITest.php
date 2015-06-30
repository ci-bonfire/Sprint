<?php

class CITest extends CodeIgniterTestCase {
    protected $ci;

    public function testGetPost()
    {
        $expected = 'bar';

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['foo'] = 'bar';
        $this->assertEquals(! $expected, $this->ci->input->get_post('foo'));
    }
}