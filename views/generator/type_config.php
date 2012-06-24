
<?php if (empty($values)) { ?>
return array
(
	// Configuration
);
<?php 
} else {
	echo 'return '.str_replace('  ', "\t", var_export($values, TRUE)).';';
}
?> 
