<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application and module files from templates using different
 * configurable generators.
 *
 * Usage: minion generate:GENERATOR [arguments] [options]
 *
 * Common options for all generators:
 *
 *   --help       #  Show each generator's options and usage
 *   --pretend    #  Run the generator without making changes
 *   --force      #  Replace any existing files
 *   --quiet      #  Don't output any status messages
 *   --inspect    #  View the rendered output only
 *   --no-ask     #  Don't ask for any user input
 *   --remove     #  Delete files and empty directories
 *   --verbose    #  Show more information when running
 *   --no-ansi    #  Disable ANSI output, e.g. colors
 *
 *   --template=VIEW
 *
 *     The view to use instead of the default view template, stored in 
 *     the /views folder.
 *
 *   --module=NAME|FOLDER
 *
 *     Either a loaded module name as defined in the bootstrap, or a valid
 *     folder name under MODPATH in which to create the files instead of
 *     the default APPPATH.
 *
 *   --config=GROUP
 *
 *     The config group to use with this task instead of the default
 *     value 'generator'.
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate extends Generator_Task_Generate {}
