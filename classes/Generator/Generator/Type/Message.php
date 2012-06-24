<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Message type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type_Message extends Generator_Type_Config
{
	protected $_folder = 'messages';

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
			$ret[trim($key)] = trim($val);
		}

		return $ret;
	}

} // End Generator_Generator_Type_Message
