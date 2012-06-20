/**
 * Description of <?php echo $name ?>.
 *
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
class <?php echo $name ?> extends <?php echo $extends ?> 
{
<?php	if (empty($blank)): ?>
	/**
	 * Automatically executed before the controller action. Can be used to set
	 * properties, do authorization checks, and execute other custom code.
	 *
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		// Nothing by default
	}
<?php if ( ! empty($actions)) foreach ($actions as $action) : ?>

	/**
	 * <?php echo ucfirst($action) ?> action.
	 * 
	 * @return  void
	 */
	public function action_<?php echo $action ?>()
	{
		// Nothing by default
	}
<?php endforeach; ?> 
	/**
	 * Automatically executed after the controller action. Can be used to
	 * apply transformation to the response, add extra output, and execute
	 * other custom code.
	 * 
	 * @return  void
	 */
	public function after()
	{
		// Nothing by default

		parent::after();
	}
<?php endif; ?>

} // End <?php echo $name ?> 
