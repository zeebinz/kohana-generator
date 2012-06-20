<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Generator Directory type.
 *
 * @package    Generator 
 * @category   Generator/Types 
 * @author     Zeebee 
 * @copyright  (c) 2012 Zeebee 
 * @license    BSD revised 
 */
class Generator_Generator_Type_Directory extends Generator_Type 
{
	protected $_force = FALSE;

	/**
	 * Ensures that the directory is not converted to a file name.
	 *
	 * @param   bool    $convert  Should the name be converted to a file path?
	 * @throws  Generator_Exception  On invalid name or base path
	 * @return  string  The guessed filename
	 */
	public function guess_filename($convert = TRUE)
	{
		return parent::guess_filename(FALSE);
	}

	/**
	 * Existing directories should not be replaced, so don't allow the force
	 * mode to be changed.
	 *
	 * @param   bool  $force  The force mode to be used
	 * @return  Generator_Type  This instance
	 */
	public function force($force = TRUE)
	{
		return $this;
	}

	/**
	 * This is a directory, so just return a message for inspect() output.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return 'This is a directory type, nothing to render.';
	}

	/**
	 * Deletes the directory and its parent if not empty.
	 *
	 * @throws  Generator_Exception  On invalid directory name
	 * @return  Generator_Type  This instance
	 */
	public function remove()
	{
		// We can't continue without a valid path
		if ( ! $this->_file AND ! $this->guess_filename())
			throw new Generator_Exception('Directory name could not be determined');

		// Start a fresh log
		$this->_log = array();

		// Set the directories
		$dir = $this->_file;
		$parent = dirname($this->_file);
		$empty = FALSE;

		// Check the child directory
		if ($this->item_exists($dir, Generator::REMOVE))
		{
			if ( ! $this->dir_is_empty($dir))
			{
				// The directory isn't empty, so leave it be
				$this->log('not empty', $dir);
			}
			else
			{
				$this->log('remove', $dir);
				$empty = TRUE;

				if ( ! $this->_pretend)
				{
					// Remove the directory
					rmdir($dir);
				}
			}
		}

		// Check the parent directory
		if ($this->item_exists($parent, Generator::REMOVE))
		{
			if ( ! $empty OR ! $this->dir_is_empty($parent, $dir))
			{
				// The directory isn't empty, so leave it be
				$this->log('not empty', $parent);
			}
			else
			{
				$this->log('remove', $parent);

				if ( ! $this->_pretend)
				{
					// Remove the directory
					rmdir($parent);
				}
			}
		}

		return $this;
	}

} // End Generator_Generator_Type_Directory 
