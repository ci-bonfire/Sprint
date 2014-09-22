<?php

//--------------------------------------------------------------------

class DocSearchTest extends \Codeception\TestCase\Test
{

    protected $searcher;

    protected function _before()
    {
        $this->searcher = new \Myth\Docs\Search();
    }

    protected function _after()
    {
        unset($this->searcher);
    }

    //--------------------------------------------------------------------

    public function testClassIsLoaded()
    {
        $this->assertTrue(gettype($this->searcher) == 'object');
        $this->assertEquals(get_class($this->searcher), 'Myth\Docs\Search');
    }
    //--------------------------------------------------------------------
}