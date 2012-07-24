<?php defined('SYSPATH') OR die('No direct script access.');

class Fx_Class
{
	const CONST_ONE = 1;
	const CONST_TWO = 2;

	public static $prop_one;
	protected  static $_prop_two = 'two';

	public static function method_one($one, $two = 'two', $three = 3, $four = 4.01) {}
	protected static function _method_two(SomeClass $class = NULL) {}

	public $prop_three = array();
	public $prop_four = 4.001;

	public function method_three(array $list) {}
	final public function method_four(array $list = NULL, $two = NULL) {}

	/**
	 * A protected property
	 */
	protected $_prop_five = array();

	/**
	 * A protected method
	 */
	protected function _method_five(SomeClass $class, $two) {}
	final protected function _method_six() {}
}

class Fx_ChildClass extends Fx_Class
{
	const CONST_ONE = 'one';
	const CONST_TWO = 2;
	const CONST_THREE = 3;

	public static $prop_one;

	protected function _method_five(SomeClass $class, $two) {}
}

abstract class Fx_AbstractClass extends Fx_Class
{
	public $abstract_prop;

	abstract public function abstract_method_one($one);
	abstract protected function _abstract_method_two($two = 2);
}

class Fx_ImplClass extends Fx_Class implements Fx_Countable, Fx_Sortable
{
	public $impl_prop;

	public function count() {}

	public function sort(array $list) {}
}

abstract class Fx_AbstractImplClass implements Fx_Countable, Fx_Sortable {}
