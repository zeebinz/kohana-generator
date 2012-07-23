/**
 * Transparent extension of <?php echo $source ?>.
 *
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
<?php
	if ( ! empty($abstract)) {echo 'abstract ';}
	echo $class_type, ' ', $name;
	if ( ! empty($extends)) {echo ' extends ', $extends;}
	echo ($class_type == 'trait' ? " {use $source;}" : ' {}');
?> 
