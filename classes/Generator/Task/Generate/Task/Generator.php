<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for creating generator tasks, see Task_Generate_task_Generator for
 * usage and examples.
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Task_Generator extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'    => '',
		'extend'  => '',
		'prefix'  => '',
		'no-stub' => FALSE,
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
	 * Loads any view parameter defaults from config.
	 *
	 * @param  array  $options  The selected task options
	 * @return array  The default values
	 */
	public function get_defaults(array $options = NULL)
	{
		$config = ! empty($options['config']) ? $options['config'] : 'generator';

		if ($defaults = Kohana::$config->load($config.'.defaults.class'))
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
		// Set any class prefix, default is the module name
		$prefix = $options['prefix'] ?: ucfirst($options['module']);

		// Set the default template and extension
		$template = $options['template'] ?: 'generator/type_task_generator';
		$extend = $options['extend'] ?: 'Task_Generate';

		$builder = Generator::build()
			->add_task('Generate_'.$options['name'])
				->extend($extend);

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

		return $builder
			->with_template($template)
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_defaults($this->get_defaults($options))
			->prepare();
	}

	/**
	 * Executes the task.
	 *
	 * @param  array  $params  The task parameters
	 * @return void
	 */
	protected function _execute(array $params)
	{
		$builder = $this->get_builder($params);
		$this->run($builder, $params);
	}

} // End Generator_Task_Generate_Task_Generator
