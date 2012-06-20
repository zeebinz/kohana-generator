<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application controllers from templates. The controller can be
 * created in either the application folder or a module folder.
 *
 * Additional options:
 *
 *   --name=CONTROLLER (required)
 *
 *     The name of this controller. If 'Controller_' is not included in
 *     the name, it will be prepended automatically.
 *
 *   --extend=CLASS
 *
 *     The name of the parent class from which this is extended, if none
 *     is given then Controller will be used by default.
 *
 *   --actions=ACTION[,ACTION[,...]]
 *
 *     A comma-separated list of optional action methods to be included in
 *     this controller, without the 'action_' prefix.
 * 
 *   --blank
 *
 *     The skelton methods will be omitted if this option is set.
 * 
 * Examples
 * ========
 * minion generate:controller --name=Home
 *
 *     class : Controller_Home extends Controller
 *     file  : APPPATH.'/classes/Controller/Home.php'
 *
 * minion generate:controller --name=Home --module=logger --blank \
 *     --extend=Controller_Template --actions="index,create,edit"
 *
 *     class : Controller_Home extends Controller_Template
 *     file  : MODPATH.'/logger/classes/Controller/Home.php'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Controller extends Generator_Task_Generate_Controller {} 
