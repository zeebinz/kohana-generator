<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Guide type, creates a Guide menu file from page definitions.
 *
 * @package    Generator 
 * @category   Generator/Types 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_Generator_Type_Guide extends Generator_Type 
{
	protected $_template = 'generator/type_guide_menu';
	protected $_name     = 'menu.md';
	protected $_folder   = 'guide';

	// Don't add the security string
	protected $_security = FALSE;

	/**
	 * Sets/gets the generator name.
	 *
	 * Here we want to divert the calls to set/get the top menu item, so that
	 * $_name can be used instead for the menu filename.
	 *
	 * @param   string  $name  The top menu item
	 * @return  string|Generator_Type_Guide  The top menu item or this instance
	 */
	public function name($name = NULL)
	{
		if ($name === NULL)
			return (isset($this->_params['menu']) ? $this->_params['menu'] : '');

		$this->_params['menu'] = $name;
		return $this;
	}

	/**
	 * Sets any page items for the menu.
	 *
	 * The items should be passed in the form "Page Title|filename", and may
	 * be in an array or comma-separated lists.
	 *
	 * @param   string|array  $pages    The page definitions
	 * @return  Generator_Type_Guide  This instance
	 */
	public function page($pages = NULL)
	{
		$this->param_to_array($pages, 'pages');
		return $this;
	}

	/**
	 * Ensures that the filename is not guessed by converting the name to 
	 * a path, replacing underscores, etc.
	 *
	 * @param   bool    $convert  Should the name be converted to a file path?
	 * @throws  Generator_Exception  On invalid name or base path
	 * @return  string  The guessed filename
	 */
	public function guess_filename($convert = TRUE)
	{
		if ($this->_module AND strpos($this->_folder, $this->_module) === FALSE)
		{
			// Add the module name to the path
			$this->_folder .= DIRECTORY_SEPARATOR.$this->_module;
		}

		return parent::guess_filename(FALSE);
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if ( ! empty($this->_params['pages']))
		{
			$this->_params['pages'] = $this->_parse_pages($this->_params['pages']);
		}

		if (empty($this->_params['menu']))
		{
			$this->_params['menu'] = 'Guide Menu';
		}

		return parent::render();
	}

	/**
	 * Converts an array of page definition strings into a final array of 
	 * menu items.
	 *
	 * @param   array  $pages  The list of page definitions
	 * @return  array  The parsed list
	 */
	protected function _parse_pages(array $pages)
	{
		$ret = array();

		foreach ($pages as $page)
		{
			list($title, $file) = explode('|', $page);
			$ret[trim($title)] = trim($file);
		}

		return $ret;
	}

} // End Generator_Generator_Type_Guide 
