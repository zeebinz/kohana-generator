<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Reflector.
 *
 * @group      generator
 * @group      generator.reflector
 *
 * @package    Generator
 * @category   Tests
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_ReflectorTest extends Unittest_TestCase
{
	/**
	 * Sources can be set via constructor or by setter method that also acts as
	 * a getter, and any stored values should always be reset.
	 */
	public function test_setting_source()
	{
		$refl = new TestReflector('TestInterface');

		$this->assertSame('TestInterface', $refl->source());
		$this->assertAttributeEmpty('_info', $refl);
		$this->assertFalse($refl->is_analyzed());

		$p = new ReflectionProperty('Generator_Reflector', '_info');
		$p->setAccessible(TRUE);
		$p->setValue($refl, array('foo'));
		$this->assertAttributeNotEmpty('_info', $refl);
		$this->assertTrue($refl->is_analyzed());

		$this->assertInstanceOf('Generator_Reflector', $refl->source('SomeSource'));
		$this->assertSame('SomeSource', $refl->source());
		$this->assertAttributeEmpty('_info', $refl);
		$this->assertFalse($refl->is_analyzed());
	}

	/**
	 * The existence of sources should be checked differently depending on their
	 * types - class, interface, file, etc.
	 */
	public function test_source_exists()
	{
		$refl = new TestReflector('TestInterface', Generator_Reflector::TYPE_INTERFACE);

		$this->assertTrue($refl->exists());
		$refl->source('TestInterface')->type(Generator_Reflector::TYPE_CLASS);
		$this->assertFalse($refl->exists());

		$refl->source('TestReflector')->type(Generator_Reflector::TYPE_CLASS);
		$this->assertTrue($refl->exists());
		$refl->source('TestReflector')->type(Generator_Reflector::TYPE_INTERFACE);
		$this->assertFalse($refl->exists());
	}

	/**
	 * Sources should only be inspected once per run or each time that
	 * analyze() is called, and the method should also be chainable.
	 */
	public function test_analyze()
	{
		$refl = new TestReflector('TestInterface');

		$this->assertFalse($refl->is_analyzed());

		$refl->get_methods();
		$this->assertTrue($refl->is_analyzed());
		$this->assertSame(1, $refl->analysis_count);

		$refl->get_methods();
		$this->assertTrue($refl->is_analyzed());
		$this->assertSame(1, $refl->analysis_count);

		$this->assertSame($refl, $refl->analyze());
		$this->assertTrue($refl->is_analyzed());
		$this->assertSame(2, $refl->analysis_count);
	}

	/**
	 * Most methods need a source to work with.
	 *
	 * @expectedException Generator_Exception
	 */
	public function test_missing_source_throws_exception()
	{
		$refl = new TestReflector;
		$refl->analyze();
	}

	/**
	 * The method signature should be returned as a string, with any array
	 * parameter values parsed recursively.
	 */
	public function test_get_method_signature()
	{
		$refl = new TestReflector('TestInterface');

		$expected = 'abstract public function method_one(SomeClass $class, $foo = \'foo\', '
			.'array $bar = array(\'bar1\', \'bar2\', \'bar3\' => FALSE, \'bar4\' => array(1, \'foo\' => \'bar\')), '
			.'$bool = FALSE)';
		$actual = $refl->get_method_signature('method_one');
		$this->assertSame($expected, $actual);

		$expected = 'abstract public function & method_two(OtherClass $class, & $foo = NULL, array $bar = NULL)';
		$actual = $refl->get_method_signature('method_two');
		$this->assertSame($expected, $actual);
	}

} // End Generator_ReflectorTest

interface TestInterface
{
	/**
	 * Short description.
	 */
	public function method_one(SomeClass $class, $foo = 'foo',
		array $bar = array('bar1', 'bar2', 'bar3' => FALSE, 'bar4' => array(1, 'foo' => 'bar')),
		$bool = FALSE);

	public function &method_two(OtherClass $class, &$foo = NULL, array $bar = NULL);
}

class TestReflector extends Generator_Reflector
{
	public $analysis_count = 0;

	public function analyze()
	{
		$this->analysis_count++;
		return parent::analyze();
	}
}
