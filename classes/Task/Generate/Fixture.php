<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generates fixture files for functional testing, with a given command as
 * the input, and a generated string as the expected output.
 *
 * Additional options:
 *
 *   --name=FIXTURE (required)
 *
 *     This sets the name of the fixture file. The '.test' extension will be
 *     added if it isn't already included in the name.
 *
 *   --command="COMMAND STRING" (required)
 *
 *     A valid command string to be included in the fixture file, and which
 *     is run to create the expectation string. Note that single quotation
 *     marks must be used for option values.
 *
 * Examples
 * ========
 * minion generate:fixture --name=generate_class --module=logger \
 *     --command="generate:class --name=Foo --implement='Bar, Baz' --no-test"
 *
 *     file : MODPATH/logger/tests/fixtures/generate_class.test
 *
 * @package    Generator
 * @category   Tasks
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Task_Generate_Fixture extends Generator_Task_Generate_Fixture {}
