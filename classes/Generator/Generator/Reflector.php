<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * This class provides some shortcuts for handling basic Reflection details
 * from sources such as classes, interfaces, etc.
 *
 * Thanks to simshaun for the pointers and samples.
 *
 * @package    Generator
 * @category   Reflectors
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Reflector
{
	// The supported source types
	const TYPE_CLASS     = 'class';
	const TYPE_INTERFACE = 'interface';
	const TYPE_TRAIT     = 'trait';

	/**
	 * The source class, interface etc. to inspect
	 * @var  string
	 */
	protected $_source;

	/**
	 * The current source type
	 * @var  string
	 */
	protected $_type;

	/**
	 * The parsed reflection info for the source
	 * @var  string
	 */
	protected $_info = array();

	/**
	 * Instantiates the reflector and stores the name of the source that is
	 * being inspected.
	 *
	 * @param  string  $source  The source name
	 * @param  string  $type    The source type
	 * @return void
	 */
	public function __construct($source = NULL, $type = Generator_Reflector::TYPE_CLASS)
	{
		$this->source($source);
		$this->type($type);
	}

	/**
	 * Setter/getter for the source class, interface etc. being inspected.
	 *
	 * @param   string  $source  The source name
	 * @return  string|Generator_Reflector  The current source name or this instance
	 */
	public function source($source = NULL)
	{
		if ($source === NULL)
			return $this->_source;

		if ($this->_source !== $source)
		{
			// Reset if we're swapping sources
			$this->_source = $source;
			$this->_info = array();
		}

		return $this;
	}

	/**
	 * Setter/getter for the current source type.
	 *
	 * @param   string  $type    The source type
	 * @return  string|Generator_Reflector  The current source type or this instance
	 */
	public function type($type = NULL)
	{
		if ($type === NULL)
			return $this->_type;

		if ($this->_type !== $type)
		{
			// Reset if we're swapping types
			$this->_type = $type;
			$this->_info = array();
		}

		return $this;
	}

	/**
	 * Determines whether the current source exists, based on its given type.
	 *
	 * @return  boolean
	 * @throws  Generator_Exception
	 */
	public function exists()
	{
		if ($this->is_trait() AND ! $this->supports_traits())
		{
			throw new Generator_Exception('PHP >= 5.4.0 is required for trait support');
		}

		return call_user_func($this->_type.'_exists', $this->_source);
	}

	/**
	 * Gathers basic reflection info on the given source and stores it locally.
	 *
	 * @return  Generator_Reflector  This instance
	 * @throws  Generator_Exception  On missing source
	 */
	public function analyze()
	{
		if ( ! $this->_source)
		{
			// We need a source to work with
			throw new Generator_Exception('No source is available to analyze');
		}

		// Start the new reflection
		$class = new ReflectionClass($this->_source);

		// Store the reflection info locally
		$this->_info = $this->parse_reflection_class($class);

		return $this;
	}

	/**
	 * Parses reflection classes for key information to store locally.
	 *
	 * @param   ReflectionClass  $class  The class to parse
	 * @return  array  The parsed info
	 */
	public function parse_reflection_class(ReflectionClass $class)
	{
		// Get any class doccomment
		$doccomment = $class->getDocComment();

		// Get the full class name
		$name = $class->getName();

		// Get any class modifiers
		$modifiers = array();
		if ( ! $this->is_trait())
		{
			// The default trait modifiers 'abstract public' aren't helpful
			// but for the other types we want the modifier names
			$modifiers = Reflection::getModifierNames($class->getModifiers());
		}

		// Get the abstract flag
		$abstract = $class->isAbstract();

		// Get any parent class name
		$parent = ($parent = $class->getParentClass()) ? $parent->getName() : NULL;

		// Get any traits used by the class
		$trait_names = array();
		$traits = array();
		if ($this->supports_traits())
		{
			$trait_names = $class->getTraitNames();
			$traits = $class->getTraits();
		}

		// Get any class constants
		$constants = $class->getConstants();
		$const_names = array_keys($constants);
		foreach ($constants as $key => $value)
		{
			// Guess the declaring class of the constant (fudge!)
			$const_source = ($parent AND ($v = $class->getParentClass()->getConstant($key))
				AND $v === $value) ? $parent : $this->_source;

			// Add the constant info
			$constants[$key] = array('class' => $const_source, 'value' => $value);
		}

		// Get any implemented interfaces
		$interface_names = array();
		$interfaces = $class->getInterfaces();
		$inherited  = array();
		foreach ($interfaces as $i)
		{
			// Track the interface inheritance tree
			$interface_names[$i->getName()] = $i->getInterfaceNames();
			$inherited += array_flip($i->getInterfaceNames());

			foreach ($const_names as $c) if ($i->getConstant($c) != NULL)
			{
				// The constant was declared by this interface
				$constants[$c]['class'] = $i->getName();
			}
		}

		// Get the default properties list
		$defaults = $class->getDefaultProperties();

		// Get any class properties
		$properties = array();
		foreach ($class->getProperties() as $property)
		{
			$properties[$property->getName()] = $this->parse_reflection_property(
				$property, $defaults);

			foreach ($traits as $trait) if ($trait->hasProperty($property->getName()))
			{
				// The property was defined by a trait, and can't be re-declared
				$properties[$property->getName()]['trait'] = $this->get_declaring_trait(
					$property, $trait);
			}
		}

		// Get any declared methods
		$methods = array();
		foreach ($class->getMethods() as $method)
		{
			$method_name = $method->getName();
			$methods[$method_name] = $this->parse_reflection_method($method);

			foreach ($interfaces as $i) if ($i->hasMethod($method_name))
			{
				// The method was defined by an interface, so track the declaring class
				$methods[$method_name]['interface'] = $i->getMethod($method_name)
					->getDeclaringClass()->getName();
			}

			foreach ($traits as $trait) if ($trait->hasMethod($method_name))
			{
				// The method was defined by a trait, so track the base trait info
				$trait_info = $this->get_declaring_trait($method, $trait);
				$methods[$method_name]['trait'] = $trait_info['name'];
				$methods[$method_name]['inherited'] = ! $trait_info['overridden'];
			}
		}

		// Return the parsed info
		return array(
			'doccomment' => $doccomment,
			'name'       => $name,
			'modifiers'  => $modifiers,
			'abstract'   => $abstract,
			'parent'     => $parent,
			'interfaces' => $interface_names,
			'inherited'  => $inherited,
			'traits'     => $trait_names,
			'constants'  => $constants,
			'properties' => $properties,
			'methods'    => $methods,
		);
	}

	/**
	 * Parses reflection properties for information such as modifiers, type,
	 * any default values, etc.
	 *
	 * @param   ReflectionProperty  $property  The property to parse
	 * @param   array  $defaults  A list of defined property defaults
	 * @return  array  The parsed info
	 */
	public function parse_reflection_property(ReflectionProperty $property, array $defaults)
	{
		// Get the property doccomment
		$doccomment = $property->getDocComment();

		// Get the declaring class name
		$class = $property->getDeclaringClass()->getName();

		// Get any trait info. Trait properties can't be re-declared.
		$trait = ($this->supports_traits() and trait_exists($class)) ? $class : '';

		// Get the modifiers string
		$modifiers = implode(' ', Reflection::getModifierNames($property->getModifiers()));

		// Get any default value
		$default = ($property->isDefault() AND $defaults[$property->getName()] !== NULL)
			? $this->export_variable($defaults[$property->getName()])
			: NULL;

		// Get the property type based on the default value
		$type = $this->get_variable_type($defaults[$property->getName()]);

		// Return the parsed info
		return array(
			'class'      => $class,
			'trait'      => $trait,
			'doccomment' => $doccomment,
			'modifiers'  => $modifiers,
			'value'      => $default,
			'type'       => $type,
		);
	}

	/**
	 * Parses reflection methods for key information, including any modifiers,
	 * declaring class, the parsed parameters list, etc.
	 *
	 * @param   ReflectionMethod  $method  The method to parse
	 * @return  array  The parsed info
	 */
	public function parse_reflection_method(ReflectionMethod $method)
	{
		// Get any method doccomment
		$doccomment = $method->getDocComment();

		// Get the declaring class name
		$class = $method->getDeclaringClass()->getName();

		// Get any interface info
		$interface = interface_exists($class) ? $class : '';

		// Get any trait info. Trait methods can be re-declared by classes and
		// other traits, so we need to track the method's base trait.
		$trait = ($this->supports_traits() and trait_exists($class)) ? $class : '';

		// Has the class inherited the method?
		$inherited = ($class != $this->_source);

		// Get the modifiers
		$modifiers = $method->getModifiers();

		if ($this->is_interface())
		{
			// We don't need the abstract modifier for interface methods
			$modifiers &= ~ReflectionMethod::IS_ABSTRACT;
		}

		// Convert the modifiers to a string
		$modifiers = implode(' ', Reflection::getModifierNames($modifiers));

		// Get the returns by reference flag
		$by_ref = $method->returnsReference();

		// Get the method flags
		$abstract = $method->isAbstract();
		$final    = $method->isFinal();
		$private  = $method->isPrivate();

		// Get the parsed parameters list
		$params = array();
		foreach ($method->getParameters() as $param)
		{
			$params[$param->getName()] = $this->parse_reflection_param($param);
		}

		// Return the parsed info
		return array(
			'class'      => $class,
			'interface'  => $interface,
			'trait'      => $trait,
			'inherited'  => $inherited,
			'doccomment' => $doccomment,
			'modifiers'  => $modifiers,
			'by_ref'     => $by_ref,
			'abstract'   => $abstract,
			'final'      => $final,
			'private'    => $private,
			'params'     => $params,
		);
	}

	/**
	 * Parses reflection parameters for information such as type hints and any
	 * default values, etc.
	 *
	 * @param   ReflectionParameter  $param  The parameter to parse
	 * @return  array  The parsed info
	 */
	public function parse_reflection_param(ReflectionParameter $param)
	{
		// Get any type hint without needing to load any classes
		preg_match('/\[\s\<\w+?>\s([\w]+)/s', $param->__toString(), $matches);
		$type = isset($matches[1]) ? $matches[1] : '';

		// Do we have a type hint to use?
		$hint = (bool) $type;

		// Get the param properties
		$by_ref = $param->isPassedByReference();
		$default = NULL;

		if ($param->isDefaultValueAvailable())
		{
			// Add any default values
			$default = $this->export_variable($param->getDefaultValue());

			if ($type == '')
			{
				// Set the type info based on the default value
				$type = $this->get_variable_type($param->getDefaultValue());
			}
		}

		// Use 'mixed' as the default type
		$type = ($type == '') ? 'mixed' : $type;

		// Return the parsed info
		return array(
			'type'    => $type,
			'hint'    => $hint,
			'default' => $default,
			'by_ref'  => $by_ref
		);
	}

	/**
	 * Gets the base trait in which a given method or property was first defined
	 * by recursively searching each trait used by the given trait.
	 *
	 * @link http://php.net/manual/en/language.oop5.traits.php
	 *
	 * @param   ReflectionMethod|ReflectionParameter  $member  The member to check
	 * @param   ReflectionClass  $trait  The trait to begin searching
	 * @return  string|boolean   The trait name or FALSE on no match
	 */
	public function get_declaring_trait($member, $trait)
	{
		// Search any included traits recursively
		foreach ($trait->getTraits() as $used)
		{
			// Use the base trait as the declaring trait
			if ($base_trait = $this->get_declaring_trait($member, $used))
				return $base_trait;
		}

		if ($member instanceof ReflectionProperty AND $trait->hasProperty($member->getName()))
		{
			// The trait includes a definition of the method/property
			return $trait->getName();
		}
		elseif ($member instanceof ReflectionMethod AND $trait->hasMethod($member->getName()))
		{
			// Compare method definitions
			$method = $this->parse_reflection_method($member);
			$trait_method = $this->parse_reflection_method($trait->getMethod($member->getName()));

			// Has the current source overridden the trait method?
			$overridden = (($method['modifiers'] != $trait_method['modifiers'])
				OR ($method['params'] != $trait_method['params']));

			// Return the trait method info
			return array('name' => $trait->getName(), 'overridden' => $overridden);
		}

		// No definition was found
		return FALSE;
	}

	/**
	 * Converts a parsed abstract method definition to a concrete one for
	 * storing locally, optionally updating an already stored definition.
	 *
	 * @param   array   $method  The method definition to convert
	 * @param   string  $name    The stored method name to update
	 * @return  array   The converted definition
	 */
	public function make_method_concrete(array $method, $name = NULL)
	{
		$method['modifiers'] = trim(str_replace('abstract', '', $method['modifiers']));
		$method['abstract'] = FALSE;

		if ($name AND isset($this->_info['methods'][$name]))
		{
			// Update the stored definition
			$this->_info['methods'][$name] = $method;
		}

		return $method;
	}

	/**
	 * Exports a variable value to a parsable string representation. Array
	 * variables can be processed recursively, and indentation may optionally
	 * be included with these.
	 *
	 * @param   mixed    $variable  The variable to export
	 * @param   boolean  $indent    Should indentation be included?
	 * @param   integer  $level     The indentation level
	 * @return  string   The exported string
	 */
	public function export_variable($variable, $indent = FALSE, $level = 1)
	{
		if ( ! is_array($variable))
		{
			// Objects shouldn't be exported
			if (is_object($variable))
				return NULL;

			// Return the exported value
			$val = var_export($variable, TRUE);
			return in_array($val, array('true', 'false', 'null')) ? strtoupper($val) : $val;
		}

		// Convert arrays to comma-separated lists
		$list = array();

		foreach ($variable as $key => $value)
		{
			// Array values may be exported recursively
			$entry = $this->export_variable($value, $indent, is_array($value) ? ($level + 1) : $level);

			if ( ! is_integer($key))
			{
				// Expand string keys to 'key' => val
				$entry = "'{$key}' => ".$entry;
			}

			// Add the new entry
			$list[] = $entry;
		}

		if ($indent)
		{
			// Return an indented array definition
			return 'array('.PHP_EOL
				.str_repeat("\t", $level)
				.implode(",".PHP_EOL.str_repeat("\t", $level), $list).','.PHP_EOL
				.str_repeat("\t", $level - 1).')';
		}

		// Return a flat array definition
		return 'array('.implode(', ', $list).')';
	}

	/**
	 * Returns a normalized type definition for a given variable.
	 *
	 * @param   mixed   $variable  The variable to inspect
	 * @return  string  The normalized type
	 */
	public function get_variable_type($variable)
	{
		$type = gettype($variable);
		$type = str_replace(array('NULL', 'double'), array('mixed', 'float'), $type);

		return $type;
	}

	/**
	 * Determines whether the current source has been analyzed yet.
	 *
	 * @return  boolean
	 */
	public function is_analyzed()
	{
		return ! empty($this->_info);
	}

	/**
	 * Determines whether the current source is an interface type.
	 *
	 * @return  boolean
	 */
	public function is_interface()
	{
		return $this->_type === Generator_Reflector::TYPE_INTERFACE;
	}

	/**
	 * Determines whether the current source is a class type.
	 *
	 * @return  boolean
	 */
	public function is_class()
	{
		return $this->_type === Generator_Reflector::TYPE_CLASS;
	}

	/**
	 * Determines whether the current source is a trait type.
	 *
	 * @return  boolean
	 */
	public function is_trait()
	{
		return $this->_type === Generator_Reflector::TYPE_TRAIT;
	}

	/**
	 * Determines whether the current source is an abstract type.
	 *
	 * @return  boolean
	 */
	public function is_abstract()
	{
		$this->is_analyzed() OR $this->analyze();

		return $this->_info['abstract'];
	}

	/**
	 * Determines whether traits are supported for this PHP version.
	 *
	 * @return  boolean
	 */
	public function supports_traits()
	{
		return function_exists('trait_exists');
	}

	/**
	 * Returns the raw data for the current source, to be used for debugging
	 * purposes only as the implementation is subject to change.
	 *
	 * @param   string  $key  The requested info
	 * @return  mixed   The raw info
	 */
	public function get_info($info = NULL)
	{
		if ($info === NULL)
			return $this->_info;

		return $this->_info[$info];
	}

	/**
	 * Returns the doccomment for the current source.
	 *
	 * @return  string  The source doccomment
	 */
	public function get_doccomment()
	{
		$this->is_analyzed() OR $this->analyze();

		return $this->_info['doccomment'];
	}

	/**
	 * Returns the modifiers string for the current source.
	 *
	 * @return  string  The source modifiers
	 */
	public function get_modifiers()
	{
		$this->is_analyzed() OR $this->analyze();

		return implode(' ', $this->_info['modifiers']);
	}

	/**
	 * Returns the parent class of the current source.
	 *
	 * @return  string  The parent class name
	 */
	public function get_parent()
	{
		$this->is_analyzed() OR $this->analyze();

		return $this->_info['parent'];
	}

	/**
	 * Returns the list of interfaces implemented by the current source,
	 * by default limited to just the interfaces that aren't inherited by
	 * another in the current list.
	 *
	 * Interfaces can be extended by multiple inheritance, and ReflectionClass
	 * will report the whole inheritance tree by default. Problems can happen
	 * when an interface or class tries to implement multiple interfaces that
	 * inherit each other - depending on the declared order - so it's usually
	 * helpful to filter the list in this way.
	 *
	 * @link http://php.net/manual/en/language.oop5.interfaces.php
	 *
	 * @param   boolean   $unfiltered  Include inherited interfaces?
	 * @return  array     The interfaces list
	 */
	public function get_interfaces($unfiltered = FALSE)
	{
		$this->is_analyzed() OR $this->analyze();

		if ($unfiltered)
			return array_keys($this->_info['interfaces']);

		// Start the filtered list
		$interfaces = array();

		foreach (array_keys($this->_info['interfaces']) as $i)
		{
			if ( ! isset($this->_info['inherited'][$i]))
			{
				// We only want non-inherited interfaces
				$interfaces[] = $i;
			}
		}

		return $interfaces;
	}

	/**
	 * Returns the list of traits used by the current source.
	 *
	 * @return  array  The traits list
	 */
	public function get_traits()
	{
		$this->is_analyzed() OR $this->analyze();

		return $this->_info['traits'];
	}

	/**
	 * Returns the list of constants defined by the current source.
	 *
	 * By default this will exclude constants defined by interfaces, which is
	 * usually wise since such constants can't be re-declared by any inheriting
	 * interface or by any implementing class.
	 *
	 * @link http://php.net/manual/en/language.oop5.interfaces.php
	 *
	 * @param   boolean  $unfiltered  Include constants defined by interfaces?
	 * @return  array    The constants list
	 */
	public function get_constants($unfiltered = FALSE)
	{
		$this->is_analyzed() OR $this->analyze();

		if ($unfiltered)
			return $this->_info['constants'];

		// Start the filtered list
		$constants = array();

		foreach ($this->_info['constants'] as $const => $c)
		{
			if ($c['class'] == $this->_source OR $c['class'] == $this->_info['parent'])
			{
				// Only add constants defined by the source or its parent
				$constants[$const] = $c;
			}
		}

		return $constants;
	}

	/**
	 * Returns the list of properties with their parsed info from the current
	 * source.
	 *
	 * Since properties declared by traits can't be re-declared by any class or
	 * other trait that uses the trait directly, they're skipped by default for
	 * all but the declaring trait. Classes that inherit classes that use the
	 * trait *can* re-declare them, though ... confused?
	 *
	 * @link http://php.net/manual/en/language.oop5.traits.php
	 *
	 * @param   boolean  $unfiltered  Return all properties?
	 * @return  array  The properties list
	 */
	public function get_properties($unfiltered = FALSE)
	{
		$this->is_analyzed() OR $this->analyze();

		if ($unfiltered)
			return $this->_info['properties'];

		// Start the filtered list
		$properties = array();

		foreach ($this->_info['properties'] as $property => $p)
		{
			// Skip trait properties unless this is the declaring trait
			if ( ! empty($p['trait']) AND $p['trait'] != $p['class'])
				continue;

			// Add the property info
			$properties[$property] = $p;
		}

		return $properties;
	}

	/**
	 * Returns the list of methods with their parsed info from the current source,
	 * optionally limited to only abstract methods that may need implementing,
	 * and by default including inheritable methods.
	 *
	 * Note that ReflectionClass will report all methods associated with the source,
	 * including final and private methods of parents, interface methods whether
	 * implemented by the source or not, etc. So 'inherited' here means any method
	 * not declared exclusively by the current source or overridden by it; and
	 * 'inheritable' means all non-private and non-final 'inherited' methods.
	 *
	 * @param   boolean  $abstract    Only return abstract methods?
	 * @param   boolean  $inherit     Include inheritable methods?
	 * @param   boolean  $unfiltered  Return all reported methods?
	 * @return  array    The methods list
	 */
	public function get_methods($abstract = FALSE, $inherit = TRUE, $unfiltered = FALSE)
	{
		$this->is_analyzed() OR $this->analyze();

		if ($unfiltered)
			return $this->_info['methods'];

		// Start the filtered list
		$methods = array();

		foreach ($this->_info['methods'] as $method => $m)
		{
			// Ignore inherited methods that we don't want to implement
			if ($m['inherited'] AND ($m['final'] OR $m['private'] OR ! $inherit))
				continue;

			// Only include abstract methods?
			if ($abstract AND ! $m['abstract'])
				continue;

			// Add the method definition
			$methods[$method] = $m;
		}

		return $methods;
	}

	/**
	 * Returns a parsable string declaration for the given constant name.
	 *
	 * @param   string  $constant  The constant name
	 * @return  string  The constant declaration
	 * @throws  Generator_Exception  On invalid constant name
	 */
	public function get_constant_declaration($constant)
	{
		$this->is_analyzed() OR $this->analyze();

		if ( ! isset($this->_info['constants'][$constant]))
		{
			throw new Generator_Exception('Constant :constant does not exist', array(
				':constant' => $constant));
		}

		// Create the declaration
		return 'const '.$constant.' = '
			.$this->export_variable($this->_info['constants'][$constant]['value']);
	}

	/**
	 * Returns a parsable string declaration for the given property name.
	 *
	 * @param   string  $property  The property name
	 * @return  string  The property declaration
	 * @throws  Generator_Exception  On invalid property name
	 */
	public function get_property_declaration($property)
	{
		$this->is_analyzed() OR $this->analyze();

		if (empty($this->_info['properties'][$property]))
		{
			throw new Generator_Exception('Property :property does not exist', array(
				':property' => $property));
		}

		$p = $this->_info['properties'][$property];

		// Create the declaration
		$modifiers = $p['modifiers'] ? ($p['modifiers'].' ') : '';
		$value = ($p['value'] != NULL) ? (' = '.$p['value']) : '';

		return $modifiers.'$'.$property.$value;
	}

	/**
	 * Returns the signature for a given method parameter as a parsable string
	 * representation from the current source.
	 *
	 * @param   string  $method  The method name
	 * @param   string  $param   The parameter name
	 * @return  string  The parameter signature
	 * @throws  Generator_Exception  On invalid parameter name
	 */
	public function get_param_signature($method, $param)
	{
		if (empty($this->_info['methods'][$method]['params'][$param]))
		{
			throw new Generator_Exception('Param :param does not exist for method :method',
				array(':param' => $param, ':method' => $method));
		}

		$p = $this->_info['methods'][$method]['params'][$param];

		// Build the signature from the stored info
		$type    = ($p['hint'] AND $p['type']) ? ($p['type'].' ') : '';
		$ref     = $p['by_ref'] ? '& ' : '';
		$default = $p['default'] ? (' = '.$p['default']) : '';

		// Return the parsed signature
		return $type.$ref.'$'.$param.$default;
	}

	/**
	 * Returns the full signature for the given method parameters as a parsable
	 * string representation from the current source.
	 *
	 * @param   string  $method  The method name
	 * @return  string  The full signature for the parameters
	 * @throws  Generator_Exception  On invalid method name
	 */
	public function get_method_param_signatures($method)
	{
		if (empty($this->_info['methods'][$method]))
		{
			throw new Generator_Exception('Method :method does not exist', array(
			':method' => $method));
		}

		// Start the list of signatures
		$sigs = array();

		if ( ! empty($this->_info['methods'][$method]['params']))
		{
			foreach (array_keys($this->_info['methods'][$method]['params']) as $param)
			{
				// Add each parameter signature to the list
				$sigs[] = $this->get_param_signature($method, $param);
			}
		}

		// Return the imploded list
		return implode(', ', $sigs);
	}

	/**
	 * Returns a full method signature as a parsable string representation from
	 * the current source.
	 *
	 * @param   string  $method  The method name
	 * @return  string  The method signature
	 * @throws  Generator_Exception  On invalid method name
	 */
	public function get_method_signature($method)
	{
		$this->is_analyzed() OR $this->analyze();

		if (empty($this->_info['methods'][$method]))
		{
			throw new Generator_Exception('Method :method does not exist', array(
				':method' => $method));
		}

		$m = $this->_info['methods'][$method];

		// Get the method parameter signatures
		$params = $this->get_method_param_signatures($method);

		// Create the full signature
		$ref = $m['by_ref'] ? '& ' : '';
		$modifiers = $m['modifiers'] ? ($m['modifiers'].' ') : '';

		return $modifiers.'function '.$ref.$method.'('.$params.')';
	}

	/**
	 * Returns a parsable string representation of a method invocation from
	 * the current source.
	 *
	 * @param   string  $method  The method name
	 * @return  string  The method invocation string
	 * @throws  Generator_Exception  On invalid method name
	 */
	public function get_method_invocation($method)
	{
		$this->is_analyzed() OR $this->analyze();

		if (empty($this->_info['methods'][$method]))
		{
			throw new Generator_Exception('Method :method does not exist', array(
				':method' => $method));
		}

		// Get the parameters list
		$params = array_keys($this->_info['methods'][$method]['params']);
		$params = array_map(function($p) {return '$'.$p;}, $params);

		return $method.'('.implode(', ', $params).')';
	}

} // End Generator_Generator_Reflector
