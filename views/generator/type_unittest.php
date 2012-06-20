/**
 * Test case for <?php echo $class_name ?>.
 * 
<?php foreach ($groups as $group): ?>
 * @group      <?php echo $group ?> 
<?php endforeach; ?>
 *
 * @package    <?php echo $package ?> 
 * @category   <?php echo $category ?> 
 * @author     <?php echo $author ?> 
 * @copyright  <?php echo $copyright ?> 
 * @license    <?php echo $license ?> 
 */
class <?php echo $name ?> extends <?php echo $extends ?> 
{
<?php	if (empty($blank)): ?>
	/**
	 * This method is called before any tests are run.
	 */	
	public static function setUpBeforeClass()
	{
	}

	/**
	 * This method is called before each test is run.
	 */
	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * This method is called after each test is run.
	 */
	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Test for Something.
	 *
	 * @covers  Something
	 * @todo    Implement test_something()
	 */
	public function test_something()
	{
		$this->markTestIncomplete('This test has not been implemented yet');
	}

	/**
	 * This method is called after all tests are run.
	 */	
	public static function tearDownAfterClass()
	{
	}
<?php endif; ?>

} // End <?php echo $name ?> 
