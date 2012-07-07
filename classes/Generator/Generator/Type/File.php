<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator File type.
 *
 * This is a special type that doesn't use view templates to render output, so
 * it can be used by a builder to create files and their contents on the fly
 * in any location.
 *
 * @package    Generator 
 * @category   Generator/Types 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_Generator_Type_File extends Generator_Type 
{
	/**
	 * The destination folder for the file
	 * @var string
	 */
	protected $_folder;

	/**
	 * The content of the created file
	 * @var string
	 */
	protected $_content;

	/**
	 * Sets/gets the generated file content.
	 *
	 * @param   string  $content  The file content
	 * @return  string|Generator_Type_File  The file content or this instance
	 */
	public function content($content)
	{
		if ($content === NULL)
			return $this->_content;

		$this->_content = $content;

		return $this;
	}

	/**
	 * Ensures that the filename is not guessed by converting the name to 
	 * a path, replacing underscores, etc.
	 *
	 * @throws  Generator_Exception  On invalid name or base path
	 * @param   bool    $convert  Should the name be converted to a file path?
	 * @return  string  The guessed filename
	 */
	public function guess_filename($convert = TRUE)
	{
		return parent::guess_filename(FALSE);
	}

	/**
	 * As we're not using templates, we just need to return the given file
	 * contents directly here.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return $this->_content;
	}

} // End Generator_Generator_Type_File 
