<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates Guide menu, index and optionally page files with skeleton
 * content, and the userguide config file. The menu will include entries
 * for any given page definitions.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=MENU</info> <alert>(required)</alert>
 *
 *     This will set the top level of the Guide menu in the menu file.
 *
 *   <info>--pages=PAGES</info>
 *
 *     Page definitions may be added as a comma-separated list in the
 *     format: "Page Title|filename".
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:guide --name=Logging --module=logger \
 *     --pages="Setting up|setup, Running the tasks|tasks"</info>
 *
 *     file : MODPATH/logger/guide/logger/menu.md
 *     file : MODPATH/logger/guide/logger/index.md
 *     file : MODPATH/logger/guide/logger/setup.md
 *     file : MODPATH/logger/guide/logger/tasks.md
 *     file : MODPATH/logger/config/userguide.php
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Guide extends Generator_Task_Generate_Guide {} 
