<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application classes from templates. The class can be created in 
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * Additional options:
 *
 *   --name=CLASS (required)
 *
 *     The full class name.
 *
 *   --extend=CLASS
 *
 *     The name of the parent class from which this is optionally extended.
 *
 *   --stub=CLASS
 *
 *     If set, this empty class will be created as a transparent extension
 *     of the main class.
 * 
 *   --implement=INTERFACE[,INTERFACE[,...]]
 *
 *     A comma-separated list of any interfaces that this class should 
 *     implement.
 *
 *   --clone=CLASS
 *
 *     If a valid class name is set with this option, its properties and
 *     methods will be copied directly from its class file.  Reflection
 *     will be used for internal classes, or if the --reflect option is set,
 *     and inherited methods and properties may be included with --inherit.
 *
 *   --no-test
 *
 *     A test case will be created automatically for the class unless this
 *     option is set.
 *
 *   --abstract
 *
 *     The class will be marked as abstract if this option is set.
 *  
 * Examples
 * ========
 * minion generate:class --name=Log_Reader --implement="Countable,ArrayAccess"
 *
 *     class : Log_Reader implements Countable, ArrayAccess
 *     file  : APPPATH.'/classes/Log/Reader.php'
 *     class : Log_ReaderTest extends Unittest_TestCase
 *     file  : APPPATH.'/tests/Log/ReaderTest.php' 
 *
 * minion generate:class --name=Logger_Log_Reader --extend=Logger_Reader \
 *     --module=logger --stub=Log_Reader --no-test
 *
 *     class : Logger_Log_Reader extends Logger_Reader
 *     file  : MODPATH.'/logger/classes/Logger/Log/Reader.php'
 *     class : Log_Reader extends Logger_Log_Reader
 *     file  : MODPATH.'/logger/classes/Log/Reader.php'
 *
 * minion generate:class --name=Log --clone=SplMinHeap --inherit --no-test
 *
 *     class : Log extends SplHeap implements Countable, Traversable, Iterator
 *     file  : APPPATH.'/classes/Log.php'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Class extends Generator_Task_Generate_Class {} 
