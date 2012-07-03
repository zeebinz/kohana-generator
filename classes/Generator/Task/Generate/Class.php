<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating classes, see Task_Generate_Class for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Class extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'      => '',
		'extend'    => '',
		'implement' => '',
		'stub'      => '',
		'abstract'  => FALSE,
		'no-test'   => FALSE,
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
	 * @param  array  $options  The selected task options
	 * @return Generator_Builder
	 */
	public function get_builder(array $options)
	{
		$builder = Generator::build()
			->add_class($options['name'])
				->as_abstract(($options['abstract']))
				->extend($options['extend'])
				->implement($options['implement'])
				->template($options['template'])
			->builder();

		if ($options['stub'])
		{
			$builder->add_class($options['stub'])
				->extend($options['name'])
				->template($options['template'])
				->blank();
		}

		if ( ! $options['no-test'])
		{
			$name = $options['stub'] ? $builder->name() : $options['name'];
			$builder->add_unittest($name)
				->group($options['module']);
		}

		return $builder
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_defaults($this->get_config('defaults.class', $options['config']))
			->prepare();
	}

} // End Generator_Task_Generate_Class
