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
 *   --clone=INTERFACE
 *
 *     If a valid interface name is given, its definition will be copied
 *     directly from its file.  Reflection will be used for any internal
 *     interfaces, or if the --reflect option is set, and any inherited
 *     method definitions may be included with --inherit.
 *
 *   --stub=INTERFACE
 *
 *     If set, this stub will be created as a transparent extension.
 *
 * Examples
 * ========
 * minion generate:interface --name=Loggable --extend=Countable,Iterator
 *
 *     interface : Loggable extends Countable, Iterator
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
 * minion generate:interface --name=Loggable --clone=SeekableIterator
 *
 *     interface : Loggable extends Traversable
 *     file      : APPPATH/classes/Log.php
 * 
 * @package    Generator 
 * @category   Tasks 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Task_Generate_Interface extends Generator_Task_Generate_Interface {} 
