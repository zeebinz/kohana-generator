<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates a simple view template file.
 *
 * Additional options:
 *
 *   --name=VIEW (required)
 *
 *     The name of the view template to be created in the views folder,
 *     without the file extension.
 *
 * Examples
 * ========
 * minion generate:view --name=foo/bar
 *
 *     file : APPPATH.'/views/foo/bar.php'
 *
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_View extends Generator_Task_Generate_View {} 
