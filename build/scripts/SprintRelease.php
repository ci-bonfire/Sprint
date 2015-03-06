<?php

use Myth\CLI;

/**
 * Class SprintRelease
 *
 * Responsible for cleaning up our codebase of all development-specific
 * code and prepping it for a release.
 */
class SprintRelease extends BaseBuilder {

	protected $source_path;
	protected $dest_path;

	protected $ignore_files = ['.', '..', '.git', 'vendor', '.idea', '.travis.yml', 'build'];

	public function __construct($destination)
	{
	    $this->source_path = realpath(BASEPATH .'../');

		if (empty($this->source_path))
		{
			CLI::error('Unable to determine the source path.');
			exit(1);
		}

		$this->dest_path = BASEPATH . $destination;
		$this->dest_path = rtrim($this->dest_path, '/ ') .'/'. date('Y-m-d');
	}

	//--------------------------------------------------------------------

	/**
	 * Runs the different tasks
	 */
	public function run()
	{
		$step = 1;

		// Copy the entire codebase to the new folder so we
		// can have something to work with and not wreck our code.
		CLI::write("\tCopying files...");
		$this->copyFolder($this->source_path, $this->dest_path);

		// Clean up all temporary files/folders
		CLI::write("\tClean up temp files...");
		$this->cleanTempFiles();

		CLI::write("\tClean up test folders...");
		$this->cleanTestsFolder();

		CLI::write("\tRemoving application modules...");
		$this->cleanFolder($this->dest_path .'/application/modules', ['index.html', '.htaccess']);

		CLI::write("\tCompressing files...");
		$this->compressFolder($this->dest_path, $this->dest_path .'/Sprint_'. date('Y-m-d') .'.zip');
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Tasks
	//--------------------------------------------------------------------

	private function cleanTempFiles()
	{
		// Remove Log Files
		$this->cleanFolder($this->dest_path .'/application/logs', ['index.html', '.htaccess']);

		// Cache Files
		$this->cleanFolder($this->dest_path .'/application/cache', ['index.html', '.htaccess']);
	}

	//--------------------------------------------------------------------

	public function cleanTestsFolder()
	{
		// Remove coverage Files
		$this->cleanFolder($this->dest_path .'/tests/_output', ['.gitignore']);

		// Remove our Acceptance tests
		$this->cleanFolder($this->dest_path .'/tests/acceptance/myth');
		rmdir($this->dest_path .'/tests/acceptance/myth');

		// Remove our Unit tests
		$this->cleanFolder($this->dest_path .'/tests/unit/myth');
		rmdir($this->dest_path .'/tests/unit/myth');
	}

	//--------------------------------------------------------------------



}