<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Unittest type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Unittest extends Generator_Type_Class
{
	protected $_template = 'generator/type/unittest';
	protected $_folder   = 'tests';

	/**
	 * Sets/gets the test name.
	 *
	 * @param   string  $name  The test name
	 * @return  string|Generator_Type_Unittest  The current name or this instance
	 */
	public function name($name = NULL)
	{
		if ($name == NULL)
			return $this->_name;

		// Append 'Test' to the class name if not already present
		$name = (substr($name, -4) !== 'Test') ? ($name.'Test') : $name;
		$this->_name = $name;

		// Store the class name without the 'Test' suffix
		$this->_params['class_name'] = substr($name, 0, -4);

		return $this;
	}

	/**
	 * Adds any groups to which this test case belongs.
	 *
	 * The groups may be passed either as an array, comma-separated list
	 * of group names, or as a single group name.
	 *
	 * @param   string|array  $groups    The group names
	 * @return  Generator_Type_Unittest  This instance
	 */
	public function group($groups)
	{
		$this->param_to_array($groups, 'groups');
		return $this;
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if (empty($this->_params['category']))
		{
			$this->_params['category'] = 'Tests';
		}

		if (empty($this->_params['groups']))
		{
			$this->_params['groups'][] = 'group';
		}

		if (empty($this->_params['extends']))
		{
			$this->_params['extends'] = 'Unittest_TestCase';
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Unittest
