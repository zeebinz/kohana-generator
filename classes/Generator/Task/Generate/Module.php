<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating module skeletons, see Task_Generate_Module for usage.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Module extends Task_Generate
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name' => '',
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
		$ds = DIRECTORY_SEPARATOR;

		return Generator::build()

			// Start with an empty init file
			->add_file('init.php')
				->content(Kohana::FILE_SECURITY.PHP_EOL)

			// Readme and license files
			->add_file('README.md')
				->content('# '.ucfirst($options['name']).' module'.PHP_EOL.PHP_EOL
					.'Information about this module.'.PHP_EOL)
			->add_file('LICENSE')
				->content('License info'.PHP_EOL)

			// Guide pages and config
			->add_guide(ucfirst($options['name']))
			->add_file('index.md')
				->folder('guide'.$ds.$options['name'])
				->content('# '.ucfirst($options['name']).' module'.PHP_EOL.PHP_EOL
					.'Index page for this module.'.PHP_EOL)
			->add_config('userguide')
				->template('generator/type_guide_config')
				->set('name', ucfirst($options['name']))
				->set('module', $options['name'])
				->set('copyright', '(c) Copyright')

			// Basic directory structure
			->add_directory('classes')
			->add_directory('tests')

			// Apply global settngs
			->with_module($options['name'])
			->with_pretend($options['pretend'])
			->with_force($options['force'])
			->with_verify(FALSE)
			->prepare();
	}

} // End Generator_Task_Generate_Module
