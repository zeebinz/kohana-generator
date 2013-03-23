<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating message files, see Task_Generate_Message for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Message extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'values'  => '',
		'import'  => '',
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
		2 => 'values',
	);

	/**
	 * Validates the task options.
	 *
	 * @param   Validation  $validation  The validation object to add rules to
	 * @return  Validation
	 */
	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('name', 'not_empty');
	}

	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param   array  $options  the selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder(array $options)
	{
		return Generator::build()
			->add_message($options['name'])
				->value($options['values'])
				->import($options['import'])
				->template($options['template'])
				->module($options['module'])
				->pretend($options['pretend'])
				->force($options['force'])
			->builder();
	}

} // End Generator_Task_Generate_Message
