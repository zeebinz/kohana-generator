<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates message files, optionally with simple message entries
 * passed as value definitions or imported from existing sources.
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
 *   <info>--import=SOURCE[,SOURCE[,...]]</info>
 *
 *     Values may be imported from existing sources as a comma-separated list
 *     in the format: "source|array.path.key", and may be overridden by any
 *     values set via the <info>--values</info> option. If only the source is specified,
 *     all of its values will be imported.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:message --name=logger --module=logger \
 *     --values="logging.some_message|some_value"</info>
 *
 *     file : MODPATH/logger/messages/logger.php
 *
 * <info>minion generate:message --name=logger --import="app|logging, other" \
 *     --values="logging.some_message|some_value"</info>
 *
 *     file : APPPATH/logger/messages/logger.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Message extends Generator_Task_Generate_Message {}
