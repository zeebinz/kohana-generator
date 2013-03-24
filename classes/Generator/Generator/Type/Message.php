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

	protected function _import_source($source, $path = NULL)
	{
		return Generator::get_message($source, $path);
	}

} // End Generator_Generator_Type_Message
