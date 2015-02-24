# CLI Controllers

You can create controllers intended for use only on the command line by extending the `Myth\Controllers\CLIController`. This will restrict the controller to being called from anywher other than the command line, and load up the CLI library, which provides several tools to make working with the command prompt a pleasant experience.

CLI Controllers can be used for creating tools that can be called from third-party scripts on your server. You can create tools that handle redundant chores for yourself, or add new features to your code, like the `database tools` that have been provided with Sprint.

	use \Myth\CLI as CLI;
	class myTools extends Myth\Controllers\CLIController {
		...
	}

## Calling A CLI Controller
Calling a CLIController-based tool uses the standard CodeIgniter CLI approach. This maps the arguments on the command line to the same that you would enter in a web browser. If you would reach your module/controller at

	http://mysite.com/database/seed

then the relevant command line would be

	php sprint database seed

You can access any of your controllers this way, though the output won't be pretty in most cases.

## Providing User Help
Every command line tool should provide at least the command list in order to work with the `sprint` cli tool. This requires that the index method is reserved for use by the CLIController. You should fill out the `$descriptions` class variable with an array of method names and their help. 

	protected $descriptions = [
		'migrate' => ['migrate [$to]', 'Runs the migrations up or down until schema at version \$to']
	];

The key of the `$descriptions` array is the name of the command. This should match the name of the method calling it. Each 'value' is another array with two items. The first element is the usage string, which shows the command along with any required and/or optional commands. The second element is a short description of what the command does.

### Command Help
For each command, you can provide longer help strings that the `sprint` cli tool can access by filling in the `$long_descriptions` array. The array's keys are the same as for descriptions, above: the method name. The 'value' is the help description given. 

	protected $long_descriptions = [
		'migrate'	=> '...'
	];
