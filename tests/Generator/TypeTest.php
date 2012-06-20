<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type.
 * 
 * @group      generator 
 * @group      generator.types 
 *
 * @package    Generator 
 * @category   Tests 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_TypeTest extends Unittest_TestCase 
{
	/**
	 * If created using the Builder, any calls to undefined methods will be
	 * passed to the builder instance injected into the type.
	 */
	public function test_undefined_method_is_passed_to_builder()
	{
		$builder = $this->getMock('Generator_Builder');
		$builder->expects($this->once())->method('with_pretend');

		$type = new Generator_Type_Tester('Foo', $builder);
		$type->with_pretend();
	}

	/**
	 * If not created using the Builder, any calls to undefined methods should
	 * throw an exception.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_without_builder_undefined_method_throws_exception()
	{
		$type = new Generator_Type_Tester('Foo');
		$type->with_pretend();
	}

	/**
	 * Methods not defined on either the type or any associated builder
	 * should throw Generator_Exception.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_undefined_method_on_type_or_builder_throws_exception()
	{
		$type = new Generator_Type_Tester('Foo', new Generator_Builder);
		$type->some_undefined_method();
	}

	/**
	 * To keep the interface fluent, a number of methods act as both setters and
	 * getters. If used as setters, they should return the type instance.
	 */
	public function test_combined_setters_and_getters()
	{
		$type  = new Generator_Type_Tester();
		$dummy = 'some_string';

		$this->assertInstanceOf('Generator_Type', $type->name($dummy));
		$this->assertSame($dummy, $type->name());

		$this->assertInstanceOf('Generator_Type', $type->file($dummy));
		$this->assertSame($dummy, $type->file());

		$this->assertInstanceOf('Generator_Type', $type->template($dummy));
		$this->assertSame($dummy, $type->template());

		$this->assertInstanceOf('Generator_Type', $type->folder($dummy));
		$this->assertSame($dummy, $type->folder());

		$this->assertInstanceOf('Generator_Type', $type->module($dummy));
		$this->assertSame($dummy, $type->module());

		$dummy = array('some_key' => 'some_value');

		$this->assertInstanceOf('Generator_Type', $type->params($dummy));
		$this->assertSame($dummy, $type->params());

		$this->assertInstanceOf('Generator_Type', $type->defaults($dummy));
		$this->assertSame($dummy, $type->defaults());
	}

	/**
	 * Boolean setter methods should return the type instance, and should 
	 * ignore NULL values.
	 */
	public function test_boolean_setters()
	{
		$type = new Generator_Type_Tester();

		$this->assertAttributeSame(FALSE, '_pretend', $type);
		$this->assertInstanceOf('Generator_Type', $type->pretend());
		$this->assertAttributeSame(TRUE, '_pretend', $type);
		$this->assertInstanceOf('Generator_Type', $type->pretend(FALSE));
		$this->assertAttributeSame(FALSE, '_pretend', $type);
		$this->assertInstanceOf('Generator_Type', $type->pretend(NULL));
		$this->assertAttributeSame(FALSE, '_pretend', $type);

		$this->assertAttributeSame(FALSE, '_force', $type);
		$this->assertInstanceOf('Generator_Type', $type->force());
		$this->assertAttributeSame(TRUE, '_force', $type);
		$this->assertInstanceOf('Generator_Type', $type->force(FALSE));
		$this->assertAttributeSame(FALSE, '_force', $type);
		$this->assertInstanceOf('Generator_Type', $type->force(NULL));
		$this->assertAttributeSame(FALSE, '_force', $type);

		$this->assertAttributeSame(TRUE, '_verify', $type);
		$this->assertInstanceOf('Generator_Type', $type->verify());
		$this->assertAttributeSame(TRUE, '_verify', $type);
		$this->assertInstanceOf('Generator_Type', $type->verify(FALSE));
		$this->assertAttributeSame(FALSE, '_verify', $type);
		$this->assertInstanceOf('Generator_Type', $type->verify(NULL));
		$this->assertAttributeSame(FALSE, '_verify', $type);
	}

	/**
	 * Template parameters can be set by different methods, all of which
	 * should return the type instance.
	 */
	public function test_setting_and_getting_template_parameters()
	{
		$params = array('author' => 'author');
		$type = new Generator_Type_Tester();

		$this->assertInstanceOf('Generator_Type', $type->params($params));		
		$this->assertSame($params, $type->params());

		$this->assertInstanceOf('Generator_Type', $type->set('author', 'Anon'));
		$params = $type->params();
		$this->assertSame('Anon', $params['author']);

		$this->assertInstanceOf('Generator_Type', $type->blank(TRUE));
		$params = $type->params();
		$this->assertTrue($params['blank']);
	}

	/**
	 * Each action should be logged locally by the type in a simple format.
	 */
	public function test_local_logging()
	{
		$type = new Generator_Type_Tester();

		$this->assertInstanceOf('Generator_Type', $type->log('create', 'some_item'));
		$this->assertSame(
			array(array('status' => 'create', 'item' => 'some_item')), 
			$type->log()
		);
	}

	/**
	 * If a non-existent module is specified, an exception should be thrown
	 * unless verify mode is set to FALSE.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_verifying_nonexistent_module_throws_exception()
	{
		$type = new Generator_Type_Tester('Foo_Bar');
		$type->verify(TRUE);
		$type->module('amodule');
		$type->guess_filename();
	}

	/**
	 * If a filename has not been set manually, it should be guessed automatically
	 * based on the given name, folder and module values.
	 */
	public function test_guess_filename()
	{
		$ds = DIRECTORY_SEPARATOR;

		$type = new Generator_Type_Tester('Foo_Bar');
		$type->folder('classes');

		$expected = APPPATH.'classes'.$ds.'Foo'.$ds.'Bar'.EXT;
		$type->guess_filename();
		$this->assertSame($expected, $type->file());

		$expected = APPPATH.'classes'.$ds.'Foo_Bar';
		$type->guess_filename(FALSE);
		$this->assertSame($expected, $type->file());

		$module = 'amodule';
		$expected = MODPATH.$module.$ds.'classes'.$ds.'Foo'.$ds.'Bar'.EXT;
		$type->module($module)->verify(FALSE);
		$type->guess_filename();
		$this->assertSame($expected, $type->file());
	}

	/**
	 * The default render method should return a rendered view template.
	 */
	public function test_render()
	{
		$type = new Generator_Type_Tester('Foo');
		$type->template('generator/type_tester');

		$this->assertRegExp('/Testing Foo template/', $type->render());
	}

	/**
	 * Creating an item in pretend mode will create a log of expected actions
	 * without making any changes.
	 */
	public function test_pretend_create_makes_no_changes()
	{
		$ds = DIRECTORY_SEPARATOR;

		$type = new Generator_Type_Tester('Dummy_Foo');
		$type->folder('classes')->pretend();

		$log = array(
			array('status' => 'create', 'item' => APPPATH.'classes'.$ds.'Dummy'),
			array('status' => 'create', 'item' => APPPATH.'classes'.$ds.'Dummy'.$ds.'Foo'.EXT),
		);

		$type->create();
		$this->assertSame($log, $type->log());
		$type->create();
		$this->assertSame($log, $type->log());
	}

	/**
	 * Some parameter values, e.g. comma-separated lists, may need to be stored
	 * by the type as arrays before being rendered later as strings.
	 */
	public function test_convert_param_to_array()
	{
		$type = new Generator_Type_Tester('Foo');

		$type->param_to_array('list,of,values', 'test');
		$type->param_to_array('some_string', 'test');
		$params = $type->params();

		$this->assertSame(array('list', 'of', 'values', 'some_string'), $params['test']);
	}

	/**
	 * To support the fluent interface, calling builder() on any Type instance
	 * should return any associated Builder instance.
	 */
	public function test_builder_call_returns_associated_builder()
	{
		$builder = new Generator_Builder;
		$type = new Generator_Type_Tester('Foo', $builder);
		$this->assertInstanceOf('Generator_Builder', $type->builder());
	}

	/**
	 * If no builder is associated, calling builder() on any Type instance
	 * should throw an exception and halt execution.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_builder_call_requires_associated_builder()
	{
		$type = new Generator_Type_Tester('Foo');
		$type->builder();
	}

} // End Generator_TypeTest 

class Generator_Type_Tester extends Generator_Type {}
