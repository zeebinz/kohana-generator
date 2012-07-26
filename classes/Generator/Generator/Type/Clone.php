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
	protected $_template = 'generator/type/clone';
	protected $_folder   = 'classes';

	protected $_inherit = FALSE;

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
	 * Sets whether the clone should include inherited properties and methods.
	 *
	 * @param   boolean  $inherit  Should the clone inherit?
	 * @return  Generator_Type_Clone  This instance
	 */
	public function inherit($inherit = TRUE)
	{
		$this->_inherit = (bool) $inherit;
		return $this;
	}

	/**
	 * Adds any properties and method skeletons to the output, converts any
	 * interfaces list to a string and renders the template.
	 *
	 * @return  string  The rendered output
	 * @throws  Generator_Exception  On missing source to clone
	 * @uses    Generator_Reflector
	 */
	public function render()
	{
		$source = $this->_params['source'];
		$type   = $this->_params['type'];
		$refl   = new Generator_Reflector($source, $type);

		if ( ! $refl->exists())
		{
			// We need an existing source matched correctly to the type
			throw new Generator_Exception(":type ':source' does not exist", array(
				':type' => ucfirst($type), ':source' => $source));
		}

		// Is the class abstract?
		$this->_params['abstract'] = $refl->is_abstract();

		// Get any interfaces
		$interfaces = $refl->get_interfaces();

		if ($refl->is_interface())
		{
			// Interfaces support multiple inheritance by extension
			$this->extend(implode(', ', $interfaces));
		}
		else
		{
			// Add any class parent name
			$this->extend($refl->get_parent());

			// Add any interfaces to implement
			$this->implement($interfaces);
		}

		// Add any modifiers info
		$this->_params['modifiers'] = $refl->get_modifiers();

		if ( ! isset($this->_params['blank']))
		{
			// Add any source traits
			$this->_params['traits'] = $refl->get_traits();

			// Add any source constants
			$this->_params['constants'] = $this->_get_reflection_constants(
				$source, $type, $refl);

			// Add any source properties
			$props = $this->_get_reflection_properties($source, $type, $refl);

			// Group the properties by modifier
			$this->_params['properties'] = $this->_group_by_modifier($props);

			// Add any source methods
			$this->_params['methods'] = $this->_get_reflection_methods(
				$source, $type, $this->_inherit, $refl);
		}

		return parent::render();
	}

	/**
	 * Returns reflection details of any source constants to be included in the
	 * template.
	 *
	 * @param   string|array  $sources  The source names to inspect
	 * @param   string        $type     The inspected source type
	 * @param   Generator_Reflector  $reflector  The reflector object to use, if any
	 * @return  array   The constants info
	 */
	protected function _get_reflection_constants($sources, $type,
		Generator_Reflector $reflector = NULL)
	{
		$refl = ($reflector !== NULL) ? $reflector : (new Generator_Reflector);
		$refl->type($type);

		// Start the constants list
		$constants = array();

		foreach ( (array) $sources as $source)
		{
			// Only add constants for known sources
			if ($refl->source($source)->exists())
			{
				foreach ($refl->get_constants() as $constant => $c)
				{
					// Skip inherited constants?
					if ( ! $this->_inherit AND $c['class'] != $source)
						continue;

					// Create the comment and declaration
					$comment = '// Declared in '.$c['class'];
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
	 * @param   string|array  $sources  The source names to inspect
	 * @param   string        $type     The inspected source type
	 * @param   Generator_Reflector  $reflector  The reflector object to use, if any
	 * @return  array   The properties info
	 */
	protected function _get_reflection_properties($sources, $type,
		Generator_Reflector $reflector = NULL)
	{
		$refl = ($reflector !== NULL) ? $reflector : (new Generator_Reflector);
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
					// Skip inherited properties?
					if ( ! $this->_inherit AND $p['class'] != $source)
						continue;

					if (empty($p['doccomment']))
					{
						// Create a new doccomment if one doesn't exist
						$doc = View::factory('generator/type/doccomment')->set('tabs', 1);
						$doc->set('short_description',  "Declared in {$p['class']}");
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
