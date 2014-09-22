<?php

require_once 'application/helpers/markdown_helper.php';
require_once 'application/helpers/markdown_extended_helper.php';

define ('DEV_DOCPATH', 'myth/_docs_src');
define ('APP_DOCPATH', 'application/docs');


class DocBuilderTest extends \Codeception\TestCase\Test
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $builder;

    protected function _before()
    {
        $config = array(
            'apppath' => 'application'
        );

        $this->builder = new \Myth\Docs\Builder( $config );
        $this->builder->registerFormatter('MarkdownExtended');
    }

    protected function _after()
    {
        unset($this->builder);
    }

    //--------------------------------------------------------------------

    public function testClassIsLoaded()
    {
        $this->assertTrue(gettype($this->builder) == 'object');
        $this->assertEquals(get_class($this->builder), 'Myth\Docs\Builder');
    }

    //--------------------------------------------------------------------

    public function testDocFolderPaths()
    {
        $this->builder->addDocFolder('application', APP_DOCPATH);
        $this->builder->addDocFolder('developer', DEV_DOCPATH);

        $final = [
            'application' => realpath( APP_DOCPATH ) . '/',
            'developer'   => realpath( DEV_DOCPATH ) . '/'
        ];

        $this->assertEquals($this->builder->docFolders(), $final);

        unset($final['application']);
        $this->builder->removeDocFolder('Application');

        $this->assertEquals($this->builder->docFolders(), $final);
    }

    //--------------------------------------------------------------------

    /**
     * Verify that reading in the routes docs works and processes
     * the Markdown, etc.
     */
    public function testReadPageBasics()
    {
        $this->builder->addDocFolder('developer', DEV_DOCPATH);

        // Verify Reads content
        $content = $this->builder->readPage('general/routes', 'developer');
        $this->assertNotEmpty($content);

        // Verify Markdown processing
        $this->assertTrue(strpos($content, '<h2>') !== false);
    }

    //--------------------------------------------------------------------

    public function testPostProcessLinkConversion()
    {
        $site_url = 'http://testsite.com';

        $start = '<a href="docs/developer/test">Test</a>';
        $final = '<div><a href="' . $site_url . '/docs/developer/test">Test</a></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $site_url));
    }

    //--------------------------------------------------------------------

    public function testPostProcessNamedAnchorsAllowed()
    {
        $site_url = 'http://testsite.com';

        $start = '<a name="test"></a>';
        $final = '<div><a name="test" href=" "/></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $site_url));
    }

    //--------------------------------------------------------------------

    public function testPostProcessConvertsLinksToNamedAnchors()
    {
        $site_url    = 'http://testsite.com';
        $current_url = 'http://testsite.com/docs/test';

        $start = '<a href="#test">Test</a>';
        $final = '<div><a href="' . $current_url . '#test">Test</a></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $current_url));
    }

    //--------------------------------------------------------------------

    public function testPostProcessLinkConversionHandlesLocalFullLinks()
    {
        $site_url = 'http://testsite.com';

        $start = '<a href="http://testsite.com/docs/developer/test">Test</a>';
        $final = '<div><a href="' . $site_url . '/docs/developer/test">Test</a></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $site_url));
    }

    //--------------------------------------------------------------------

    public function testPostProcessAddsTableClasses()
    {
        $site_url = 'http://testsite.com';
        $classes  = 'myclass your-class';

        $this->builder->setTableClasses($classes);

        $start = '<table><tbody></tbody></table>';
        $final = '<div><table class="' . $classes . '"><tbody/></table></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $site_url));
    }

    //--------------------------------------------------------------------

    public function testPostProcessKeepsExternalUrls()
    {
        $site_url = 'http://testsite.com';

        $start = '<a href="http://anothertestsite.com/docs/developer/test">Test</a>';
        $final = '<div><a href="http://anothertestsite.com/docs/developer/test" target="_blank">Test</a></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $site_url));
    }

    //--------------------------------------------------------------------

    public function testPostProcessHandlesPoundSignsOnLink()
    {
        $site_url    = 'http://testsite.com';
        $current_url = 'http://testsite.com/docs/developer#';

        $this->builder->addDocFolder('application', APP_DOCPATH);
        $this->builder->addDocFolder('developer', DEV_DOCPATH);

        $start = '<li><a href="index">Bonfire Docs Home</a></li>';
        $final = '<div><li><a href="http://testsite.com/docs/developer/index">Bonfire Docs Home</a></li></div>';

        $this->assertEquals($final, $this->builder->postProcess($start, $site_url, $current_url));
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Document maps
    //--------------------------------------------------------------------

//    public function testBuildDocMapAddsAnchorsToContent()
//    {
//        $start = "## Second
//### Third";
//
//        $start = MarkdownExtended($start);
//
//        $final = '<a name="second" id="second" ></a>' . "<h2>Second</h2>\n\n" .
//                 '<a name="third" id="third" ></a>' . "<h3>Third</h3>\n";
//
//        $this->builder->buildDocumentMap($start);
//
//        $this->assertEquals($start, $final);
//    }

    //--------------------------------------------------------------------

//    public function testBuildDocMapBasics()
//    {
//        $start = "# First
//Some text goes here
//
//## Second
//Some more text
//
//### Third
//Third-level text
//
//#### Fourth
//
//## Another Second
//
//### Another Third";
//
//        $start = MarkdownExtended($start);
//
//        $final = [
//            [
//                'name'  => 'Second',
//                'link'  => '#second',
//                'items' => [
//                    [
//                        'name' => 'Third',
//                        'link' => '#third'
//                    ]
//                ]
//            ],
//            [
//                'name'  => 'Another Second',
//                'link'  => '#another_second',
//                'items' => [
//                    [
//                        'name' => 'Another Third',
//                        'link' => '#another_third'
//                    ]
//                ]
//            ],
//        ];
//
//        $this->assertEquals($final, $this->builder->buildDocumentMap($start));
//    }
    //--------------------------------------------------------------------

}