# Running the Generator Tasks

You can run generators from any type of controller, including from a page of your web application. But it makes a lot more sense - is far easier and more secure - to create new resources from the commandline.  The module is therefore bundled with a set of tasks for Kohana's awesome Minion CLI module.

## Getting Started
First you need to install the latest version of Minion from [here](http://github.com/kohana/minion). Then you should load both Generator and Minion modules in your bootstrap like so, in this order:

    Kohana::modules(array(
        ...
        'generator' => MODPATH.'generator',
        'minion'    => MODPATH.'minion',
        ...
    ));

It's well advised to read the Minion guide at least once (it's short) so you understand its conventions and how it handles what it calls 'tasks'. Then start your console of choice and run this simple command:

	./minion generate --help

This should give you the main help page for the Generator module in the usual Minion format. Along with general info, it should include something like this:
	
	Usage: minion generate:GENERATOR [arguments] [options]
	
	Common options for all generators:
	
	  --help       :  Show each generator's options and usage
	  --pretend    :  Run the generator without making changes
	  --force      :  Replace any existing files
	  --quiet      :  Don't output any status messages
	  --inspect    :  View the rendered output only
	  --no-ask     :  Don't ask for any user input
	  --remove     :  Delete files and empty directories
	  --verbose    :  Show more information when running
	  --no-ansi    :  Disable ANSI output, e.g. colors
	
	  --template=VIEW
	
	    The view to use instead of the default view template, stored in 
	    the /views folder.
	
	  --module=NAME|FOLDER
	
	    Either a loaded module name as defined in the bootstrap, or a valid
	    folder name under MODPATH in which to create the files instead of
	    the default APPPATH.
	
	  --config=GROUP
	
	    The config group to use with this task instead of the default
	    value 'generator'.
	
	Available generators:
	
	...

Following this should be a list of the available generators on your system. You can view the help pages for each of these by running the command:

	./minion generate:GENERATOR --help
	
The options listed in the general help are common to all generators, and each generator help page lists any extra options, along with examples of usage. For instance, the help for the `config` generator (one of the simplest) includes this:

	./minion generate:config --help
	
	Usage
	=====
	minion generate:config NAME VALUES [--option=value] [--option]
	
	...
	
	Description
	===========
	Generates configuration files, optionally with simple config entries
	passed as value definitions or imported from existing sources.
	
	Additional options:
	
	  --name=CONFIG (required)
	
	    This sets the name of the config file.
	
	  --values=VALUES
	
	    Value definitions may be added as a comma-separated list in the
	    format: "array.path.key|value".
	
	  --import=SOURCE[,SOURCE[,...]]
	
	    Values may be imported from existing sources as a comma-separated list
	    in the format: "source|array.path.key", and may be overridden by any
	    values set via the option. If only the source is specified, all of its
	    its values will be imported.
	
	Examples
	========
	minion generate:config --name=logger --module=logger \
	    --values="logger.file.name|log, logger.file.ext|txt, logger.debug|1"
	
	    file : MODPATH/logger/config/logger.php
	
	minion generate:config --name=logger --import="app|logger.file, other" \
	    --values="logger.debug|1, other.name|foo"
	
	    file : APPPATH/logger/config/logger.php
	
	...

As can be seen from the Usage info, the `name` and `values` options can be passed as the first two arguments, or set explicitly using the syntax in the examples.  Each task defines which positional arguments may be used, but the examples always use the full syntax to keep things clear. Just note for now that this is also possible:

	minion generate:config logger "logger.file.name|log" --module=logger

This should be plenty to get you started.  The examples in the help cover the most common cases, and the source code for the tasks is generally quite short and simple to read. All of the tasks use the [Generator Builder](builder), so you should read about that if you haven't already.

## Previewing and Inspecting

Unless you're really confident about what the output is going to be, you'll usually want to preview the changes that are going to be made.  Let's continue with the `config` generator example.  Run this command in your console:

	./minion generate:config --name=logger

	The result of running this task will be:
	
	     exists  APPPATH/config
	     create  APPPATH/config/logger.php

	Do you want to continue? [ y, n ]:
	
Notice that by default you're asked to confirm any actions - *exists* means the item won't be replaced. You can change the behaviour by using the `--force` option usually, and if you don't want to be asked for confirmation use `--no-ask` or `--quiet`. If you want to see what the changes will be without making them and don't want the prompt, use `--pretend`. Entering 'n' as your response will end the task, 'y' will create only the resources marked *create*.

[!!] The log output will be colorized by default for consoles that support ANSI escape characters - Windows users will need to install [ANSICON](http://adoxa.110mb.com/ansicon). To disable the colors, use the `--no-ansi` option.

Otherwise, you can add the `--inspect` option to preview the destination filename as well as the rendered file contents:

	./minion generate:config --name=logger --values="logger.debug|1" --inspect
	
	[ File 1 ] APPPATH/config/logger.php

	<?php defined('SYSPATH') OR die('No direct script access.');
	
	return array(
		'logger' => array(
			'debug' => 1,
		),
	);

Isn't that handy? You pretty much can't go wrong if you preview like this when trying out options until things look just right, then run again without `--inspect`. If you accidentally add files and want to remove them, this is also easy. Assuming you just added the config file, now run this:

	./minion generate:config --name=logger --remove

	The result of running this task will be:
	
	     remove  APPPATH/config/logger.php
	  not empty  APPPATH/config
	
	Do you want to continue? [ y, n ]:

Now you'll be asked to confirm which items to remove, including the sub-directories. Any directories that are marked `not empty` - meaning they won't be left empty when the file is actually deleted - won't be touched. Only empty directories will be removed.

Sharp eyes will notice that the syntax of the output so far is very similar to RoR's generator tool. This is by design, with some local flavour. ;)

## Configuration and Common Settings

Quite a few of the tasks involve creating files where more likely than not you'll want to set common or frequently used values on the output.  Docblocks for class files are typical examples.  You have two options here:

**Edit the view templates**

Replace the template used by the task with your own (usually in your APPPATH/views/generator folder), and set the values in it manually.  The templates are quite simple and easy to edit.

**Edit config/generator.php**

Add generator.php to your APPPATH/config directory, and set the common values in there, using Kohana's normal conventions. An example is included in the module config directory, here for any task that uses the Class generator type:

	return array(
		'defaults' => array(
			'class' => array(
				'author'    => 'Author',
				'copyright' => '(c) 2012 Author',
				'license'   => 'License info',
			),
		),
	);
 
Notice that these are just defaults, they won't override anything set manually. If you don't set any specific value here, placeholder values will be used by the tasks instead. You can also set the config group used by the task with the `--config` option:

	./minion generate:class --name=Foo  --config=test/generator

In this case, the task will look for the list of default values in the array path `defaults.class` of the config/test/generator.php file.

## Creating your own Generator Tasks

At some point you'll probably want to make your own generators with tasks to run them. Or maybe you want to tweak the existing tasks. There are lots of choices here.  First, by the magic of Kohana's transparent extension, you can just create your customized versions of the tasks in your application directory, e.g. to modify the `generator:class` task:

	APPPATH/classes/Task/Generate/Class.php
	
	class Task_Generate_Class extends Generator_Task_Generate_Class {}
	
	This now takes the place of:
	
	MODPATH/generator/classes/Task/Generate/Class.php
	
So you can just add your new methods or override existing ones in that new class file.	Note, though, that because of how Minion works, you'll need to add the help page for this task to your new file also.

Otherwise you can run the `generate:task` command with the proper options, or for a less manual approach you can use this handy shortcut instead:

	./minion generate:task:generator --name=Foo

But for a full set of files, what you *really* want to do (trust me) is run the special task for creating generators:

	./minion generate:generator --name=Foo
	
You'll find more info about this command and what it does on the [Generator Types](types) page. All you really need to know is that it makes life a whole lot easier.  Once you've run it, you can start to work through the files that it creates and complete the skeleton methods provided if you need to, fill in the help page details, etc., and you're off.
