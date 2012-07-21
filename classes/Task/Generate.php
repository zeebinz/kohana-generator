<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application and module files from templates using different
 * configurable generators.
 *
 * Usage: minion <alert>generate:GENERATOR</alert> [arguments] [options]
 *
 * <comment>Common options for all generators:</comment>
 *
 *   <info>--help</info>       :  Show each generator's options and usage
 *   <info>--pretend</info>    :  Run the generator without making changes
 *   <info>--force</info>      :  Replace any existing files
 *   <info>--quiet</info>      :  Don't output any status messages
 *   <info>--inspect</info>    :  View the rendered output only
 *   <info>--no-ask</info>     :  Don't ask for any user input
 *   <info>--remove</info>     :  Delete files and empty directories
 *   <info>--verbose</info>    :  Show more information when running
 *   <info>--no-ansi</info>    :  Disable ANSI output, e.g. <alert>colors</alert>
 *
 *   <info>--template=VIEW</info>
 *
 *     The view to use instead of the default view template, stored in 
 *     the /views folder.
 *
 *   <info>--module=NAME|FOLDER</info>
 *
 *     Either a loaded module name as defined in the bootstrap, or a valid
 *     folder name under <alert>MODPATH</alert> in which to create the files instead of
 *     the default <alert>APPPATH</alert>.
 *
 *   <info>--config=GROUP</info>
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
