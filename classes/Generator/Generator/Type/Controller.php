<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Controller type.
 *
 * @package    Generator 
 * @category   Generator/Types 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_Generator_Type_Controller extends Generator_Type_Class
{
	protected $_template = 'generator/type/controller';
	protected $_folder   = 'classes';

	/**
	 * Sets/gets the controller name.
	 *
	 * @param   string  $name  The controller name
	 * @return  string|Generator_Type_Controller  The current name or this instance
	 */
	public function name($name = NULL)
	{
		if ($name == NULL)
			return $this->_name;

		// Prepend 'Controller_' to the class name if not already present
		$this->_name = (strpos($name, 'Controller_') === FALSE) ? ('Controller_'.$name) : $name;

		return $this;
	}

	/**
	 * Adds any actions for this controller.
	 *
	 * The actions may be passed either as an array, comma-separated list
	 * of action names, or as a single group name.
	 *
	 * @param   string|array  $actions     The action names
	 * @return  Generator_Type_Controller  This instance
	 */
	public function action($actions)
	{
		$this->param_to_array($actions, 'actions');
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
			$this->_params['category'] = 'Controllers';
		}

		if (empty($this->_params['extends']))
		{
			$this->_params['extends'] = 'Controller';
		}

		if (empty($this->_params['actions']))
		{
			$this->_params['actions'][] = 'index';
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Controller 
