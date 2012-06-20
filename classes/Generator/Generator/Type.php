<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Base class for all generator types.
 *
 * Types represent items that are to be created in the filesystem. All
 * generators should extend this class, as it provides common methods and
 * parameters that should be applied to every type.
 *
 * @package    Generator
 * @category   Generator/Types
 * @author     Zeebee
 * @copyright  (c) 2012 Zeebee
 * @license    BSD revised
 */
class Generator_Generator_Type
{	
	/**
	 * The builder instances used to create this generator, if any
	 * @var Generator_Builder
	 */
	protected $_builder;

	/**
	 * Should the generator be run in pretend mode?
	 * @var bool
	 */
	protected $_pretend = FALSE;

	/**
	 * Should the generator be run in force mode?
	 * @var bool
	 */
	protected $_force = FALSE;

	/**
	 * The name of this generator
	 * @var string
	 */
	protected $_name;

	/**
	 * The destination file for the generator
	 * @var string
	 */
	protected $_file;

	/**
	 * The base folder in which the file should be created
	 * @var string
	 */
	protected $_folder;

	/**
	 * The module folder in which the file should be created
	 * @var string
	 */
	protected $_module;

	/**
	 * The view template file used by the generator
	 * @var bool
	 */
	protected $_template;

	/**
	 * Should the security string be added to the rendered template?
	 * @var bool
	 */
	protected $_security = TRUE;

	/**
	 * Should the destination path be verified?
	 * @var bool
	 */
	protected $_verify = TRUE;

	/**
	 * The default template parameters
	 * @var array
	 */
	protected $_defaults = array();

	/**
	 * The parameters set on the generator
	 * @var array
	 */
	protected $_params = array();

	/**
	 * The log of any generator actions, real or pretended
	 * @var array
	 */
	protected $_log = array();


	/**
	 * Instantiates the generator; if created by a builder, stores a reference
	 * locally to the builder instance.
	 *
	 * @param   string  $name  The generator name
	 * @param   Generator_Builder  $builder  Any builder instance
	 * @return  void
	 */
	public function __construct($name = NULL, Generator_Builder $builder = NULL)
	{
		$this->_builder = $builder ?: NULL;
		$this->name($name);
	}

	/**
	 * Setter and getter for the generator name.
	 *
	 * @param   string  $name  The generator name
	 * @return  string|Generator_Type  The current name or this instance
	 */
	public function name($name = NULL)
	{
		if ($name === NULL)
			return $this->_name;

		$this->_name = $name;
		return $this;
	}

	/**
	 * Setter and getter for the filename to be created by the generator.
	 *
	 * @param   string  $file  The destination filename
	 * @return  string|Generator_Type  The current filename or this instance
	 */
	public function file($file = NULL)
	{
		if ($file === NULL)
			return $this->_file;

		$this->_file = $file;
		return $this;
	}

	/**
	 * Setter and getter for the view template to be used by the generator,
	 * i.e. within the views folder.
	 *
	 * @param   string  $template  The template file
	 * @return  string|Generator_Type  The current template or this instance
	 */
	public function template($template = NULL)
	{
		if ($template === NULL)
			return $this->_template;

		if ($template !== '')
		{
			$this->_template = $template;
		}

		return $this;
	}

	/**
	 * Setter and getter for the base folder in which the generator items will
	 * be created, e.g. 'classes', 'tests'.
	 *
	 * @param   string  $folder  The base folder name
	 * @return  string|Generator_Type  The current folder or this instance
	 */
	public function folder($folder = NULL)
	{
		if ($folder === NULL)
			return $this->_folder;

		$this->_folder = $folder;

		return $this;
	}

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

	/**
	 * Setter and getter for the template parameters.
	 *
	 * @param   array  $params  The parameters list
	 * @return  array|Generator_Type  The current params or this instance
	 */
	public function params(array $params = NULL)
	{
		if ($params === NULL)
			return $this->_params;

		$this->_params = $params;

		return $this;
	}

	/**
	 * Setter and getter for the template parameter defaults.
	 *
	 * @param   array  $defaults  The parameter defaults
	 * @return  array|Generator_Type  The current defaults or this instance
	 */	
	public function defaults(array $defaults = NULL)
	{
		if ($defaults === NULL)
			return $this->_defaults;

		$this->_defaults = $defaults;

		return $this;
	}

	/**
	 * Sets the pretend mode for the generator.  If TRUE, no changes will be made
	 * to the filesystem, and he log will report the actions that would have been
	 * taken in non-pretend mode.
	 *
	 * @param   bool  $pretend  The pretend mode to be used
	 * @return  Generator_Type  This instance
	 */
	public function pretend($pretend = TRUE)
	{
		if ($pretend !== NULL)
		{
			$this->_pretend = (bool) $pretend;
		}

		return $this;
	}

	/**
	 * Sets the force mode for the generator.  If TRUE, any existing files will 
	 * be over-written with the new generator output.
	 *
	 * @param   bool  $force  The force mode to be used
	 * @return  Generator_Type  This instance
	 */
	public function force($force = TRUE)
	{
		if ($force !== NULL)
		{
			$this->_force = (bool) $force;
		}

		return $this;
	}

	/**
	 * Sets whether the destination path should be verified.
	 *
	 * @param   bool  $verify  The verify mode to be used
	 * @return  Generator_Type  This instance
	 */
	public function verify($verify = TRUE)
	{
		if ($verify !== NULL)
		{
			$this->_verify = (bool) $verify;
		}

		return $this;
	}

	/**
	 * Sets individual template parameter values.
	 *
	 * @param   string  $name   The parameter name
	 * @param   string  $value  The parameter value
	 * @return  Generator_Type  This instance
	 */
	public function set($name, $value)
	{
		$this->_params[$name] = $value;

		return $this;
	}

	/**
	 * Sets the builder instance associated with this type.
	 *
	 * @param   Generator_Builder  $builder  A builder instance
	 * @return  Generator_Type  This instance
	 */	
	public function set_builder(Generator_Builder $builder)
	{
		$this->_builder = $builder;
		return $this;
	}

	/**
	 * Sets the 'blank' parameter for the current template. This allows
	 * generators that support it to skip ay skeleton methods, etc.
	 *
	 * @param   bool  $blank  The 'blank' parameter value
	 * @return  Generator_Type  This instance
	 */
	public function blank($blank = TRUE)
	{
		$this->_params['blank'] = (bool) $blank;

		return $this;
	}

	/**
	 * A convenience method to support the fluent interface, ensures that any
	 * associated builder can be referenced properly and halts execution if it
	 * can't be, since this is usually the safest option.
	 *
	 * @throws  Generator_Exception  On missing builder
	 * @return  Generator_Builder    The associated builder instance
	 */
	public function builder()
	{
		if ( ! $this->_builder)
			throw new Generator_Exception('No builder is associated with this type');

		return $this->_builder;
	}

	/**
	 * Adds an entry to the curret generator log, or returns the whole log.
	 *
	 * @param   string  $status  The status message for this entry
	 * @param   string  $item    The item affected
	 * @return  array|Generator_Type  The current log or this instance
	 */	
	public function log($status = NULL, $item = NULL)
	{
		if ($status === NULL)
			return $this->_log;

		$this->_log[] = array('status' => $status, 'item' => $item);

		return $this;
	}

	/**
	 * If the current filename has not been set, this method will try to guess
	 * it based on current module, folder and name values.
	 *
	 * @param   bool  $convert  Should the name be converted to a file path?
	 * @throws  Generator_Exception  On invalid name or base path
	 * @return  string  The guessed filename
	 */
	public function guess_filename($convert = TRUE)
	{
		// We can't continue without a generator name
		if ( ! $this->_name)
			throw new Generator_Exception('Name is required for this type');

		// Why does this constant have to be so damn long?
		$ds = DIRECTORY_SEPARATOR;

		// Determine the base path for the file
		$path = $this->_module ? (MODPATH.$this->_module.$ds) : APPPATH;

		if ($this->_verify AND ! file_exists($path))
			throw new Generator_Exception("Path ':dir' does not exist", array(':dir' => Debug::path($path)));

		// Get the file name, optionally converting it to a path
		$name = $convert ? (str_replace('_', $ds, $this->_name)) : $this->_name;

		// Set and return the full guessed file path
		$file = $path.($this->_folder ? ($this->_folder.$ds) : '').$name;
		$file = $convert ? ($file.EXT) : $file;

		return $this->_file = $file;
	}

	/**
	 * Renders the generator output before saving to the destination file.
	 *
	 * The default implementation uses view templates, but any string may be
	 * returned by child classes that override this method.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		// Create the view with initial parameters
		$view = View::factory($this->_template)->set('name', $this->_name);

		// Merge the default parameters with any set manually
		$params = array_merge($this->_defaults, $this->_params);

		if ($this->_module AND empty($this->_params['package']))
		{
			// Use any module name as the default package name
			$params['package'] = ucfirst($this->_module);
		}

		foreach ($params as $key => $value)
		{
			// Set the view parameters
			$view->set($key, $value);
		}

		// Return the rendered view template
		return ($this->_security ? (Kohana::FILE_SECURITY.PHP_EOL) : '').$view->render();
	}

	/**
	 * Creates the destination item with any rendered output, and records
	 * expected or actual actions in the generator log depending on the
	 * pretend and force modes used.
	 *
	 * @throws  Generator_Exception  On invalid filename
	 * @return  Generator_Type  This instance
	 */
	public function create()
	{
		// We can't continue without a valid filename
		if ( ! $this->_file AND ! $this->guess_filename())
			throw new Generator_Exception('Filename could not be determined');

		// Start a fresh log
		$this->_log = array();

		// Get the parent directory of the item
		$item = $this->_file;
		$directory = dirname($item);

		if ($this->item_exists($directory))
		{
			// The directory exists, no action is needed
			$this->log('exists', $directory);
		}
		else
		{
			$this->log('create', $directory);

			if ( ! $this->_pretend)
			{
				// Create the parent directory
				$this->make_dir($directory);
			}
		}

		if ( ! $this->_force AND $this->item_exists($item))
		{
			// The item won't be replaced if it already exists
			$this->log('exists', $item);
		}
		else
		{
			$this->log('create', $item);

			if ( ! $this->_pretend)
			{
				if ($this instanceof Generator_Type_Directory)
				{
					// Item is a directory
					$this->make_dir($item);
				}
				else
				{
					// Create the destination file with the rendered output
					file_put_contents($item, $this->render());
				}
			}
		}

		return $this;
	}

	/**
	 * Determines whether a file or directory exists, with faked checks of
	 * the current builder list if we're pretending.
	 *
	 * @param   string  $item  The file or directory
	 * @param   string  $mode  The current command mode
	 * @return  bool
	 */
	public function item_exists($item, $mode = Generator::CREATE)
	{
		if ($this->_pretend AND $this->_builder AND $mode == Generator::CREATE)
		{
			// Search the current builder generators for the item path
			foreach ($this->builder()->generators() as $generator)
			{
				// Ignore this generator instance
				if ($generator === $this)
					break;

				// Another generator has this item path
				if (strpos($generator->file(), $item) !== FALSE)
					return TRUE;
			}
		}

		// Otherwise check the filesystem
		clearstatcache();
		return file_exists($item);
	}

	/**
	 * Recursively creates a directory path and applies the proper permissions.
	 *
	 * @param   string  $path  The full directory path
	 * @return  void
	 */	
	public function make_dir($directory)
	{
		mkdir($directory, 0777, TRUE);
		chmod($directory, 0777);
	}

	/**
	 * Deletes the specified file and its parent directory if empty.
	 *
	 * @throws  Generator_Exception  On invalid filename
	 * @return  Generator_Type  This instance
	 */
	public function remove()
	{
		// We can't continue without a valid filename
		if ( ! $this->_file AND ! $this->guess_filename())
			throw new Generator_Exception('Filename could not be determined');

		// Start a fresh log
		$this->_log = array();

		// Set the parent directory
		$directory = dirname($this->_file);

		// Check the file
		if ($this->item_exists($this->_file, Generator::REMOVE))
		{
			$this->log('remove', $this->_file);

			if ( ! $this->_pretend)
			{
				// Delete the file
				unlink($this->_file);
			}
		}

		// Check the parent directory
		if ($this->item_exists($directory, Generator::REMOVE))
		{
			if ( ! $this->dir_is_empty($directory, $this->_file))
			{
				// Don't touch non-empty directories
				$this->log('not empty', $directory);
			}
			else
			{
				$this->log('remove', $directory);

				if ( ! $this->_pretend)
				{
					// Remove the directory
					rmdir($directory);
				}
			}
		}

		return $this;
	}

	/**
	 * Determines whether a directory should be considered empty; in pretend
	 * mode, this means whether it contains items other than $ignored.
	 *
	 * @param   string  $directory  The directory path
	 * @param   string  $target     Item to ignore in the directory
	 * @return  bool
	 */	
	public function dir_is_empty($directory, $ignored = NULL)
	{
		// Get the list of files in the directory
		$files = scandir($directory);

		if ($this->_pretend)
		{
			$ignored = (array) $ignored;

			if ($this->_builder)
			{
				// Get a list of items removed so far from the builder
				$ignored += $this->_builder->get_removed_items();
			}

			foreach ($ignored as $item)
			{
				if ($key = array_search(basename($item), $files))
				{
					// Remove any ignored files from the file list
					unset($files[$key]);
				}
			}
		}

		return (count($files) <= 2);
	}

	/**
	 * Converts strings, including comma-separated lists, to arrays for local
	 * parameter storage.
	 *
	 * @param   string  $values  The given parameter string value(s)
	 * @param   string  $param   The parameter name
	 * @return  bool    TRUE if the values were converted
	 */	
	public function param_to_array($values, $param)
	{
		// String values should be converted internally to arrays
		$values = is_array($values) ? $values : explode(',', $values);

		if ( ! isset($this->_params[$param]))
		{
			// Make sure we have the parameter to work with
			$this->_params[$param] = array();
		}

		// The parameter has been set not to be an array
		if ( ! is_array($this->_params[$param]))
			return FALSE;

		foreach ($values as $value)
		{
			$value = trim($value);
			if ($value != '' AND ! in_array($value, $this->_params[$param]))
			{
				// Add only new values to the stored list
				$this->_params[$param][] = $value;
			}
		}

		return TRUE;
	}

	/**
	 * This magic method passes any undefined method calls to the builder
	 * instance used to create this generator, if any.
	 *
	 * This allows a fluent interface to be used by the builder by chaining
	 * method calls. The downside: care needs to be taken that the proper
	 * instance is being referenced.
	 *
	 * @param   string  $method      The undefined method name
	 * @param   string  $arguments   The undefined method arguments
	 * @throws  Generator_Exception  If no builder instance is available
	 * @return  mixed   Whatever the referenced builder method returns
	 */
	public function __call($method, $arguments)
	{
		if ($this->_builder)
			return call_user_func_array(array($this->_builder, $method), $arguments);

		throw new Generator_Exception("Method :method() is not defined for :class",
			array(':method' => $method, ':class' => get_class($this)));
	}

} // End Generator_Generator_Type
