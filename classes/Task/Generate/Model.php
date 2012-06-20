<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application models from templates. The model can be created in 
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * Additional options:
 *
 *   --name=MODEL (required)
 *
 *     The name of this Model. If 'Model_' is not included in the name, it
 *     will be prepended automatically.
 *
 *   --extend=CLASS
 *
 *     The name of the parent class from which this is optionally extended.
 *
 *   --stub=MODEL
 *
 *     If set, this stub will be created as a transparent extension of the 
 *     model; the 'Model_' prefix may also be omitted.
 *
 *   --no-test
 *
 *     A test case will be created automatically for the class unless this
 *     option is set.
 *
 * Examples
 * ========
 * minion generate:model --name=Log
 *
 *     class : Model_Log extends Model
 *     file  : APPPATH.'/classes/Model/Log.php'
 *     class : Model_LogTest extends Unittest_TestCase
 *     file  : APPPATH.'/tests/Model/LogTest.php' 
 *
 * minion generate:model --name=Logger_Model_Log --extend=Model_Database \
 *     --module=logger --stub=Model_Log --no-test
 *
 *     class : Logger_Model_Log extends Model_Database
 *     file  : MODPATH.'/logger/classes/Logger/Model/Log.php'
 *     class : Model_Log extends Logger_Model_Log
 *     file  : MODPATH.'/logger/classes/Logger/Model/Log.php'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Model extends Generator_Task_Generate_Model {} 
