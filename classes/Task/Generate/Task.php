<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application tasks from templates. The task can be created in 
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TASK</info> <alert>(required)</alert>
 *
 *     The name of this task. If 'Task_' is not included in the name, it
 *     will be prepended automatically.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is extended, if none
 *     is given then Minion_Task will be used by default.
 *
 *   <info>--stub=TASK</info>
 *
 *     If set, this empty task will be created as a transparent extension,
 *     and usage info will be added to the stub instead; the 'Task_' prefix
 *     may also be omitted from the stub name.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:task --name=Logs_Rotate</info>
 *
 *     class : Task_Logs_Rotate extends Minion_Task
 *     file  : APPPATH/classes/Task/Logs/Rotate.php
 *
 * <info>minion generate:task --name=Logger_Task_Logs_Rotate --module=logger \
 *     --stub=Logs_Rotate</info>
 *
 *     class : Logger_Task_Logs_Rotate extends Minion_Task
 *     file  : MODPATH/logger/classes/Logger/Task/Logs/Rotate.php
 *     class : Task_Logs_Rotate extends Logger_Task_Logs_Rotate
 *     file  : MODPATH/logger/classes/Task/Logs/Rotate.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Task extends Generator_Task_Generate_Task {}
