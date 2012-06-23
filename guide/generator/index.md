# Generator for Kohana

This module allows the automatic creation of application and module files from templates using different [configurable generators](types).  It includes a versatile [Builder class](builder) with a fluent interface for combining different generators, and a set of [Minion tasks](tasks) for running generators from the commandline.

The Builder's interface allows easy creation of different resource types. At its simplest, to create a Log class in APPPATH/classes/Log.php:

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

In this example, a class is created with a stub for transparent extension in the 'logger' module directory, along with a skeleton unit test case for it. Global options can also be set on all the types added to the builder (in this case, the module name via `with_module()`). See [Using the Builder](builder) for more information on different types and examples of more complex cases.

The module includes a set of [Minion](http://github.com/kohana/minion) tasks for running generators from the commmandline. Start here for help and common options, and a list of available generators:

	./minion generate --help

And then enjoy creating your application or module resources in one line:

	./minion generate:class --name=Logger_Log --stub=Log --module=logger

To run the tasks, you'll need to load both Generator and Minion modules in your bootstrap, in this order:

    Kohana::modules(array(
        ...
        'generator' => MODPATH.'generator',
        'minion'    => MODPATH.'minion',
        ...
    ));

See [Running the Tasks](tasks) for more information and examples.
