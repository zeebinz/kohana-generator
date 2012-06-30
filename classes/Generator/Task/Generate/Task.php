<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating tasks, see Task_Generate_Task for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Task extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'     => '',
		'extend'   => '',
		'stub'     => '',
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
		$builder = Generator::build()
			->add_task($options['name'])
				->extend($options['extend'])
			->builder();

		if ($options['stub'])
		{
			$builder->no_help();
			$parent = $builder->name();

			$builder->add_task($options['stub'])
				->extend($parent)
				->blank();
		}

		return $builder
			->with_module($options['module'])
			->with_template($options['template'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_defaults($this->get_config('defaults.class', $options['config']))
			->prepare();
	}

} // End Generator_Task_Generate_Task
