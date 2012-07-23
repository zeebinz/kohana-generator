<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Interface type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Interface extends Generator_Type
{
	protected $_template = 'generator/type/interface';

	protected $_defaults = array(
		'package'    => 'package',
		'category'   => 'Interfaces',
		'author'     => 'author',
		'copyright'  => 'copyright',
		'license'    => 'license',
		'class_type' => 'interface',
	);

	/**
	 * Adds any interfaces that this class should be defined as extending.
	 * Multiple inheritance is allowed with interfaces.
	 *
	 * @link http://php.net/manual/en/language.oop5.interfaces.php
	 *
	 * The interfaces may be passed either as an array, comma-separated list
	 * of interface names, or as a single interface name.
	 *
	 * @param   string|array  $interfaces  The interface names to extend
	 * @return  Generator_Type_Class  This instance
	 */
	public function extend($interfaces)
	{
		$this->param_to_array($interfaces, 'extends');
		return $this;
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if ( ! empty($this->_params['extends']) AND is_array($this->_params['extends']))
		{
			// Convert the inherited interfaces list to a string
			$this->_params['extends'] = implode(', ', $this->_params['extends']);
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Interface
