/**
 * Generator <?php echo $type ?> type.
 *
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
class <?php echo $name.' extends '.$extends;
	if ( ! empty($blank)) {echo ' {}';} else { ?> 
{
	protected $_template = 'generator/<?php echo $type_template ?>';
	protected $_folder   = 'classes';

	/**
	 * Finalizes parameters and renders the template.
	 *
	 * @return  string  The rendered output
	 */
	public function render()
	{
		return parent::render();
	}

} // End <?php echo $name ?>
<?php } ?> 
