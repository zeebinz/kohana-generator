<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Builder.
 *
 * @group      generator
 * @group      generator.builder
 *
 * @package    Generator
 * @category   Tests
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_BuilderTest extends Unittest_TestCase
{
	/**
	 * The build() method is a factory method for the Builder.
	 */
	public function test_build_returns_new_builder_instance()
	{
		$builder_a = Generator::build();
		$builder_b = Generator::build();

		$this->assertInstanceOf('Generator_Builder', $builder_a);
		$this->assertInstanceOf('Generator_Builder', $builder_b);
		$this->assertNotSame($builder_a, $builder_b);
	}

	/**
	 * Undefined methods should throw Generator_Exception.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_undefined_method_throws_exception()
	{
		Generator::build()->some_undefined_method();
	}

	/**
	 * Adding invalid types should throw Generator_Exception.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_adding_invalid_type_throws_exception()
	{
		Generator::build()->add_type('some_invalid_type');
	}

	/**
	 * Successive calls to different methods for adding types
	 * should always return Generator_Type instances.
	 */
	public function test_adding_type_returns_new_type_instance()
	{
		$type = Generator::build()->add_type('class');
		$this->assertInstanceOf('Generator_Type', $type);

		$type = Generator::build()->add_class();
		$this->assertInstanceOf('Generator_Type', $type);

		$type = Generator::build()->add_class()->add_class();
		$this->assertInstanceOf('Generator_Type', $type);

		$type = Generator::build()->add_type(new Generator_Type);
		$this->assertInstanceOf('Generator_Type', $type);
	}

	/**
	 * Created types are stored in the Builder instance.
	 */
	public function test_builder_stores_type_instances()
	{
		$builder = Generator::build()->add_type('class', 'Foo');
		$generators = $builder->generators();

		$this->assertCount(1, $generators);
		$this->assertInstanceOf('Generator_Type', $generators[0]);
	}

	/**
	 * To allow the fluent interface, if undefined methods are called
	 * on the builder and are not of the add_* type, they will be passed
	 * to the last generator added to the builder, if any.
	 */
	public function test_undefined_method_is_called_on_last_added_generator()
	{
		$type = $this->getMock('Generator_Type', array('pretend'));
		$type->expects($this->once())->method('pretend');

		$builder = Generator::build()->add_type($type)->builder();
		$this->assertInstanceOf('Generator_Builder', $builder);
		$builder->pretend();
	}

	/**
	 * Calling a method undefined on a type instance should always
	 * return the builder instance.
	 */
	public function test_undefined_type_method_returns_builder_instance()
	{
		$builder = Generator::build()->add_type('class')->with_pretend();
		$this->assertInstanceOf('Generator_Builder', $builder);
	}

	/**
	 * Methods not defined on either the builder or any added types
	 * should throw Generator_Exception.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_undefined_method_on_builder_or_types_throws_exception()
	{
		$builder = Generator::build()->add_type(new Generator_Type)->builder();
		$this->assertInstanceOf('Generator_Builder', $builder);
		$builder->some_undefined_method();
	}

	/**
	 * The prepare() method sets final configuration on each Type, and
	 * completes essential functions like determining filenames.
	 */
	public function test_prepare()
	{
		$builder = Generator::build()->add_type('class', 'Foo');
		$this->assertAttributeEmpty('_file', $builder);
		$builder->prepare();
		$this->assertAttributeNotEmpty('_file', $builder);
	}

	/**
	 * The inspect() method can be used to view rendered output either
	 * before or after each Type item has been prepared.
	 */
	public function test_inspect()
	{
		$builder = Generator::build()->add_type('class', 'Foo');
		$inspect = $builder->inspect(FALSE);

		$this->assertCount(1, $inspect);
		$this->assertArrayHasKey('file', $inspect[0]);
		$this->assertArrayHasKey('rendered', $inspect[0]);

		$this->assertEmpty($inspect[0]['file']);
		$this->assertEmpty($inspect[0]['rendered']);

		$builder->prepare();
		$inspect = $builder->inspect();

		$this->assertNotEmpty($inspect[0]['file']);
		$this->assertNotEmpty($inspect[0]['rendered']);
	}

	/**
	 * Global settings can be set on each Type via the Builder's with_* methods.
	 */
	public function test_global_settings()
	{
		$builder = Generator::build()->add_type('class', 'Foo')
			->with_defaults(array('package' => 'Tester'))
			->with_module('amodule')
			->with_template('foo.bar')
			->with_pretend(TRUE)
			->with_force(TRUE)
			->with_verify(FALSE);

		$generators = $builder->generators();

		$this->assertAttributeSame(TRUE, '_pretend', $builder);
		$this->assertAttributeSame(TRUE, '_pretend', $generators[0]);

		$this->assertAttributeSame(TRUE, '_force', $builder);
		$this->assertAttributeSame(TRUE, '_force', $generators[0]);

		$this->assertAttributeSame(FALSE, '_verify', $builder);
		$this->assertAttributeSame(FALSE, '_verify', $generators[0]);

		$this->assertAttributeSame('amodule', '_module', $builder);
		$this->assertSame('amodule', $generators[0]->module());

		$this->assertAttributeSame('foo.bar', '_template', $builder);
		$this->assertSame('foo.bar', $generators[0]->template());

		$this->assertContains('Tester', $generators[0]->defaults());
	}

	/**
	 * The execute() method should call create() on each stored item, which
	 * in turn should keep a log of any actions.
	 */
	public function test_execute()
	{
		$builder = Generator::build()->add_type('class', 'Foo')
			->pretend()->execute();

		$generators = $builder->generators();
		$this->assertAttributeNotEmpty('_log', $generators[0]);
	}

	/**
	 * Generating via execute() should produce a status log for each action.
	 */
	public function test_execution_logs()
	{
		$log = Generator::build()->add_type('class', 'Foo')
			->pretend()->execute()->get_log();

		$this->assertCount(2, $log);
		$this->assertArrayHasKey('status', $log[0]);
		$this->assertArrayHasKey('item', $log[0]);
	}

	/**
	 * Executing an empty builder should do nothing.
	 */
	public function test_execute_empty_builder()
	{
		$builder = Generator::build()->prepare()->execute();

		$this->assertEmpty($builder->generators());
		$this->assertEmpty($builder->get_log());
	}

	/**
	 * Generators from different builder instances may be merged into each other,
	 * possibly with the different prepared settings for each. Merged generators
	 * should reference the new builder object.
	 */
	public function test_merging_builders()
	{
		$builder_a = Generator::build()
			->add_type('class', 'Foo')
				->module('baz')
				->verify(FALSE)
			->builder();

		$builder_b = Generator::build()
			->add_type('class', 'Bar')
				->module('qux')
				->verify(FALSE)
			->builder();

		$builder_a->merge($builder_b);

		$generators = $builder_a->generators();
		$this->assertCount(2, $generators);

		$this->assertSame('Foo', $generators[0]->name());
		$this->assertNotEmpty($generators[0]->file());
		$this->assertSame('baz', $generators[0]->module());
		$this->assertSame($builder_a, $generators[0]->builder());

		$this->assertSame('Bar', $generators[1]->name());
		$this->assertNotEmpty($generators[1]->file());
		$this->assertSame('qux', $generators[1]->module());
		$this->assertSame($builder_a, $generators[1]->builder());
	}

} // End Generator_BuilderTest
