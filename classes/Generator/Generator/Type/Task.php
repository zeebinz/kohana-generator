<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Task type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Task extends Generator_Type_Class
{
	protected $_template = 'generator/type/task';

	/**
	 * Sets/gets the task class name.
	 *
	 * @param   string  $name  The task name
	 * @return  string|Generator_Type_Task  The current name or this instance
	 */
	public function name($name = NULL)
	{
		if ($name == NULL)
			return $this->_name;

		// Prepend 'Task_' to the class name if not already present
		$this->_name = (strpos($name, 'Task_') === FALSE) ? ('Task_'.$name) : $name;

		return $this;
	}

	/**
	 * Determines if the task help comments should not be included in
	 * this task class file.
	 *
	 * @return  Generator_Type_Task  The current instance
	 */
	public function no_help()
	{
		$this->_params['help'] = FALSE;

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
			$this->_params['category'] = 'Tasks';
		}

		if (empty($this->_params['extends']))
		{
			$this->_params['extends'] = 'Minion_Task';
		}

		if ( ! isset($this->_params['help']))
		{
			$this->_params['help'] = TRUE;
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Task
