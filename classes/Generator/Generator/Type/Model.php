<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Model type.
 *
 * @package    Generator 
 * @category   Generator/Types 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_Generator_Type_Model extends Generator_Type_Class 
{
	protected $_template = 'generator/type_class';
	protected $_folder   = 'classes';

	/**
	 * Sets/gets the model class name.
	 *
	 * @param   string  $name  The model name
	 * @return  string|Generator_Type_Task  The current name or this instance
	 */
	public function name($name = NULL)
	{
		if ($name == NULL)
			return $this->_name;

		// Prepend 'Model_' to the class name if not already present
		$this->_name = (strpos($name, 'Model_') === FALSE) ? ('Model_'.$name) : $name;

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
			$this->_params['category'] = 'Models';
		}

		if (empty($this->_params['extends']))
		{
			$this->_params['extends'] = 'Model';
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Model 
