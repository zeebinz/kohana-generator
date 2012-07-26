<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Trait type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Trait extends Generator_Type
{
	protected $_template = 'generator/type/class';
	protected $_folder   = 'classes';

	protected $_defaults = array(
		'package'    => 'package',
		'category'   => 'Traits',
		'author'     => 'author',
		'copyright'  => 'copyright',
		'license'    => 'license',
		'class_type' => 'trait',
	);

	/**
	 * Adds any traits inherited by this trait.
	 *
	 * The traits may be passed either as an array, comma-separated list
	 * of trait names, or as a single trait name.
	 *
	 * @param   string|array  $traits  The trait names to use
	 * @return  Generator_Type_Trait   This instance
	 */
	public function using($traits)
	{
		$this->param_to_array($traits, 'traits');
		return $this;
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return parent::render();
	}

} // End Generator_Generator_Type_Trait
