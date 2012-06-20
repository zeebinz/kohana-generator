<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_Type_File.
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
class Generator_Type_FileTest extends Unittest_TestCase 
{
	/**
	 * Tests that all type options are applied correctly.
	 */
	public function test_type_options()
	{
		$ds = DIRECTORY_SEPARATOR;

		$filename = 'foo_bar.php';
		$type = new Generator_Type_File();

		// Name should not be converted to a file path
		$type->name($filename);
		$expected = APPPATH.$filename;
		$type->guess_filename();
		$this->assertSame($expected, $type->file());

		$type->folder('tests');
		$type->name($filename);
		$expected = APPPATH.'tests'.$ds.$filename;
		$type->guess_filename();
		$this->assertSame($expected, $type->file());

		// Content is rendered directly, not from a template
		$content = 'Test file content';
		$type->content($content);
		$this->assertEmpty($type->template());
		$this->assertSame($content, $type->render());
	}

} // End Generator_Type_FileTest 
