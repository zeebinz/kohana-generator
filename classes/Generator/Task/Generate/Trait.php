<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating traits, see Task_Generate_Trait for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Trait extends Task_Generate_Class
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'      => '',
		'use'       => '',
		'stub'      => '',
		'blank'     => FALSE,
		'clone'     => '',
		'reflect'   => FALSE,
		'inherit'   => FALSE,
	);

	/**
	 * @var  array  Arguments mapped to options
	 */
	protected $_arguments = array(
		1 => 'name',
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
		if ( ! empty($options['clone']))
		{
			// Get the clone via Task_Generate_Class::get_clone
			$builder = $this->get_clone($options, Generator_Reflector::TYPE_TRAIT);
			$builder->set('category', 'Traits');
		}
		else
		{
			$builder = Generator::build()
				->add_trait($options['name'])
					->using($options['use'])
					->template($options['template'])
					->blank($options['blank'])
				->builder();
		}

		if ($options['stub'])
		{
			$builder->add_trait($options['stub'])
				->template('generator/type/stub')
				->set('source', $options['name']);
		}

		return $builder
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_defaults($this->get_config('defaults.class', $options['config']));
	}

} // End Generator_Task_Generate_Trait
