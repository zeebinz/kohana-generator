<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates template controllers with associated view files. The files can
 * be created in either the application folder or a module folder.
 *
 * Additional options:
 *
 *   --name=CONTROLLER (required)
 *
 *     The name of this controller. If 'Controller_' is not included in
 *     the name, it will be prepended automatically.
 *
 *   --actions=ACTION[,ACTION[,...]]
 *
 *     A comma-separated list of optional action methods to be included in
 *     this controller, without the 'action_' prefix.
 * 
 * Examples
 * ========
 * minion generate:controller:view --name=Home --actions="index,create,edit"
 *
 *     class : Controller_Home extends Controller_Template
 *     file  : APPPATH/classes/Controller/Home.php
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Controller_View extends Generator_Task_Generate_Controller_View {} 
