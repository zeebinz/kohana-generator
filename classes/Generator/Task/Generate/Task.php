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
	 * @param  Validation  $validation  the validation object to add rules to	 
	 * @return Validation
	 */
	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('name', 'not_empty');
	}

	/**
	 * Loads any view parameter defaults from config.
	 *
	 * @return array
	 */
	public function get_defaults()
	{
		if ($defaults = Kohana::$config->load('generator.defaults.class'))
			return $defaults;

		return array();
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
			->with_defaults($this->get_defaults())
			->prepare();
	}

	/**
	 * Executes the task.
	 *
	 * @param  array  $params  the task parameters
	 * @return void
	 */
	protected function _execute(array $params)
	{
		$builder = $this->get_builder($params);
		$this->run($builder, $params);
	}

} // End Generator_Task_Generate_Task
