
return array
(
	// Leave this alone
	'modules' => array(

		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'<?php echo $module ?>' => array(

			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,

			// The name that should show up on the userguide index page
			'name' => '<?php echo $name ?>',

			// A short description of this module, shown on the index page
			'description' => 'Module description.',

			// Copyright message, shown in the footer for this module
			'copyright' => '<?php echo $copyright ?>',
		)	
	)
);
