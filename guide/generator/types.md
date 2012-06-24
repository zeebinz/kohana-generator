# Generator Types

As explained in [Using the Builder](builder), all generators are instances of some `Generator_Type` class.  Each represents a single resource on the file system, and in most cases the file content is created from vanilla view templates.  This keeps things simple and familiar, since it uses the same kind of MVC pattern as your web application.  But it also means that generators are not purely dynamic code generators, where all of the file content is created on the fly, for example like [Zend's CodeGenerator](http://framework.zend.com/manual/en/zend.codegenerator.introduction.html) and other great tools like it.

Although you can create generators that mimic this behaviour quite easily, the bundled generators take a simpler approach with a more straightforward syntax.  The types also include methods for creating and deleting resources, and ways to change the behaviour of these commands when executed.  So most types have combined responsibilities for handling configuration, processing view templates and working with the file system.

The advantage of this approach is that it's easy to make controllers for the generators, such as your web application or Minion tasks, and the syntax stays terse and simple to remember. It also makes it very easy to configure the output just the way you want it by replacing the template files with your own.

## The Base Generator Type

All of the types are defined in classes/Generator/Type/*, and extend the base `Generator_Type` class. This base class defines common methods that can be used in the fluent interface to configure the generators and control their behaviours.  They can all be browsed in the API pages, but here are some extra notes for the most important:

**name($name)**
:	Every generator needs an indentifying name, and in most cases this will be used to guess the final filename, so capitalization is usually significant. The name can usually be set via the constructor, too.

**file($path)**
:	The absolute path to the resource can be set directly, but otherwise it will be guessed based on name, folder and module settings, which is what you usually want.

**template($template)**
:	The view template used by the generator must be stored in the views folder. The value is what you would normally use to load templates, e.g. 'generator/type_class' will load 'views/generator/type_class.php', wherever it can be found in the Cascading File System. This means that it's very easy to swap the default templates for your own - just add them to the views folder in your application.

**pretend()**
:	When the pretend mode is set, no changes will be made to the file system, but the log will record what *would* have happened if the command had been run. This is very handy for previewing and debugging.

**render()**
:	This method must be implemented by each type, and `Generator_Type` by default returns a rendered view from the specified template, with parameters configured by methods like `set($key, $value)`.  But any children of `Generator_Type` can override this method quite easily.  All that needs to be returned is a string that represents the file contents, so pure code generation without using view templates is also possible.

**log()**
:	By design, the actions taken by the generator are recorded in a simple log, each entry consisting of an array with `status` and `item` keys. Most controllers will want to process the log returned by this method in some way to verify the result of running a generator.

**create()**, **remove()**
: When these actions are called, the log will be populated with a record of the actions taken.  This log should be identical in pretend mode - just without any actual effects on the file system.

There's a lot more in the `Generator_Type` class, including methods for handling default parameters, guessing filenames, processing sub-directories, and so on. It's well worth getting to know this class well if you plan to make your own generator types.

## Interacting with a Builder

If the generator type was actually created by a Generator_Builder object, it's really useful for the generator to know about this and to be able to interact with the Builder.  So a reference to the Builder can be injected into the type via its constructor:

	public function __construct($name = NULL, Generator_Builder $builder = NULL)

This may make some people frown - why should the generator care how it was created? But actually it makes supporting a fluent syntax for the Builder a whole lot easier.  Just about all of the the generator type methods are chainable - that is, they return a reference to themselves, so that we can configure them in one statement:

	$generator->name('Foo')->module('bar')-> ... etc.
	
This means, though, that the Builder needs to know whether a method is being called on the generator or on the Builder itself. Behind the scenes, the `__call()` magic method in `Generator_Type` handles this, by passing any undefined method calls to the Builder if one has been injected. So the flow of execution can move transparently between from generators to Builder and back again.

The good news is that if you're handling generators directly without using a Builder, you don't need to worry about this at all. What you *do* need to think about, though, is making sure that none of your custom generator method names match those of the Generator_Builder class.  Otherwise anyone using your generator via the Builder is going to have problems. For more about this, see [Using the Builder](builder).

## Core Generator Types

The simplest generator that can be created is actually an instance of `Generator_Type` itself. This isn't too handy in practice, since everything about it needs to be configured manually, but in some scenarios you may want to start with a completely generic, vanilla type like this:

	$generator = new Generator_Type;
	$generator->name('Foo')->template('some_template')-> ... etc.

Usually, though, you'll either want to adapt one of the classes that extend `Generator_Type`, or roll your own.  Here are some of the generator types that are worth getting to know a bit better:

#### Generator_Type_Class

This is probably one of the most useful to use either directly or extend yourself. As models and controllers as well as libraries are defined in Kohana as classes, you can easily make do with configuring this generator for those - adding details of any class extension, implemented interfaces, and so on, via the fluent interface. As it happens, other types are defined for controllers and models to make life a little easier.

#### Generator_Type_Controller

This type actually extends `Generator_Type_Class`, and adds methods for magically setting the correct class name, and adding a list of controller actions that you want included in the rendered output. It's quite a short class, so it's worth spending a few minutes looking through the source code to see how it modifies the behaviour of its parent. It's a good example of how you can do the same with your own custom generators.

#### Generator_Type_File

This is one of two special generators that don't use view templates. Instead, the file content can be set directly like so:

	$generator = new Generator_File;
	$generator
		->name('index.md')
		->folder('guide/generator')
		->content(''Content of the index file)
		->create();

Here the generator name is the actual filename, and the destination folder must be set explicitly. This type can be handy for generating content on the fly, but if you use it often that's probably a sign that it's time to make your own generator to handle this functionality.

#### Generator_Type_Directory

The other special type, useful only for creating empty directories, and really only needed for more complex Builder statements (for example, to create a whole module skeleton with default directories).  But again it's worth looking at the source code to see how it overrides some parent methods - particularly for handling removal of nested directories.

Apart from these, all of the types in the module are used by one or more of the bundled Minion tasks, and absolutely the best way of seeing how they work is to peek into the simple source code for these - and see [Running the Tasks](tasks) for more info.

##  Creating your own Generators

At some point, you'll probably find that the bundled generator types don't have quite the functionality you need.  Maybe you want to make some configuration more automatic, or perhaps your template has some parameters that need to be handled in a special way.  By making your own generator type, you can extend the fluent interface very easily. For example, these two are completely equivalent:

	// Using the Class type:
	$generator = new Generator_Type_Class;
	$generator
		->name('Model_Foo')
		->extend('Model')
		->set('category', 'Models')
		->create();
	
	// Using the Model type:
	$generator = new Generator_Type_Model('Foo');
	$generator->create();
	
The Model type actually extends the Class type, but it makes setting the class name and template parameters a bit easier by overriding the `name()` method, and adding some extra checks for the parameters in the `render()` method.  It also makes the syntax for the Builder a bit more meaningful:

	Generator::build()->add_model('Foo')->execute();

Any generator type can be created by the Builder with `add_<type>()` method like this. Otherwise the main need for new generator types is to add extra processing, or extend the syntax in some meaningful way.

Every public method that you define in the new type will become part of the generator fluent interface, so it's usually important always to return the generator instance from them so that they can be chained.  A number of methods act as both getters and setters to keep the syntax simple. For example:

	/**
	 * Setter and getter for the module folder in which generator items will
	 * be created.  This must be a valid folder under the current MODPATH.
	 *
	 * @param   string  $module  The module folder name
	 * @return  string|Generator_Type  The current module name or this instance
	 */
	public function module($module = NULL)
	{
		if ($module === NULL)
			return $this->_module;

		$this->_module = $module;

		return $this;
	}
	
So when called without a parameter, it will return the current module name, otherwise it will set the name and return the generator instance. This is a useful pattern to follow generally.

To get started on creating your own generators, you can copy and modify an existing one, but absolutely the easiest way is to run the Minion task:

	./minion generate:generator --name=Foo --module=mymodule --prefix=Bar
	
This creates all the files you need, starting with classes/Bar/Generator/Type/Foo.php with some skeleton methods for you to work with, and a stub for transparent extension. It also adds unit test and task files for the new type. So now would be a good time to read about [Running the Tasks](tasks).
