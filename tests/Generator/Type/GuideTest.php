<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_Guide.
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
class Generator_Type_GuideTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$ds = DIRECTORY_SEPARATOR;

		$type = new Generator_Type_Guide();
		$type->name('Guide Menu');
		$this->assertAttributeSame('menu.md', '_name', $type);

		$type->page('Page 1|first');
		$type->page('Page 2|second, Page 3 | third');

		$type->render();
		$params = $type->params();

		$this->assertSame('Guide Menu', $params['menu']);
		$this->assertSame(array('Page 1' => 'first', 'Page 2' => 'second',
			'Page 3' => 'third'), $params['pages']
		);

		$type->guess_filename();
		$this->assertSame(APPPATH.'guide'.$ds.'menu.md', $type->file());

		$type->module('amodule')->verify(FALSE);
		$type->guess_filename();
		$this->assertSame(MODPATH.'amodule'.$ds.'guide'.$ds
			.'amodule'.$ds.'menu.md', $type->file()
		);
	}

} // End Generator_Type_GuideTest 
