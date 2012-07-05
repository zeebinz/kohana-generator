Generator for Kohana - Changelog
================================

Version 0.4
-----------

- The implemented methods for generated classes will now use the original interface
  doccomments if these are available, otherwise new ones will be created.
- Added new doccomment template.
- Renamed Generator_Reflector::inspect to ::analyze, avoiding confusion with
  the inspect methods of other Generator classes.

Version 0.3
-----------

- Skeleton methods will now be added for classes that implement interfaces - if
  the interfaces exist - to satisfy any interface requirements.
- Generated config values should now have proper indentation.
- Numeric config keys are now set directly and not treated as array paths.
- Added Generator_Reflector class to support basic reflection functions.
- Limited access to Type methods that should not be part of their fluent interface.
- Refactored the tasks to reduce the number of required methods, making
  the creation of new tasks simpler.
- Added new get_config() method for all tasks to more easily load default
  configuration values.

Version 0.2
-----------

- Added generate:task:generator as a shortcut for creating generator tasks
  with skeleton methods.
- Config file to use with generator tasks can now be specified with the
  new `--config` option.
- Added colorization of log and inspect output, with a new `--no-ansi` option
	to disable it. Windows users will need to install the [ANSICON console](http://adoxa.110mb.com/ansicon)
	to view the colors.
- Added a custom version of Minion_CLI temporarily to support some
  extra features.

Version 0.1
-----------

- Initial release
