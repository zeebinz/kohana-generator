/**
 * <?php echo ucfirst($type), ' ', $name ?>, cloned from <?php echo $source ?>.
 *
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
<?php 
	echo ( ! empty($modifiers) ? ($modifiers.' '.$type) : $type);
	echo ' ', $name;
	if ( ! empty($extends)) {echo ' extends ', $extends;}
	if ( ! empty($implements)) {echo ' implements ', $implements;}
	if ( ! empty($blank)): echo ' {}'; else : ?> 
{
<?php if ( ! empty($constants)) foreach ($constants as $constant => $c): ?>
	<?php echo $c['comment'] ?> 
	<?php echo $c['declaration'] ?>;

<?php endforeach; ?>
<?php foreach (array('static', 'public', 'abstract', 'other') as $group): ?>
<?php	if (isset($properties[$group])) foreach ($properties[$group] as $property => $p): ?>
	<?php echo $p['doccomment'] ?> 
	<?php echo $p['declaration'] ?>; 

<?php endforeach; ?>
<?php	if (isset($methods[$group])) foreach ($methods[$group] as $method => $m): ?>
	<?php echo $m['doccomment'] ?> 
	<?php echo $m['signature'] ?>
<?php if ($m['abstract']): echo ';', PHP_EOL, PHP_EOL; else: ?> 
	{
		<?php echo $m['body'] ?> 
	}

<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
} // End <?php echo $name ?>
<?php endif; ?> 
