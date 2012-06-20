<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates Guide menu, index and optionally page files with skeleton
 * content. The menu will include entries for any given page definitions.
 *
 * Additional options:
 *
 *   --name=MENU (required)
 *
 *     This will set the top level of the Guide menu in the menu file.
 *
 *   --pages=PAGES
 *
 *     Page definitions may be added as a comma-separated list in the
 *     format: "Page Title|filename".
 *
 * Examples
 * ========
 * minion generate:guide --name=Logging --module=logger \
 *     --pages="Setting up|setup, Running the tasks|tasks"
 *
 *     file : MODPATH.'/logger/guide/logger/menu.md'
 *     file : MODPATH.'/logger/guide/logger/index.md'
 *     file : MODPATH.'/logger/guide/logger/setup.md'
 *     file : MODPATH.'/logger/guide/logger/tasks.md'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Guide extends Generator_Task_Generate_Guide {} 
