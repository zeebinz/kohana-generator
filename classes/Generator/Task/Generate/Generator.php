<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating generators, see Task_Generate_Generator for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Generator extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'extend'  => '',
		'prefix'  => '',
		'no-stub' => FALSE,
		'no-task' => FALSE,
		'no-test' => FALSE,
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
	 * @param   array  $options  The selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder(array $options)
	{
		// Set any class prefix, default is the module name
		$prefix = $options['prefix'] ?: ucfirst($options['module']);

		$builder = Generator::build()
			->add_generator($options['name'])
				->extend($options['extend'])
				->template($options['template'])
			->builder(); // Return the builder instance

		if ($options['module'] AND ! $options['no-stub'])
		{
			// Prefix the generator name
			$name = $prefix.'_'.$builder->name();
			$builder->name($name);

			// Add a stub to extend the generator transparently
			$builder->add_generator($options['name'])
				->template($options['template'])
				->extend($name)
				->blank();
		}

		if ( ! $options['no-task'])
		{
			$builder->add_task('Generate_'.$options['name'])
				->template('generator/type_task_generator')
				->extend('Task_Generate');

			if ($options['module'] AND ! $options['no-stub'])
			{
				// Prefix the task name
				$name = $prefix.'_'.$builder->name();
				$builder->name($name)
					->set('category', 'Generator/Tasks')
					->no_help();

				// Add a stub to extend the task transparently
				$builder->add_task('Generate_'.$options['name'])
					->extend($name)
					->blank();
			}
		}

		if ( ! $options['no-test'])
		{
			$builder->add_unittest('Generator_Type_'.$options['name'])
				->group('generator')
				->group('generator.types');
		}

		return $builder
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_defaults($this->get_config('defaults.class', $options['config']));
	}

} // End Generator_Task_Generate_Generator
