<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Task for generating template controllers with views, for usage see
 * Task_Generate_Controller_View.
 *
 * @package    Generator
 * @category   Generator/Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Task_Generate_Controller_View extends Task_Generate_Controller
{
	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param   array  $options  The selected task options
	 * @return  Generator_Builder
	 */
	public function get_builder(array $options)
	{
		$ds = DIRECTORY_SEPARATOR;
		$view = str_replace('_', $ds, (str_replace('Controller_', '', $options['name'])));
		$view = strtolower($view);

		// Configure the template and add the view file
		return parent::get_builder($options)
			->template('generator/type/controller_view')
				->set('view', $view)
			->add_file($view.EXT)
				->folder('views')
				->content('View for '.$options['name'].' controller'.PHP_EOL)
				->pretend($options['pretend'])
				->force($options['force'])
			->builder();
	}

} // End Generator_Task_Generate_Controller_View
