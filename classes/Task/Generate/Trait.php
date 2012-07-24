<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates application traits from templates. The trait can be created in
 * either the application folder or a module folder, and can optionally be
 * configured for transparent extension <alert>(requires PHP >= 5.4.0)</alert>.
 *
 * <comment>Additional options:</comment>
 *
 *   <info>--name=TRAIT</info> <alert>(required)</alert>
 *
 *     The full name of the trait to be created, with capitalization.
 *
 *   <info>--stub=TRAIT</info>
 *
 *     If set, this empty trait will be created as a transparent extension
 *     of the main trait.
 *
 *   <info>--use=TRAIT[,TRAIT[,...]]</info>
 *
 *     A comma-separated list of any traits that this trait should use.
 *
 *   <info>--clone=TRAIT</info>
 *
 *     If a valid trait name is set with this option, its methods will be
 *     copied directly from its class file. Reflection  will be used if the
 *     <info>--reflect</info> option is set, and inherited methods may be included with
 *     the <info>--inherit</info> option.
 *
 *   <info>--blank</info>
 *
 *     The trait's skelton methods will be omitted if this option is set.
 *
 * <comment>Examples</comment>
 * ========
 * <info>minion generate:trait --name=Trait_Logging</info>
 *
 *     trait : Trait_Logging
 *     file  : APPPATH/classes/Trait/Logging.php
 *
 * <info>minion generate:trait --name=Logger_Trait_Logging --module=logger \
 *     --use="Sorter, Counter" --stub=Trait_Logging</info>
 *
 *     trait : Logger_Trait_Logging {use Sorter; use Counter;}
 *     file  : MODPATH/logger/classes/Logger/Trait/Logging.php
 *     trait : Trait_Logging {use Logger_Trait_Logging;}
 *     file  : MODPATH/logger/classes/Trait/Logging.php
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Trait extends Generator_Task_Generate_Trait {}
