<?php
namespace Myth\Forge;
use \UnitTester;

class FileKitTestCest
{
    protected $filename = 'application/cache/filekittest.txt';

    public function _before(UnitTester $I)
    {
        $content = <<<EOF
Put stuff before here.

Put stuff after here.

EOF;
        $I->writeToFile($this->filename, $content);
    }

    public function _after(UnitTester $I)
    {
        // Clean up our test file.
//        $I->deleteFile( $this->filename );
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Append
    //--------------------------------------------------------------------

    public function appendContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
Put stuff before here.

Put stuff after here.
New Content

EOF;

        $kit->append($this->filename, $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    public function appendReturnsTrueOnEmptyContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "";

        $test = <<<EOF
Put stuff before here.

Put stuff after here.
New Content

EOF;

        $result = $kit->append($this->filename, $str);

        $I->assertTrue($result);
    }

    //--------------------------------------------------------------------

    public function appendCreatesNewFileIfDoesntExist(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
Put stuff before here.

Put stuff after here.
New Content

EOF;

        $kit->append('application/cache/test.txt', $str);

        $I->openFile($this->filename);
        $I->seeFileFound('test.txt', 'application/cache');

        $I->deleteFile('application/cache/test.txt');
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Prepend
    //--------------------------------------------------------------------

    public function prependContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
New Content
Put stuff before here.

Put stuff after here.

EOF;

        $kit->prepend($this->filename, $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Before
    //--------------------------------------------------------------------

    public function beforeContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
New Content
Put stuff before here.

Put stuff after here.

EOF;

        $kit->before($this->filename, "Put stuff before here.\n", $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    public function beforeContentDoubleCheck(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
Put stuff before here.

New Content
Put stuff after here.

EOF;

        $kit->before($this->filename, "Put stuff after here.\n", $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // After
    //--------------------------------------------------------------------

    public function afterContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
Put stuff before here.

Put stuff after here.
New Content

EOF;

        $kit->after($this->filename, "Put stuff after here.\n", $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    public function afterContentDoubleCheck(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
Put stuff before here.
New Content

Put stuff after here.

EOF;

        $kit->after($this->filename, "Put stuff before here.\n", $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // ReplaceIn
    //--------------------------------------------------------------------

    public function replaceInContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "New Content";

        $test = <<<EOF
New Content before here.

New Content after here.

EOF;

        $kit->replaceIn($this->filename, "Put stuff", $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Regex
    //--------------------------------------------------------------------

    public function regexContent(UnitTester $I)
    {
        $kit = new FileKit();

        $str = "X";

        $test = <<<EOF
Xut stuff before here.

Xut stuff after here.

EOF;

        $kit->replaceWithRegex($this->filename, "/P/", $str);

        $I->openFile($this->filename);
        $I->canSeeFileContentsEqual($test);
    }

    //--------------------------------------------------------------------
}