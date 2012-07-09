<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates message files, optionally with simple message entries
 * passed as value definitions.
 *
 * Additional options:
 *
 *   --name=MESSAGE (required)
 *
 *     This sets the name of the message file.
 *
 *   --values=VALUES
 *
 *     Value definitions may be added as a comma-separated list in the
 *     format: "array.key.path|value".
 *
 * Examples
 * ========
 * minion generate:message --name=logger --module=logger \
 *     --values="logging.some_message|some_value"
 *
 *     file : MODPATH.'/logger/messages/logger.php'
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Message extends Generator_Task_Generate_Message {}
