/**
 * Class <?php echo $name ?>.
 *
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
<?php 
	echo ( ! empty($abstract) ? 'abstract class ' : 'class ');
	echo $name;	
	if ( ! empty($extends)) {echo ' extends '.$extends;} 
	if ( ! empty($implements)) {echo ' implements '.$implements;}
	if ( ! empty($blank)) {echo ' {}';} else { ?> 
{
	/**
	 * @var  string  some string
	 */
	public $some_string;

	/**
	 * Short description.
	 *
	 * Long method description.
	 *
	 * @param  string  $param  Some string
	 * @return void
	 */
	public function some_method($param)
	{
		// Method implementation
	}
<?php	if (isset($methods)) foreach ($methods as $method): ?> 
	/**
	 * Implementation of <?php echo $method['interface'].'::'.$method['name'] ?> 
	 *
<?php if ( ! empty($method['params'])) foreach ($method['params'] as $param => $info): ?>
	 * @param  <?php echo $info['type'].'  $'.$param ?> 
<?php endforeach ?>
	 * @return void  **This line should be edited**
	 */
	<?php echo $method['signature'] ?> 
	{
		// Method implementation
	}
<?php endforeach; ?> 
} // End <?php echo $name ?>
<?php } ?> 
