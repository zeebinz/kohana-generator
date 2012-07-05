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
<?php	if (isset($methods)) foreach ($methods as $method => $m): ?> 
	<?php echo $m['doccomment'] ?> 
	<?php echo $m['signature'] ?> 
	{
		// Method implementation
	}
<?php endforeach; ?> 
} // End <?php echo $name ?>
<?php } ?> 
