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
		if ( ! isset($this->_params['blank']) AND ! isset($this->_params['methods']))
		{
			$this->_params['methods'] = array();
		}

		if ( ! empty($this->_params['implements']) AND is_array($this->_params['implements']))
		{
			if ( ! isset($this->_params['blank']))
			{
				// Merge any class and interface methods
				$this->_params['methods'] = array_merge($this->_params['methods'],
					$this->_get_reflection_methods($this->_params['implements'], Generator_Reflector::TYPE_INTERFACE)
				);

				// Group the methods by modifier
				$this->_params['methods'] = $this->_group_by_modifier($this->_params['methods']);
			}

			// Convert the interfaces list
			$this->_params['implements'] = implode(', ', $this->_params['implements']);
		}

		return parent::render();
	}

	/**
	 * Returns reflection details of the source methods to be implemented,
	 * allowing the generation of basic skeleton methods.
	 *
	 * @uses    Generator_Reflector
	 * @param   string|array   $sources  The source names to inspect
	 * @param   string  $type  The inspected source type
	 * @param   Generator_Reflector  The reflector object to use, if any
	 * @return  array   Details of the methods to be implemented
	 */
	protected function _get_reflection_methods($sources, $type, Generator_Reflector $reflector = NULL)
	{
		$refl = ($reflector == NULL) ? (new Generator_Reflector) : $reflector;
		$refl->type($type);

		// Start the methods list
		$methods = array();

		foreach ( (array) $sources as $source)
		{
			// Only add skeleton methods for known sources
			if ($refl->source($source)->exists())
			{
				foreach ($refl->get_methods() as $method => $m)
				{
					// Create a doccomment if one doesn't exist
					if (empty($m['doccomment']))
					{
						$doc = View::factory('generator/type_doccomment')->set('tabs', 1);
						$tags = array();

						// Set the method description
						$doc->set('short_description',  "Implementation of {$m['class']}::{$method}");

						foreach ($m['params'] as $param => $p)
						{
							// Add the parameter tags
							$tags[] = '@param   '.$p['type'].'  $'.$param;
						}

						// Add the return tag
						$tags[] = '@return  void  **This line should be edited**';

						// Include the rendered doccomment
						$m['doccomment'] = $doc->set('tags', $tags)->render();
					}

					// Get the full method signature
					$sig = $refl->get_method_signature($method);

					// Include the signature
					$m['signature'] = $sig;

					// Add the method info
					$methods[$method] = $m;
				}
			}
		}

		return $methods;
	}

	/**
	 * Indexes a set of reflection values (e.g. methods, properties) by their
	 * modifier types for proper grouping in the template.
	 *
	 * @param   array  $source  The reflection values to group
	 * @return  array  The grouped list
	 */
	protected function _group_by_modifier(array $source)
	{
		$grouped = array();

		foreach ($source as $key => $value)
		{
			if (strpos($value['modifiers'], 'static') !== FALSE)
			{
				$grouped['static'][$key] = $value;
			}
			elseif (strpos($value['modifiers'], 'abstract') !== FALSE)
			{
				$grouped['abstract'][$key] = $value;
			}
			elseif (strpos($value['modifiers'], 'public') !== FALSE)
			{
				$grouped['public'][$key] = $value;
			}
			else
			{
				$grouped['other'][$key] = $value;
			}
		}

		return $grouped;
	}

} // End Generator_Generator_Type_Class
