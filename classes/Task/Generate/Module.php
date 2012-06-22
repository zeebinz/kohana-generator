<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates a new module skeleton, with a basic directory structure
 * and initial files.
 *
 * Additional options:
 *
 *   --name=MODULE (required)
 *
 *     The name of the module folder to be created.
 *
 * Examples
 * ========
 * minion generate:module --name=mymodule
 *
 *     file : MODPATH.'/mymodule/init.php'
 *     file : MODPATH.'/mymodule/README.md'
 *     file : MODPATH.'/mymodule/LICENSE'
 *     file : MODPATH.'/mymodule/guide/mymodule/menu.md'
 *     file : MODPATH.'/mymodule/guide/mymodule/index.md'
 *     file : MODPATH.'/mymodule/config/userguide.php'
 *     dir  : MODPATH.'/mymodule/classes'
 *     dir  : MODPATH.'/mymodule/tests'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Module extends Generator_Task_Generate_Module {} 
