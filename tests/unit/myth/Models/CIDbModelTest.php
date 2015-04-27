<?php

use Myth\Models\CIDbModel as CIDbModel;
use \Mockery as m;

require_once "tests/_support/database.php";
require_once "tests/_support/form_validation.php";

class CIDbModelTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    public $load_model = 'Record_model';

    public $model;

    //--------------------------------------------------------------------

    protected function _before()
    {
        if (! class_exists($this->load_model))
        {
            $file = strtolower($this->load_model);
            require ("tests/_support/models/{$file}.php");
        }

        $db = m::mock('MY_DB');
        $fv = m::mock('MY_Form_validation');

        $model_name = $this->load_model;
        $this->model = new $model_name($db, $fv);
    }

    protected function _after()
    {
        if (isset($this->model))
        {
            unset($this->model);
        }
    }

    // tests
    public function testIsLoaded()
    {
        // Will also load the class into memory...
        $this->assertTrue( class_exists('\Myth\Models\CIDbModel') );
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // CRUD Methods
    //--------------------------------------------------------------------

    public function testFirst()
    {
        $orig = new stdClass();
        $orig->something = 'has value';

        $this->model->db->shouldReceive('limit')->once()->with(1, '')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn( $orig );

        $obj = $this->model->first();
        $this->assertEquals($orig, $obj);
    }

    //--------------------------------------------------------------------

    public function testFirstReturnsFirstArrayElement()
    {
        $orig = [
            'fake object'
        ];

        $this->model->db->shouldReceive('limit')->once()->with(1, '')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn( $orig );

        $obj = $this->model->first();
        $this->assertEquals($orig[0], $obj);
    }

    //--------------------------------------------------------------------

    public function testFind()
    {
        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $obj = $this->model->find(1);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindWithSoftDeletes()
    {
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->once()->with('deleted', 0)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $obj = $this->model->find(1);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindWithDeletedOn()
    {
        // We have to be using soft deletes
        // for with_deleted to have any effect...
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->with('deleted', 0)->never();
        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $this->model->with_deleted()->find(1);
        $this->assertFalse($this->model->get_with_deleted());
    }

    //--------------------------------------------------------------------

    public function testFindBy()
    {
        $this->model->db->shouldReceive('where')->once()->with('name', 'Darth')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $obj = $this->model->find_by('name', 'Darth');
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindByWithSoftDeletes()
    {
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->once()->with('deleted', 0)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('where')->once()->with('name', 'Darth')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $obj = $this->model->find_by('name', 'Darth');
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindByWithDeleted()
    {
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->with('deleted', 0)->never();
        $this->model->db->shouldReceive('where')->once()->with('name', 'Darth')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $obj = $this->model->with_deleted()->find_by('name', 'Darth');
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindMany()
    {
        $ids = [1, 2, 3];

        $this->model->db->shouldReceive('where_in')->once()->with('id', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->find_many($ids);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindManyWithSoftDeletes()
    {
        $ids = [1, 2, 3];

        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->once()->with('deleted', 0)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('where_in')->once()->with('id', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->find_many($ids);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindManyWithDeleted()
    {
        $ids = [1, 2, 3];

        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->with('deleted', 0)->never();
        $this->model->db->shouldReceive('where_in')->once()->with('id', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->with_deleted()->find_many($ids);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindManyBy()
    {
        $ids = [1, 2, 3];

        $this->model->db->shouldReceive('where')->once()->with('name', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->find_many_by('name', $ids);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindManyByWithSoftDeletes()
    {
        $ids = [1, 2, 3];

        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->once()->with('deleted', 0)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('where')->once()->with('name', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->find_many_by('name', $ids);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindManyByWithDeleted()
    {
        $ids = [1, 2, 3];

        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->with('deleted', 0)->never();
        $this->model->db->shouldReceive('where')->once()->with('name', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->with_deleted()->find_many_by('name', $ids);
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindAll()
    {
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db);
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->find_all();
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindAllWithSoftDeletes()
    {
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->once()->with('deleted', 0)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db);
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->find_all();
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testFindAllWithDeleted()
    {
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->with('deleted', 0)->never();
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db);
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->with_deleted()->find_all();
        $this->assertEquals($obj, 'fake object');
    }

    //--------------------------------------------------------------------

    public function testInsertWithReturnInsertId()
    {
        $data = [ 'email' => 'some data' ];

        $this->model->db->shouldReceive('insert')->once()->with('records_table', $data)->andReturn( true );
        $this->model->db->shouldReceive('insert_id')->once()->andReturn(11);
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->insert($data);
        $this->assertEquals($obj, 11);
    }

    //--------------------------------------------------------------------

    public function testInsertWithNoReturnInsertId()
    {
        $data = [ 'email' => 'some data' ];

        $this->model->return_insert_id(false);

        $this->model->db->shouldReceive('insert')->once()->with('records_table', $data)->andReturn( true );
        $this->model->db->shouldReceive('insert_id')->never();
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->insert($data);
        $this->assertEquals($obj, true);
    }

    //--------------------------------------------------------------------

    public function testInsertBatch()
    {
        $data = [ 'some data' ];

        $this->model->db->shouldReceive('insert_batch')->once()->with('records_table', $data)->andReturn( true );

        $obj = $this->model->insert_batch($data);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testReplace()
    {
        $data = [ 'email' => 'some data' ];

        $this->model->db->shouldReceive('replace')->once()->with('records_table', $data)->andReturn( true );
        $this->model->db->shouldReceive('insert_id')->once()->andReturn(11);
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->replace($data);
        $this->assertEquals($obj, 11);
    }

    //--------------------------------------------------------------------

    public function testUpdateReturnsTrueOnSuccess()
    {
        $data = [ 'email' => 'some data' ];

        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->once()->with($data)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->once()->andReturn( true );
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->update(1, $data);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testUpdateBatchReturnsTrueOnSuccess()
    {
        $data = [ 'some data' ];

        $this->model->db->shouldReceive('update_batch')->once()->with('records_table', $data, 'id')->andReturn( true );

        $obj = $this->model->update_batch($data, 'id');
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testUpdateQuickReturnsNullWithNoData()
    {
        $ids = [];
        $data = [ 'some_data' ];

        $this->model->db->shouldReceive('where_in')->never();
        $this->model->db->shouldReceive('set')->never();
        $this->model->db->shouldReceive('update')->never();

        $obj = $this->model->update_many($ids, $data);
        $this->assertNull($obj);
    }

    //--------------------------------------------------------------------

    public function testUpdateManyReturnsTrueOnSuccess()
    {
        $ids = [1,2,3];
        $data = [ 'some_data' ];

        $this->model->db->shouldReceive('where_in')->once()->with('id', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->once()->with($data)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->once()->with('records_table')->andReturn(true);

        $obj = $this->model->update_many($ids, $data);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testUpdateByReturnsTrueOnSuccess()
    {
        $data = [ 'email' => 'some_data' ];

        $this->model->db->shouldReceive('where')->once()->with('name', 'Darth')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->once()->with($data)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->once()->with('records_table')->andReturn(true);
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->update_by('name', 'Darth', $data);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testUpdateAll()
    {
        $data = [ 'email' => 'some_data' ];

        $this->model->db->shouldReceive('where')->never();
        $this->model->db->shouldReceive('set')->once()->with($data)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->once()->with('records_table')->andReturn(true);
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->update_all($data);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDelete()
    {
        $this->model->db->shouldReceive('where')->once()->with('id', 11)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('delete')->once()->with('records_table')->andReturn(true);

        $obj = $this->model->delete(11);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDeleteWithSoftDeletes()
    {
        $this->model->soft_delete(true);

        $this->model->db->shouldReceive('where')->once()->with('id', 11)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->once()->with('records_table', ['deleted' => 1])->andReturn(true);

        $obj = $this->model->delete(11);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDeleteBy()
    {
        $this->model->db->shouldReceive('where')->once()->with('name', 'Darth')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('delete')->once()->with('records_table')->andReturn(true);

        $obj = $this->model->delete_by('name', 'Darth');
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDeleteMany()
    {
        $ids = [1,2,3];

        $this->model->db->shouldReceive('where_in')->once()->with('id', $ids)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('delete')->once()->with('records_table')->andReturn(true);

        $obj = $this->model->delete_many($ids);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDeleteManyQuickExistsWithNoData()
    {
        $ids = [];

        $this->model->db->shouldReceive('where_in')->never();
        $this->model->db->shouldReceive('delete')->never();

        $obj = $this->model->delete_many($ids);
        $this->assertNull($obj);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Scope Method Checks
    //--------------------------------------------------------------------

    public function testFindAsObject()
    {
        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row')->once()->andReturn('fake object');

        $obj = $this->model->as_object()->find(1);
        $this->assertEquals( 'fake object', $obj );
    }

    //--------------------------------------------------------------------

    public function testFindAsArray()
    {
        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row_array')->once()->andReturn('fake object');

        $obj = $this->model->as_array()->find(1);
        $this->assertEquals( 'fake object', $obj );
    }

    //--------------------------------------------------------------------

    public function testFindAsJson()
    {
        $this->model->db->shouldReceive('where')->once()->with('id', 1)->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('row_array')->once()->andReturn(NULL);

        $obj = $this->model->as_json()->find(1);
        $this->assertEquals( '{}', $obj );
    }

    //--------------------------------------------------------------------

    public function testFindAllAsObject()
    {
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result')->once()->andReturn('fake object');

        $obj = $this->model->as_object()->find_all();
        $this->assertEquals( 'fake object', $obj );
    }

    //--------------------------------------------------------------------

    public function testFindAllAsArray()
    {
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result_array')->once()->andReturn('fake object');

        $obj = $this->model->as_array()->find_all();
        $this->assertEquals( 'fake object', $obj );
    }

    //--------------------------------------------------------------------

    public function testFindAllAsJson()
    {
        $this->model->db->shouldReceive('get')->once()->with('records_table')->andReturn( $this->model->db );
        $this->model->db->shouldReceive('result_array')->once()->andReturn('fake object');

        $obj = $this->model->as_json()->find_all();
        $this->assertEquals( '[]', $obj );
    }

    //--------------------------------------------------------------------

    public function testCountBy()
    {
        $this->model->db->shouldReceive('where')->with('name', 'Darth')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('count_all_results')->with('records_table')->once()->andReturn(52);

        $obj = $this->model->count_by('name', 'Darth');
        $this->assertEquals(52, $obj);
    }

    //--------------------------------------------------------------------

    public function testGetField()
    {
        $result = new stdClass();
        $result->name = 'Darth';

        $this->model->db->shouldReceive('select')->with('name')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->with('records_table')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('num_rows')->once()->andReturn( 1 );
        $this->model->db->shouldReceive('row')->once()->andReturn( $result );

        $obj = $this->model->get_field(11, 'name');
        $this->assertEquals('Darth', $obj);
    }

    //--------------------------------------------------------------------

    public function testIsUniqueReturnsFalseWhenMatchFound()
    {
        $this->model->db->shouldReceive('where')->with('name', 'Darth')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->with('records_table')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('num_rows')->once()->andReturn( 1 );

        $obj = $this->model->is_unique('name', 'Darth');
        $this->assertFalse($obj);
    }

    //--------------------------------------------------------------------

    public function testIsUniqueReturnsTrueWhenNoMatchFound()
    {
        $this->model->db->shouldReceive('where')->with('name', 'Darth')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('get')->with('records_table')->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('num_rows')->once()->andReturn( 0 );

        $obj = $this->model->is_unique('name', 'Darth');
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testIncrement()
    {
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->with('hits', 'hits+1', false)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->with('records_table')->once()->andReturn(true);

        $obj = $this->model->increment(11, 'hits');
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testIncrementWithDifferentValue()
    {
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->with('hits', 'hits+5', false)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->with('records_table')->once()->andReturn(true);

        $obj = $this->model->increment(11, 'hits', 5);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testIncrementConvertsWrongSignsOnValues()
    {
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->with('hits', 'hits+5', false)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->with('records_table')->once()->andReturn(true);

        $obj = $this->model->increment(11, 'hits', -5);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDecrement()
    {
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->with('hits', 'hits-1', false)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->with('records_table')->once()->andReturn(true);

        $obj = $this->model->decrement(11, 'hits');
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDecrementWithDifferentValue()
    {
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->with('hits', 'hits-5', false)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->with('records_table')->once()->andReturn(true);

        $obj = $this->model->decrement(11, 'hits', 5);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    public function testDecrementConvertsWrongSignsOnValues()
    {
        $this->model->db->shouldReceive('where')->with('id', 11)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('set')->with('hits', 'hits-5', false)->once()->andReturn( $this->model->db );
        $this->model->db->shouldReceive('update')->with('records_table')->once()->andReturn(true);

        $obj = $this->model->decrement(11, 'hits', -5);
        $this->assertTrue($obj);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Validation Checks
    //--------------------------------------------------------------------

    public function testInsertRunsValidation()
    {
        $data = [ 'email' => 'some data' ];

        $this->model->return_insert_id(false);

        $this->model->db->shouldReceive('insert')->once()->with('records_table', $data)->andReturn( true );
        $this->model->db->shouldReceive('insert_id')->never();
        $this->model->form_validation->shouldReceive('set_rules')->once();
        $this->model->form_validation->shouldReceive('set_data')->once();
        $this->model->form_validation->shouldReceive('run')->once()->andReturn(true);
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['email'] );

        $obj = $this->model->insert($data, false);
        $this->assertEquals($obj, true);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Protected Attributes
    //--------------------------------------------------------------------

    public function testProtectedAttributesStripsAttribute()
    {
        $this->model->protect('name');

        $data = array('name' => 'MyName', 'title' => 'MyTitle');

        $this->model->db->shouldReceive('insert')->with('records_table', ['title' => 'MyTitle'])->once()->andReturn(true);
        $this->model->db->shouldReceive('insert_id')->once()->andReturn(5);
        $this->model->db->shouldReceive('list_fields')->once()->andReturn( ['name', 'title'] );

        $id = $this->model->insert($data);
        $this->assertEquals($id, 5);
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // CI's Database Methods
    //--------------------------------------------------------------------
    
    public function testCanAccessSelect()
    {
        $this->model->db->shouldReceive('select')->once();

        $this->model->select('something');
    }
    
    //--------------------------------------------------------------------

    /**
     * @group single
     */
    public function testCreatedOnInsertsDate()
    {
        $this->model->set_created = true;

        $data = array('name' => 'MyName', 'title' => 'MyTitle');
        $expected = array('name' => 'MyName', 'title' => 'MyTitle', 'created_on' => date('Y-m-d H:i:s'));

        $this->assertEquals($expected, $this->model->created_on(['method' => 'insert', 'fields' => $data]));
    }
    
    //--------------------------------------------------------------------

    /**
     * @group single
     */
    public function testModifiedOnInsertsDate()
    {
        $this->model->set_modified = true;

        $data = array('name' => 'MyName', 'title' => 'MyTitle');
        $expected = array('name' => 'MyName', 'title' => 'MyTitle', 'modified_on' => date('Y-m-d H:i:s'));

        $this->assertEquals($expected, $this->model->modified_on(['method' => 'insert', 'fields' => $data]));
    }

    //--------------------------------------------------------------------
}
