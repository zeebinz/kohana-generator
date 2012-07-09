<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application interfaces from templates. The interface can be
 * created in either the application folder or a module folder, and can
 * optionally be configured for transparent extension.
 *
 * Additional options:
 *
 *   --name=INTERFACE (required)
 *
 *     The full interface name.
 *
 *   --extend=INTERFACE
 *
 *     The name of the parent from which this is optionally extended.
 *
 *   --stub=INTERFACE
 *
 *     If set, this stub will be created as a transparent extension.
 *  
 * Examples
 * ========
 * minion generate:interface --name=Loggable --extend=Countable
 *
 *     interface : Loggable extends Countable
 *     file      : APPPATH/classes/Loggable.php
 *
 * minion generate:interface --name=Logger_Loggable --stub=Loggable \
 *     --module=logger
 *
 *     interface : Logger_Loggable
 *     file      : MODPATH/logger/classes/Logger/Loggable.php
 *     interface : Loggable extends Logger_Loggable
 *     file      : MODPATH/logger/classes/Loggable.php 
 * 
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Interface extends Generator_Task_Generate_Interface {} 
