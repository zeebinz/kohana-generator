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
	 * Converts the interfaces list to a string and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if ( ! empty($this->_params['implements']) AND is_array($this->_params['implements']))
		{
			$this->_params['implements'] = implode(', ', $this->_params['implements']);
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Class
