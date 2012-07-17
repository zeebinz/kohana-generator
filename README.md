Generator for Kohana
====================

This module, inspired by the Ruby on Rails tool of the same name, allows the automatic creation of application and module resources from templates using different configurable generators.  It includes a versatile Builder class with a fluent interface for combining different generator types, and a set of Minion tasks for running generators from the command line.

## Generator Builder

The Builder's fluent interface allows easy creation of different resource types. At its simplest, to create a Log class in APPPATH/classes/Log.php:

	Generator::build()->add_class('Log')->execute();

Different resources can also be created in one command by combining generator types:

	Generator::build()
		->add_class('Logger_Log')
			->implement('Countable')
			->implement('ArrayAccess')
		->add_class('Log')
			->extend('Logger_Log')
			->blank()
		->add_unittest('Logger_Log')
			->group('logger')
			->group('logger.core')
		->with_module('logger')
		->execute();

In this example, a class is created with a stub for transparent extension in the 'logger' module directory, along with a skeleton unit test case for it. Global options can also be set on all the types added to the builder (in this case, the module name via `with_module()`).

See the Guide pages for more information on different types and examples of using the Builder in more complex cases. There are also plenty of examples in the Generator tasks.

## Minion Tasks

The module includes a set of [Minion](http://github.com/kohana/minion) tasks for running generators from the commmandline. Start here for help and common options:

	./minion generate --help

And then enjoy creating your application or module resources in one line:

	./minion generate:class --name=Logger_Log --stub=Log --module=logger

See also the Guide pages for more information about different generator tasks and their options.

Current tasks include: class, controller, model, view, unittest, task, generator, interface, guide, module, config, message, etc.

## Testing

This module is unit tested using the [Unittest module](http://github.com/kohana/unittest). You can use the *generator* group to run only the generator tests. See also the files in the `tests/fixtures` directory for sample Minion commands and their generated output.

## Requirements

The module was built against Kohana version 3.3/develop, and requires Minion for running the tasks.
