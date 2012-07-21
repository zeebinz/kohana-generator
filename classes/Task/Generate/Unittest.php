<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates a unit test case with optional skeleton methods.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TEST</info> <alert>(required)</alert>
 *
 *     The full class name of this test. The 'Test' suffix will be added
 *     automatically if not already included in the name.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which the test case is extended,
 *     if none is given then Unittest_TestCase will be used by default.
 *
 *   <info>--groups=GROUP[,GROUP[,...]]</info>
 *
 *     A comma-separated list of the group parameters for this test case.
 *
 *   <info>--blank</info>
 *
 *     The skelton methods will be omitted if this option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:test --name=Logger_Logs_Rotate --groups="logger,logger.tasks"</info>
 *
 *     class : Logger_Logs_RotateTest extends Unittest_TestCase
 *     file  : APPPATH/tests/Logger/Logs/RotateTest.php
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Unittest extends Generator_Task_Generate_Unittest {} 
