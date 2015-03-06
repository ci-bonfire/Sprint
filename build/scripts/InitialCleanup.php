<?php

use Myth\CLI;
use Myth\Forge\FileKit;

/**
 * Class SprintRelease
 *
 * Responsible for cleaning up our codebase of all development-specific
 * code and prepping it for a release.
 */
class InitialCleanup extends BaseBuilder {

	protected $source_path;
	protected $dest_path;

	protected $ignore_files = ['.', '..', '.git', 'vendor', '.idea', '.travis.yml', 'build'];

	public function __construct($destination, $ci=null)
	{
		parent::__construct($ci);

	    $this->source_path = realpath(BUILDBASE .'../');

		if (empty($this->source_path))
		{
			CLI::error('Unable to determine the source path.');
			exit(1);
		}

		$this->dest_path = $this->source_path;
	}

	//--------------------------------------------------------------------

	/**
	 * Runs the different tasks
	 */
	public function run()
	{
		// Clean up all temporary files/folders
		CLI::write("\tClean up temp files...");
		$this->cleanTempFiles();

		CLI::write("\tClean up test folders...");
		$this->cleanTestsFolder();

		CLI::write("\tRemoving application modules...");
		$this->cleanFolder($this->dest_path .'/application/modules', ['index.html', '.htaccess']);

		CLI::write("\tGenerating default encryption key for config file...");
		$this->generateEncryptionKey();
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

	/**
	 * Generates an encryption key and inserts it into the
	 * application/config/config.php file.
	 */
	public function generateEncryptionKey()
	{
		$length = 16;

		$this->ci->load->library('Encryption');
		$key = $this->ci->encryption->create_key( $length );

		$kit = new FileKit();

		$kit->replaceIn(BUILDBASE .'../application/config/config.php', 'PLEASE_CHANGE_ME!', $key);
	}

	//--------------------------------------------------------------------



}