<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Creates custom generator tasks that can be evoked with generate:TASK.
 * The task can be created in either the application folder or a module
 * folder, and can optionally be configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TASK</info> <alert>(required)</alert>
 *
 *     The name of this task. The class pefixes will be added automatically,
 *     so e.g. don't include 'Task_Generate' in the name.
 *
 *   <info>--extend=CLASS</info>
 *
 *     The name of the parent class from which this is extended, if none
 *     is given then Task_Generate will be used by default.
 *
 *   <info>--prefix=PREFIX</info>
 *
 *     If created in a module and extended transparently with a stub, the
 *     task will be prefixed with the module name by default unless the
 *     value is set with this option.
 *
 *   <info>--no-stub</info>
 *
 *     The task will not be extended transparently if this option is set. 
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:task:generator --name=Log</info>
 *
 *     class : Task_Generate_Log extends Task_Generate
 *     file  : APPPATH/classes/Task/Generate/Log.php
 *
 * <info>minion generate:task:generator --name=Log --module=logger --prefix=Kohana</info>
 *
 *     class : Kohana_Task_Generate_Log extends Task_Generate
 *     file  : MODPATH/logger/classes/Kohana/Task/Generate/Log.php
 *     class : Task_Generate_Log extends Kohana_Task_Generate_Log
 *     file  : MODPATH/logger/classes/Task/Generate/Log.php 
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Task_Generator extends Generator_Task_Generate_Task_Generator {}
