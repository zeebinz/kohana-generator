<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Class type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Class extends Generator_Type
{
	protected $_template = 'generator/type_class';
	protected $_folder   = 'classes';

	protected $_defaults = array(
		'package'   => 'package',
		'category'  => 'category',
		'author'    => 'author',
		'copyright' => 'copyright',
		'license'   => 'license',
	);

	/**
	 * Sets whether the class should be defined as abstract.
	 *
	 * @param   bool  $abstract  Is the class abstract?
	 * @return  Generator_Type_Class  The current instance
	 */
	public function as_abstract($abstract = TRUE)
	{
		$this->_params['abstract'] = (bool) $abstract;
		return $this;
	}

	/**
	 * Sets any parent class that this class should extend.
	 *
	 * @param   string  $class  The parent class name
	 * @return  Generator_Type_Class  This instance
	 */
	public function extend($class)
	{
		$this->_params['extends'] = (string) $class;
		return $this;
	}

	/**
	 * Adds any interfaces that this class should be defined as implementing.
	 *
	 * The interfaces may be passed either as an array, comma-separated list
	 * of interface names, or as a single interface name.
	 *
	 * @param   string|array  $interfaces  The interface names to implement
	 * @return  Generator_Type_Class  This instance
	 */
	public function implement($interfaces)
	{
		$this->param_to_array($interfaces, 'implements');
		return $this;
	}

	/**
	 * Converts the interfaces list to a string, adds any method skeletons to be
	 * be implemented by the class and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if ( ! empty($this->_params['implements']) AND is_array($this->_params['implements']))
		{
			if ( ! isset($this->_params['blank']))
			{
				$this->_params['methods'] = $this->_get_interface_methods($this->_params['implements']);
			}

			$this->_params['implements'] = implode(', ', $this->_params['implements']);
		}

		return parent::render();
	}

	/**
	 * Returns reflection details of the interface methods to be implemented,
	 * allowing the generation of basic skeleton methods.
	 *
	 * @uses    Generator_Reflector
	 * @param   array  $interfaces  The interface names to implement
	 * @return  array  Details of the methods to be implemented
	 */
	protected function _get_interface_methods(array $interfaces)
	{
		$refl = new Generator_Reflector;
		$refl->type(Generator_Reflector::TYPE_INTERFACE);

		// Start the methods list
		$methods = array();

		foreach ($interfaces as $interface)
		{
			// Only add skeleton methods for known interfaces
			if ($refl->source($interface)->exists())
			{
				foreach($refl->get_methods() as $method => $info)
				{
					// Include the interface name
					$info['interface'] = $interface;

					// Create a doccomment if one doesn't exist
					if (empty($info['doccomment']))
					{
						$doc = View::factory('generator/type_doccomment')->set('tabs', 1);
						$tags = array();

						// Set the method description
						$doc->set('short_description',  "Implementation of {$interface}::{$method}");

						if (! empty($info['params'])) foreach ($info['params'] as $param => $p)
						{
							// Add the parameter tags
							$tags[] = '@param   '.$p['type'].'  $'.$param;
						}

						// Add the return tag
						$tags[] = '@return  void  **This line should be edited**';

						// Include the rendered doccomment
						$info['doccomment'] = $doc->set('tags', $tags)->render();
					}

					// Include the full method signature
					$info['signature'] = str_replace('abstract ', '', $refl->get_method_signature($method));

					// Add the method info
					$methods[$method] = $info;
				}
			}
		}

		return $methods;
	}

} // End Generator_Generator_Type_Class
