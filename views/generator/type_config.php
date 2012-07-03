
<?php if (empty($values)) { ?>
return array
(
	// Configuration
);
<?php 
} else {
	$refl = new Generator_Reflector;
	$values = $refl->export_variable($values, TRUE);
	echo "return $values;";
}
?> 
