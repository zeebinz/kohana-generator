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
class Generator_Generator_Type_Interface extends Generator_Type_Class
{
	protected $_template = 'generator/type_interface';

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if (empty($this->_params['category']))
		{
			$this->_params['category'] = 'Interfaces';
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Interface
