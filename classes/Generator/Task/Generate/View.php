<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating unit view templates, see Task_Generate_View for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_View extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'   => '',
	);

	/**
	 * Validates the task options.
	 *
	 * @param  Validation  $validation  The validation object to add rules to
	 * @return Validation
	 */
	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('name', 'not_empty');
	}

	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param  array  $options  the selected task options
	 * @return Generator_Builder
	 */
	public function get_builder(array $options)
	{
		return Generator::build()
			->add_file($options['name'].EXT)
				->folder('views')
				->module($options['module'])
				->pretend($options['pretend'])
				->force($options['force'])
				->content('Content of view '.$options['name'].PHP_EOL)
			->builder();
	}

} // End Generator_Task_Generate_View
