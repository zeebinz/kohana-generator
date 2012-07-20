<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates message files, optionally with simple message entries
 * passed as value definitions.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=MESSAGE</info> <alert>(required)</alert>
 *
 *     This sets the name of the message file.
 *
 *   <info>--values=VALUES</info>
 *
 *     Value definitions may be added as a comma-separated list in the
 *     format: "array.path.key|value".
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:message --name=logger --module=logger \
 *     --values="logging.some_message|some_value"</info>
 *
 *     file : MODPATH/logger/messages/logger.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Message extends Generator_Task_Generate_Message {}
