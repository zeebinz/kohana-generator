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
 *     The full name of the interface to be created, with capitalization.
 *
 *   --extend=INTERFACE[,INTERFACE[,...]]
 *
 *     A comma-separated list of any interfaces that this interface should 
 *     extend (multiple inheritance is possible).
 *
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
