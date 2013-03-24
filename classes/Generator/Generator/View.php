<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator View class.
 *
 * The modifications here allow setting absolute paths to view templates, useful
 * mainly for ensuring that test fixtures can be reproduced properly from templates
 * in a defined location rather than relying on the CFS to find them and potentially
 * breaking the tests.
 *
 * @package    Generator
 * @category   Views
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_View extends Kohana_View
{
	/**
	 * Returns a new Generator_View object.
	 *
	 * @param   string  $file   view filename
	 * @param   array   $data   array of values
	 * @return  Generator_View
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new Generator_View($file, $data);
	}

	/**
	 * Sets the view filename, optionally checking a defined templates directory
	 * first by absolute path before searching the CFS.
	 *
	 * @param   string  $file  View filename
	 * @param   string  $path  Absolute path to a templates directory
	 * @return  Generator_View
	 * @throws  View_Exception
	 */
	public function set_filename($file, $path = NULL)
	{
		if ($path AND is_file($path.$file.EXT))
		{
			// Use the absolute path
			$this->_file = $path.$file.EXT;
			return $this;
		}

		// Search the CFS instead
		return parent::set_filename($file);
	}

} // End Generator_Generator_View
