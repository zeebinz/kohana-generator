<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application interfaces from templates. The interface can be
 * created in either the application folder or a module folder, and can
 * optionally be configured for transparent extension.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=INTERFACE</info> <alert>(required)</alert>
 *
 *     The full name of the interface to be created, with capitalization.
 *
 *   <info>--extend=INTERFACE[,INTERFACE[,...]]</info>
 *
 *     A comma-separated list of any interfaces that this interface should 
 *     extend (multiple inheritance is possible).
 *
 *   <info>--clone=INTERFACE</info>
 *
 *     If a valid interface name is given, its definition will be copied
 *     directly from its file.  Reflection will be used for any internal
 *     interfaces, or if the <info>--reflect</info> option is set, and any inherited
 *     method definitions may be included with <info>--inherit</info>.
 *
 *   <info>--stub=INTERFACE</info>
 *
 *     If set, this stub will be created as a transparent extension.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:interface --name=Loggable --extend=Countable,Iterator</info>
 *
 *     interface : Loggable extends Countable, Iterator
 *     file      : APPPATH/classes/Loggable.php
 *
 * <info>minion generate:interface --name=Logger_Loggable --stub=Loggable \
 *     --module=logger</info>
 *
 *     interface : Logger_Loggable
 *     file      : MODPATH/logger/classes/Logger/Loggable.php
 *     interface : Loggable extends Logger_Loggable
 *     file      : MODPATH/logger/classes/Loggable.php 
 *
 * <info>minion generate:interface --name=Loggable --clone=SeekableIterator</info>
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
