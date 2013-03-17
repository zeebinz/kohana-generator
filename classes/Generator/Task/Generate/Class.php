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
		'use'       => '',
		'stub'      => '',
		'abstract'  => FALSE,
		'no-test'   => FALSE,
		'blank'     => FALSE,
		'clone'     => '',
		'reflect'   => FALSE,
		'inherit'   => FALSE,
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
			$builder = $this->get_clone($options);
		}
		else
		{
			$builder = Generator::build()
				->add_class($options['name'])
					->as_abstract(($options['abstract']))
					->extend($options['extend'])
					->implement($options['implement'])
					->using($options['use'])
					->template($options['template'])
					->blank($options['blank'])
				->builder();
		}

		if ($options['stub'])
		{
			$builder->add_class($options['stub'])
				->as_abstract(($options['abstract']))
				->extend($options['name'])
				->template('generator/type/stub')
				->set('source', $options['name']);
		}

		if ( ! $options['no-test'])
		{
			$name = $options['stub'] ? $builder->name() : $options['name'];
			$builder->add_unittest($name)
				->group($options['module'])
				->blank($options['blank']);
		}

		return $builder
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_defaults($this->get_config('defaults.class', $options['config']));
	}

	/**
	 * Creates a generator builder that clones an existing class, either from
	 * an existing file or from an internal class definition.
	 *
	 * @param   array  $options  The selected task options
	 * @param   array  $type     The source type to clone
	 * @return  Generator_Builder
	 */
	public function get_clone(array $options, $type = Generator_Reflector::TYPE_CLASS)
	{
		// Convert the cloned class name to a filename
		$source = str_replace('_', DIRECTORY_SEPARATOR, $options['clone']);

		if ( ! $options['reflect'] AND ($file = Kohana::find_file('classes', $source)))
		{
			// Use the existing class file
			$content = file_get_contents($file);

			// Replace the class name references
			$content = preg_replace("/\b{$options['clone']}\b/", $options['name'], $content);

			// Convert the generated class name to a filename
			$destination = str_replace('_', DIRECTORY_SEPARATOR, $options['name']).EXT;

			// Create the Builder
			$builder = Generator::build()
				->add_file($destination)
					->folder('classes')
					->content($content)
				->builder();
		}
		else
		{
			// Use the internal class definition via reflection
			$builder = Generator::build()
				->add_clone($options['name'])
					->source($options['clone'])
					->type($type)
					->inherit($options['inherit'])
				->builder();
		}

		return $builder;
	}

} // End Generator_Task_Generate_Class
