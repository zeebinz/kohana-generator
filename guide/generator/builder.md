# Using the Generator Builder

When you need to create new application resources, such as controllers or library classes, often you want to create different types at the same time. New controllers probably need new view templates. Classes may need configuration files, or stubs for transparent extension if you want to share them with others in a module, along with unit tests, guide pages and so on.

This is where the Generator Builder comes in handy.  Its main purpose is to provide a simple interface for defining and creating different resources at the same time.  But before we get into the specifics of the syntax, we first need to understand how these resources are defined by different generator *types*.

## Generator Types

A generator type represents a new resource on your file system: it knows what kind of file it should be, where it belongs, what template is being used to create it, and the values that the template needs for rendering, etc. Types have methods that allow easy configuration of the file content, as well as controlling the behaviour of the generator when it's run. All generator objects are instances of some `Generator_Type` class.

Generators can be configured manually before their resources are finally added to your project with the `create()` command:

	$generator = new Generator_Type_Class('Foo');
	$generator->template('generator/type_class');
	$generator->create();

Since most of these generator methods are chainable, you can configure and run them in a single statement, using a simple syntax:

	$generator
		->name('Foo')
		->template('generator/type_class')
		->folder('classes')
		->module('mymodule')
		->create();

You can find more information about different generator types, as well as how to create your own, on the [Generator Types](types) page, and you can browse their properties and methods on their respective API pages. Or just read their source code ;)

Running generators individually like this is not so convenient, as it turns out.  We need some way of managing more than one at a time, and applying global settings to them all.

## Adding Types

The first step in using the Builder is getting a new instance of the `Generator_Builder` class. This is easy:

	$builder = Generator::build();
	
Then you want to start adding types to it, and configuring each in turn.  We can start chaining methods straight away from the new instance, and we use one of the available `add_*` methods to add our first generator. This is the most basic:

	$builder = Generator::build()->add_type('class', 'Foo')->builder();
	
Notice that the chain here ends with `builder()`, to guarantee that we get a reference back to the Builder object, so `$builder` is now pointing to it.  This is necessary because adding new types always returns an instance of the new type, which we can then configure directly. If we hadn't called `builder()`, then `$builder` would now be pointing to the new type, which we don't (usually) want.

So, to continue with the example above:

	$builder = Generator::build()->add_class('Foo')
		->template('generator/type_class')
		->folder('classes')
		->module('mymodule')
		->builder();

The `add_class('Foo')` method is identical to `add_type('class', 'Foo')`. By the magic of  PHP, all generator types can be referenced with this shorthand. As long as the type is defined in classes/Generator/Type/*, it's automatically usable by the Builder.

For example, the Generator_Type_Controller class can be used without any additional fuss like so:

	$builder = Generator::build()->add_controller('Home')
		->extend('Controller_Template')
		->actions('index, create, edit')
		->builder();

When you've finished configuring your first generator, it's time to add some more types by calling `add_*` again, returning new type instances each time. In this way, we can build up our list step by step. We can use indentation to keep the code a bit clearer:

	$builder = Generator::build()
		->add_controller('Home')
			->extend('Controller_Template')
			->actions('index, create, edit')
			->set('category', 'Controllers')
			->pretend()
		->add_file('home.php')
			->folder('views')
			->content('Content of the Home page')
			->pretend()
		->builder();

Here the view is added as a generic *file* type with the filename and content set directly by the builder. We can do this whenever existing templates aren't quite enough for our needs.

For all other generators, the filename and its full path will be guessed based on the given generator name, folder and module settings. Otherwise the `file()` method can be used to set the absolute path manually.

Something else worth noting at this point: whenever a method is called on the Builder that isn't defined in the Generator_Builder class itself, it will search the *last added* generator for the method, and if found will call that instead. This means that you can interrupt the chain and resume it later; for example, if you need to check a condition before setting a specific option on the last generator.

## Setting Global Options and Defaults

So far, we've configured each generator individually, but often there are common settings that would be more helpful to apply to all of the generators.  For example, if the resources need to be created in the same module, or be run in *pretend* mode where no changes should be made to the file system. The Builder's `with_*` methods allow this:

	$builder = Generator::build()
		->add_model('Kohana_Model_Foo')
			->extend('Model_Database')
		->add_class('Model_Foo')
			->blank()
		->with_pretend()
		->with_module('mymodule');

All of the `with_*` methods always returns the Builder instance, so there's no need to call `builder()` here at the end of the chain.  

It's also often the case that many values in the templates will be the same for all of the added types.  For instance: author, copyright, license and other info in the class docblocks.  The `with_defaults()` method allows these to be set, and merged with whichever defaults have been defined by each generator class:

	$builder = Generator::build()
		->add_model('Kohana_Model_Foo')
			->extend('Model_Database')
			->set('author', 'Not Zeebee')
		->add_model('Foo')
			_>extend('Kohana_Model_Foo')
			->blank()
		->with_pretend()
		->with_module('mymodule')
		->with_defaults(array(
			'author'    => 'Zeebee',
			'copyright' => '(c) 2012 Zeebee',
		));

The defaults won't override anything set manually on each generator, as in the example of the `author` value above: for the first item it will be 'Not Zeebee', for the second it will be 'Zeebee'.

## Inspecting and Executing

It can be quite helpful when experimenting to view what the Builder will create before actually running it, and so the `inspect()` method will return an array of file names and rendered template output without adding any files:

		$array = Generator::build()->add_class('Foo')->inspect();
		echo Debug::vars($array);

Otherwise you can move straight to executing the Builder, which creates the resources and records a log of the actions taken.  This log can then be viewed in whichever way is most convenient:

	$builder = Generator::build()->add_class('Foo')->execute();
	
	foreach ($builder->get_log() as $msg)
	{
		echo 'Status: '.$msg['status'].PHP_EOL;
		echo 'Item:   '.$msg['item'].PHP_EOL;
	}
	
The status field should specify whether the item (file and all its parent directories) was created, or else ignored because it already exists.  Setting *force* mode via `with_force()` will mean that any existing files are always over-written with the new rendered output.

The `execute()` method actually calls `create()` by default on each of the generators in the Builder list. There's also an option to delete those items, which is useful if you need to undo any recent actions:

	$builder = Generator::build()->add_class('Foo')
		->execute(Generator::REMOVE);

Now the log will report on exactly which files were removed, as well as any of their parent directories if they were left empty by removing the file. Non-empty directories are never deleted, though! If you're ever in any doubt, run the command in *pretend* mode - this will log the actions that would have been taken in non-pretend mode, but otherwise no changes are made.

## So where do I put all this code?

Good question! We're basically working with a familiar MVC pattern here: generators are the 'Model', templates are the 'View', now we just need a 'Controller' to put the two together.  You can use your web app for this purpose if you want, but by far the most sensible and `secure` way is to use the bundled [Minion tasks](tasks) to configure and run Builders automatically from the commandline.

Exploring the source code for these tasks also happens to be the best way of seeing how the Builder class can be used in different scenarios, from creating inividual files to whole skeleton modules.
