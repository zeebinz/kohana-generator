<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates configuration files, optionally with simple config entries
 * passed as value definitions.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CONFIG</info> <alert>(required)</alert>
 *
 *     This sets the name of the config file.
 *
 *   <info>--values=VALUES</info>
 *
 *     Value definitions may be added as a comma-separated list in the
 *     format: "array.path.key|value".
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:config --name=logger --module=logger \
 *     --values="logger.file.name|log, logger.file.ext|txt, logger.debug|1"</info>
 *
 *     file : MODPATH/logger/config/logger.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Config extends Generator_Task_Generate_Config {}
