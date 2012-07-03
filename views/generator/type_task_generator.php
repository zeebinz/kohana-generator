/**
 * Description of <?php echo $name ?>.
 *
<?php if ( ! empty($help)): ?>
 * Additional options:
 *
 *   --option1=VALUE1
 *
 *     Description of this option.
 *
 *   --option2=VALUE2
 *
 *     Description of this option.
 *
 * Examples
 * ========
 * minion task --option1=value1
 *
 *     Description of this example.
 *
 * minion task --option1=value1 --option2=value2
 *
 *     Description of this example.
 *
<?php endif; ?>
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
class <?php
	echo $name;
	echo ' extends '.( ! empty($extends) ? $extends : 'Task_Generate');
	if ( ! empty($blank)) {echo ' {}';} else { ?> 
{
	/**
	 * @var  array  The task options
	 */
	protected $_options = array(
		'name' => '',
	);

	/**
	 * Validates the task options.
	 *
	 * @param  Validation  $validation  The validation object to add rules to
	 * @return Validation
	 */
	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('name', 'not_empty');
	}

	/**
	 * Creates a generator builder with the given configuration options.
	 *
	 * @param  array  $options  The selected task options
	 * @return Generator_Builder
	 */
	public function get_builder(array $options)
	{
		$builder = Generator::build();

		return $builder->prepare();
	}

} // End <?php echo $name ?>
<?php } ?> 
