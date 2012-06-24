<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Config type.
 *
 * @package    Generator 
 * @category   Generator/Types 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_Generator_Type_Config extends Generator_Type 
{
	protected $_template = 'generator/type_config';
	protected $_folder   = 'config';

	/**
	 * Adds any values for this config file.
	 *
	 * The values should be passed in the form "key|value", and may
	 * be in an array or comma-separated lists.
	 *
	 * @param   string|array  $values  The value names
	 * @return  Generator_Type_Config  This instance
	 */
	public function value($values)
	{
		$this->param_to_array($values, 'values');
		return $this;
	}

	/**
	 * Converts an array of value definition strings into a final array of 
	 * value items.
	 *
	 * @param   array  $values  The list of value definitions
	 * @return  array  The parsed list
	 */
	public function parse_values(array $values)
	{
		$ret = array();

		foreach ($values as $value)
		{
			list($key, $val) = explode('|', $value);
			$key = is_numeric($key) ? (trim($key) + 0) : trim($key);
			$val = is_numeric($val) ? (trim($val) + 0) : trim($val);
			Arr::set_path($ret, $key, $val);
		}

		return $ret;
	}

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		if ( ! empty($this->_params['values']))
		{
			$this->_params['values'] = $this->parse_values($this->_params['values']);
		}

		return parent::render();
	}

} // End Generator_Generator_Type_Config 
