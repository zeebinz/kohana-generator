<?php defined('SYSPATH') OR die('No direct script access.');

trait Fx_Trait_Counter
{
	public $counted = 0;

	public function count($input) {}
}

trait Fx_Trait_Sorter
{
	protected $_sorted = array();

	public function sort($reverse = FALSE) {}
}

trait Fx_Trait_Logger
{
	use Fx_Trait_Counter;
	use Fx_Trait_Sorter;

	protected static $_logged = array();

	public static function get_logged() {}

	public function log($text) {}
}

trait Fx_Trait_Selector
{
	use Fx_Trait_Sorter;

	abstract public function select($item);
}

trait Fx_Trait_Reporter
{
	use Fx_Trait_Selector;

	abstract public function report();
}

trait Fx_Trait_Overrider
{
	use Fx_Trait_Reporter;

	public function report($item) {}
}

class Fx_ClassWithTraits
{
	use Fx_Trait_Logger;
}

abstract class Fx_AbstractClassWithTraits
{
	use Fx_Trait_Selector;
}

class Fx_ClassChildWithTraits extends Fx_ClassWithTraits {}

class Fx_ClassOverridesTraits
{
	use Fx_Trait_Selector;

	public function select($item) {}
}
