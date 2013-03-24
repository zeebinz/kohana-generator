<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Test case for Generator_View.
 *
 * @group      generator
 * @group      generator.view
 *
 * @package    Generator
 * @category   Tests
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_ViewTest extends Unittest_TestCase
{
	protected $_template_dir;

	/**
	 * This method is called before each test is run.
	 */
	public function setUp()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->_template_dir = dirname(dirname(dirname(__FILE__))).$ds.'views'.$ds;
		parent::setUp();
	}

	/**
	 * We should be able to specify absolute paths to template files so that
	 * tests don't fail due to templates being defined elsewhere in the CFS.
	 */
	public function test_absolute_paths_can_be_set_for_template_files()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = 'generator/type/class';
		$view = Generator_View::factory();

		// With an existing templates directory and file
		$view->set_filename($file, $this->_template_dir);
		$this->assertAttributeSame($this->_template_dir.$file.EXT, '_file', $view);

		// With an arbitrary directory and file
		$view->set_filename('ViewTest', __DIR__.$ds);
		$this->assertAttributeSame(__DIR__.$ds.'ViewTest'.EXT, '_file', $view);

		// If the file doesn't exist in the given directory, we should be able
		// to fall back to searching the CFS
		$view->set_filename($file, __DIR__.$ds);
		$this->assertAttributeSame($this->_template_dir.$file.EXT, '_file', $view);
	}

	/**
	 * Missing template files should fall back to handling by the CFS, throwing
	 * an exception if they still can't be found.
	 *
	 * @expectedException View_Exception
	 */
	public function test_missing_template_file_throws_exception()
	{
		Generator_View::factory()
			->set_filename('missingtemplate', $this->_template_dir);
	}

} // End Generator_ViewTest
