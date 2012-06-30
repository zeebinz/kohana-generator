<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating Guide pages, see Task_Generate_Guide for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Guide extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name'  => '',
		'pages' => '',
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
		// Choose the folder in which to create the guide files
		$folder = $options['module'] ? 'guide'.DIRECTORY_SEPARATOR.$options['module'] : 'guide';

		// Start by creating the guide menu
		$builder = Generator::build()
			->add_guide($options['name'])
				->folder($folder)
				->page($options['pages'])
			->builder();

		if ($options['pages'])
		{
			// Get any guide page definitions
			$params = $builder->params();
			$pages = $builder->parse_pages($params['pages']);

			foreach ($pages as $title => $file)
			{
				// Add any defined page files
				$builder->add_file($file.'.md')
					->folder($folder)
					->content('# '.$title.PHP_EOL.PHP_EOL.'Content of this page.'.PHP_EOL);
			}
		}

		// Add the index file
		$builder->add_file('index.md')
			->folder($folder)
			->content('# '.$options['name'].PHP_EOL.PHP_EOL.'Content of the index page.'.PHP_EOL);

		if ($options['module'])
		{
			// Add a config file
			$builder->add_config('userguide')
				->template('generator/type_guide_config')
				->set('name', ucfirst($options['name']))
				->set('module', $options['module'])
				->defaults($this->get_config('defaults.guide', $options['config']))
		}

		// Return the builder
		return $builder
			->with_module($options['module'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->prepare();
	}

} // End Generator_Task_Generate_Guide
