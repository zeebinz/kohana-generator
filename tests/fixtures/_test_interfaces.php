<?php defined('SYSPATH') OR die('No direct script access.');

interface Fx_Countable
{
	const CONST_COUNTABLE = 1;

	public function count();
}

interface Fx_Sortable
{
	const CONST_SORTABLE = 1;

	public function sort(array $list);
}

interface Fx_Iterable extends Fx_Countable, Fx_Sortable
{
	const CONST_ITERABLE = 1;

	public function iter();
}
