<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Clone type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Clone extends Generator_Type_Class
{
	protected $_template = 'generator/type_clone';
	protected $_folder   = 'classes';

	/**
	 * Sets the reflection source to be cloned.
	 *
	 * @param   string  $source  The source class name
	 * @return  Generator_Type_Clone  This instance
	 */
	public function source($source)
	{
		$this->_params['source'] = (string) $source;
		return $this;
	}

	/**
	 * Sets the reflection source type.
	 *
	 * @param   string  $type  The source type
	 * @return  Generator_Type_Clone  This instance
	 */
	public function type($type)
	{
		$this->_params['type'] = (string) $type;
		return $this;
	}

	/**
	 * Adds any properties and method skeletons to the output, converts any
	 * interfaces list to a string and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		$refl = new Generator_Reflector($this->_params['source'], $this->_params['type']);

		// Add any parent info
		$this->extend($refl->get_parent());

		// Add any interface info
		$this->implement($refl->get_interfaces());

		// Add any modifiers info
		$this->_params['modifiers'] = $refl->get_modifiers();

		if ( ! isset($this->_params['blank']))
		{
			// Add any source constants
			$this->_params['constants'] = $this->_get_reflection_constants($this->_params['source'],
				$this->_params['type'], $refl);

			// Add any source properties
			$this->_params['properties'] = $this->_get_reflection_properties($this->_params['source'],
				$this->_params['type'], $refl);

			// Group the properties by modifier
			$this->_params['properties'] = $this->_group_by_modifier($this->_params['properties']);

			// Add any source methods
			$this->_params['methods'] = $this->_get_reflection_methods($this->_params['source'],
				$this->_params['type'], $refl);
		}

		return parent::render();
	}

	/**
	 * Returns reflection details of any source constants to be included in the
	 * template.
	 *
	 * @uses    Generator_Reflector
	 * @param   string|array   $sources  The source names to inspect
	 * @param   string  $type  The inspected source type
	 * @param   Generator_Reflector  The reflector object to use, if any
	 * @return  array   The constants info
	 */
	protected function _get_reflection_constants($sources, $type, Generator_Reflector $reflector = NULL)
	{
		$refl = ($reflector == NULL) ? (new Generator_Reflector) : $reflector;
		$refl->type($type);

		// Start the constants list
		$constants = array();

		foreach ( (array) $sources as $source)
		{
			// Only add constants for known sources
			if ($refl->source($source)->exists())
			{
				foreach (array_keys($refl->get_constants()) as $constant)
				{
					// Create the comment and declaration
					$comment = '// Declared in '.$source;
					$declaration = $refl->get_constant_declaration($constant);

					// Add the constant info
					$constants[$constant] = array(
						'comment' => $comment,
						'declaration' => $declaration,
					);
				}
			}
		}

		return $constants;
	}

	/**
	 * Returns reflection details of any source properties to be included in the
	 * template.
	 *
	 * @uses    Generator_Reflector
	 * @param   string|array   $sources  The source names to inspect
	 * @param   string  $type  The inspected source type
	 * @param   Generator_Reflector  The reflector object to use, if any
	 * @return  array   The properties info
	 */
	protected function _get_reflection_properties($sources, $type, Generator_Reflector $reflector = NULL)
	{
		$refl = ($reflector == NULL) ? (new Generator_Reflector) : $reflector;
		$refl->type($type);

		// Start the properties list
		$properties = array();

		foreach ( (array) $sources as $source)
		{
			// Only add properties for known sources
			if ($refl->source($source)->exists())
			{
				foreach ($refl->get_properties() as $property => $p)
				{
					// Create a doccomment if one doesn't exist
					if (empty($p['doccomment']))
					{
						$doc = View::factory('generator/type_doccomment')->set('tabs', 1);

						// Set the property description
						$doc->set('short_description',  "Declared in {$p['class']}");

						// Set the var tag
						$doc->set('tags', '@var  '.$p['type'].'  $'.$property);

						// Include the rendered doccomment
						$p['doccomment'] = $doc->render();
					}

					// Include the property declaration
					$p['declaration'] = $refl->get_property_declaration($property);

					// Add the property info
					$properties[$property] = $p;
				}
			}
		}

		return $properties;
	}

} // End Generator_Generator_Type_Clone