<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates a unit test case with optional skeleton methods.
 *
 * Additional options:
 *
 *   --name=TEST (required)
 *
 *     The full class name of this test. The 'Test' suffix will be added
 *     automatically if not already included in the name.
 *
 *   --extend=CLASS
 *
 *     The name of the parent class from which the test case is extended,
 *     if none is given then Unittest_TestCase will be used by default.
 *
 *   --groups=GROUP[,GROUP[,...]]
 *
 *     A comma-separated list of the group parameters for this test case.
 *
 *   --blank
 *
 *     The skelton methods will be omitted if this option is set.
 *
 * Examples
 * ========
 * minion generate:test --name=Logger_Logs_Rotate --groups="logger,logger.tasks"
 *
 *     class : Logger_Logs_RotateTest extends Unittest_TestCase
 *     file  : APPPATH.'/tests/Logger/Logs/RotateTest.php'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Unittest extends Generator_Task_Generate_Unittest {} 
