# Using the Generator Reflector

If you've been running the [Minion tasks](tasks) for creating classes or interfaces, you may have seen that some commands can speed up the creation of your resources by automatically adding any skeleton methods that may be needed - such as for abstract methods declared by any parent classes, or for any interfaces that you're implementing. For instance:

	./minion generate:class --name=Foo --implement=Countable --inspect

	... 

	class Foo implements Countable
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
		 * @param   string  $param  Some string
		 * @return  void
		 */
		public function some_method($param)
		{
			// Method implementation
		}

		/**
		 * Implementation of Countable::count
		 *
		 * @return  void  **Needs editing**
		 */
		public function count()
		{
			// Method implementation
		}

	} // End Foo

Notice how a skeleton implementation of the `count()` method from the `Countable` interface has been added without any extra work.  You can now load your class up without errors, even though it doesn't do much of anything yet.

Behind the scenes, the command uses the `Generator_Reflector` class, which is a very simple wrapper for PHP's various Reflection tools.  There are lots of alternatives out there that are more flexible and provide more functions (see [Zend_Reflection](http://framework.zend.com/manual/en/zend.reflection.html), for example). But the goal of this class is to provide all the information that the view templates need in as painless a way as possible. For example:

	$reflection = new Generator_Reflector('Kohana_Controller');
	
	// Outputs: 'protected function check_cache($etag = NULL)'
	echo $reflection->get_method_signature('check_cache');

Nice and simple. There are also methods for getting parsable string representations of constant and property declarations as well as method parameters, getting type names for doccomments, etc. - basically the things that are fiddly to do manually using PHP's Reflection classes.  Here's another typical example using the more verbose syntax:

	$reflection = new Generator_Reflector;
	$reflection
		->source('SplSubject')
		->type(GENERATOR_REFLECTOR::TYPE_INTERFACE)
		->analyze();
	
	foreach ($reflection->get_methods() as $method => $m)
	{
		echo "Method name: $method\n";

		// Pretty print the list of method parameters
		print_r($m['params']);
		
		// Or output the parameter signatures
		echo $reflection->get_method_param_signatures($method);
	}

The raw info about the methods is returned as an array rather than as different reflection objects. This is by design to keep things as simple as possible and everything in one class, but it does mean that you need to browse the source code if you want to use the `Generator_Reflector` class.  It's not long, and the arrays storing the reflection info are declared as explicitly as possible in the different `parse_reflection_*` methods.  Otherwise you can look at how some existing tasks use the class for cloning different sources.

## Cloning Classes and Interfaces

Reflection can also be used to copy parameter and method definitions from existing classes (or interfaces) in a skeleton format ready for you to implement.  The `Generator_Type_Clone` class manages all of this internally, and some Minion tasks include the `--clone` option to make things even easier. Try it with this command:

	./minion generate:class --name=Foo --clone=Kohana_Controller_Template --reflect

	/**
	 * Class Foo, cloned from Kohana_Controller_Template.
	 *
	 * @package    package
	 * @category   category
	 * @author     Author
	 * @copyright  (c) 2012 Author
	 * @license    License
	 */
	abstract class Foo extends Controller
	{
		/**
		 * @var  View  page template
		 */
		public $template = 'template';

		/**
		 * @var  boolean  auto render template
		 **/
		public $auto_render = TRUE;

		/**
		 * Loads the template [View] object.
		 */
		public function before()
		{
			// Implementation of Kohana_Controller_Template::before
		}

		/**
		 * Assigns the template [View] as the request response.
		 */
		public function after()
		{
			// Implementation of Kohana_Controller_Template::after
		}

	} // End Foo

Now we have a copy of `Kohana_Controller_Template` with everything but the class doccomment and the method bodies, ready for you to implement.  You can also import all of the `Controller` members simply by adding the `--inherit` option to the command. It works for internal classes, too:

	./minion generate:class --name=Foo --clone=SplMinHeap --inherit

The `--clone` option is also available for generating user-defined or internal interfaces:

	./minion generate:interface --name=Fooable --clone=ArrayAccess
	
There's a lot more to cloning, but this is one of those cases where trying it out yourself with different sources and options is really the best way of learning all the different things you can do with it. Apart from Kohana's own classes, you could try playing with the [Standard PHP Library](http://www.php.net/manual/en/book.spl.php). There are also some other examples of commands and generated output in the `tests/fixtures` directory.
