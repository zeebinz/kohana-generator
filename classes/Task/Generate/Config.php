<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates configuration files, optionally with simple config entries
 * passed as value definitions or imported from existing sources.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=CONFIG</info> <alert>(required)</alert>
 *
 *     This sets the name of the config file.
 *
 *   <info>--values=VALUE[,VALUE[,...]]</info>
 *
 *     Value definitions may be added as a comma-separated list in the
 *     format: "array.path.key|value".
 *
 *   <info>--import=SOURCE[,SOURCE[,...]]</info>
 *
 *     Values may be imported from existing sources as a comma-separated
 *     list in the format: "source|array.path.key", and may be overridden
 *     by any values set via the <info>--values</info> option. If only the source is
 *     specified, all of its values will be imported.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:config --name=logger --module=logger \
 *     --values="logger.file.name|log, logger.file.ext|txt, logger.debug|1"</info>
 *
 *     file : MODPATH/logger/config/logger.php
 *
 * <info>minion generate:config --name=logger --import="app|logger.file, other" \
 *     --values="logger.debug|1, other.name|foo"</info>
 *
 *     file : APPPATH/config/logger.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Config extends Generator_Task_Generate_Config {}
