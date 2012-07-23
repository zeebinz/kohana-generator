<?php

// Set the indentation level
$tabs = (isset($tabs) AND is_int($tabs)) ? str_repeat("\t", $tabs) : '';

// Open doccomment
echo '/**', PHP_EOL;

// Short description
if (isset($short_description)) foreach ( (array) $short_description as $line)
{
	echo $tabs.' * ', $line, PHP_EOL;
}

// Long description
if (isset($long_description))
{
	echo $tabs, ' *', PHP_EOL;
	foreach ( (array) $long_description as $line)
	{
		echo $tabs, ' * ', $line, PHP_EOL;
	}
}

// Spacer
if (isset($short_description) OR isset($long_description))
{
	echo $tabs, ' *', PHP_EOL;
}

// Tags
if (isset($tags)) foreach ( (array) $tags as $tag)
{
	echo $tabs, ' * ', $tag.PHP_EOL;
}

// Close doccomment
echo $tabs, ' */';
