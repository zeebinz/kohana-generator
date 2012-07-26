<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application classes from templates. The class can be created in
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CLASS</info> <alert>(required)</alert>
 *
 *     The full name of the class to be created, with capitalization.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is optionally extended.
 *
 *   <info>--stub=CLASS</info>
 *
 *     If set, this empty class will be created as a transparent extension
 *     of the main class.
 *
 *   <info>--implement=INTERFACE[,INTERFACE[,...]]</info>
 *
 *     A comma-separated list of any interfaces that this class should
 *     implement.
 *
 *   <info>--use=TRAIT[,TRAIT[,...]]</info>
 *
 *     A comma-separated list of any traits that this class should use
 *     <alert>(requires PHP >= 5.4.0)</alert>.
 *
 *   <info>--clone=CLASS</info>
 *
 *     If a valid class name is set with this option, its properties and
 *     methods will be copied directly from its class file.  Reflection
 *     will be used for internal classes, or if the <info>--reflect</info> option is set,
 *     and inherited methods and properties may be included with <info>--inherit</info>.
 *
 *   <info>--abstract</info>
 *
 *     The class will be marked as abstract if this option is set.

 *   <info>--no-test</info>
 *
 *     A test case will be created automatically for the class unless this
 *     option is set.
 *
 *   <info>--blank</info>
 *
 *     The skelton methods for both the class and the test will be omitted
 *     if this option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:class --name=Log_Reader --implement=Countable,ArrayAccess</info>
 *
 *     class : Log_Reader implements Countable, ArrayAccess
 *     file  : APPPATH/classes/Log/Reader.php
 *     class : Log_ReaderTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Log/ReaderTest.php
 *
 * <info>minion generate:class --name=Logger_Log_Reader --extend=Logger_Reader \
 *     --module=logger --stub=Log_Reader --no-test</info>
 *
 *     class : Logger_Log_Reader extends Logger_Reader
 *     file  : MODPATH/logger/classes/Logger/Log/Reader.php
 *     class : Log_Reader extends Logger_Log_Reader
 *     file  : MODPATH/logger/classes/Log/Reader.php
 *
 * <info>minion generate:class --name=Log --clone=SplMinHeap --inherit --no-test</info>
 *
 *     class : Log extends SplHeap implements Countable, Traversable, Iterator
 *     file  : APPPATH/classes/Log.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Class extends Generator_Task_Generate_Class {}
