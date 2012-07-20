<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates new Generator types from templates, together with associated unit
 * tests and Minion tasks. If a module is specified, both the generators and
 * tasks will be extended transparently with stubs.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TYPE</info> <alert>(required)</alert>
 *
 *     The name of the generator type. If `Generator_Type_` is not included
 *     in the name, it will be prepended automatically.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is optionally extended,
 *     otherwise defaults to Generator_Type.
 *
 *   <info>--prefix=PREFIX</info>
 *
 *     If created in a module and extended transparently with stubs, the
 *     classes will be prefixed with the module name by default unless the
 *     value is set with this option.
 *
 *   <info>--no-stub</info>
 *
 *     The classes will not be extended transparently if this option is set.
 * 
 *   <info>--no-task</info>
 *
 *     Minion tasks will not be created if this option is set.
 *
 *   <info>--no-test</info>
 *
 *     Unit tests will not be created if this option is set. 
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:generator --name=Foo</info>
 *
 *     class : Generator_Type_Foo extends Generator_Type
 *     file  : APPPATH/classes/Generator/Type/Foo.php
 *     class : Task_Generate_Foo extends Task_Generate
 *     file  : APPPATH/classes/Task/Generate/Foo.php
 *     class : Generator_Type_FooTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Generator/Type/FooTest.php
 *
 * <info>minion generate:generator --name=Foo --module=bar --no-task --no-test</info>
 *
 *     class : Bar_Generator_Type_Foo extends Generator_Type
 *     file  : MODPATH/bar/classes/Bar/Generator/Type/Foo.php
 *     class : Generator_Type_Foo extends Bar_Generator_Type_Foo
 *     file  : MODPATH/bar/classes/Generator/Type/Foo.php 
 *
 * <info>minion generate:generator --name=Foo --module=bar --no-test --prefix=Kohana</info>
 *
 *     class : Kohana_Generator_Type_Foo extends Generator_Type
 *     file  : MODPATH/bar/classes/Kohana/Generator/Type/Foo.php
 *     class : Generator_Type_Foo extends Kohana_Generator_Type_Foo
 *     file  : MODPATH/bar/classes/Generator/Type/Foo.php
 *     class : Kohana_Task_Generate_Foo extends Task_Generate
 *     file  : MODPATH/bar/classes/Kohana/Task/Generate/Foo.php
 *     class : Task_Generate_Foo extends Kohana_Task_Generate
 *     file  : MODPATH/bar/classes/Task/Generate/Foo.php
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Generator extends Generator_Task_Generate_Generator {} 
