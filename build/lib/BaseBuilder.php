<?php

use Myth\CLI;

class BaseBuilder {

	protected $ignore_files = ['.', '..', '.git', 'vendor', '.idea', '.travis.yml', 'tests'];

	protected $ci;

	//--------------------------------------------------------------------

	public function __construct($ci=null)
	{
	    $this->ci =& $ci;
	}

	//--------------------------------------------------------------------

	/**
	 * Copies the entire contents of a single folder
	 * into a source folder, recursively.
	 *
	 * @param $source
	 * @param $destination
	 */
	public function copyFolder($source, $destination)
	{
		$dir = opendir($source);

		@$this->ensureFolder($destination);

		while (false !== ( $file = readdir($dir)) )
		{
			if ( ! in_array($file, $this->ignore_files) )
			{
				if ( is_dir($source .'/'. $file) )
				{
					$this->copyFolder($source .'/'. $file, $destination .'/'. $file);
				}
				else {
					copy($source .'/'. $file, $destination .'/'. $file);
				}
			}
		}
		closedir($dir);
	}

	//--------------------------------------------------------------------

	/**
	 * Creates the folder if it doesn't already exist.
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	public function ensureFolder($path)
	{
	    if (is_dir($path))
	    {
		    return true;
	    }

		return mkdir($path, 0777, true);
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes all files and folders, recursively, within $path,
	 * except for any files listed in the $leave_files array.
	 *
	 * @param $path
	 * @param array $leave_files
	 */
	public function cleanFolder($path, $leave_files=[])
	{
		foreach (glob("{$path}/*") as $file)
		{
			if (in_array(basename($file), $leave_files))
			{
				continue;
			}

			if (is_dir($file))
			{
				$this->cleanFolder($file, $leave_files);
				rmdir($file);
			}
			else
			{
				unlink($file);
			}
		}
	}

	//--------------------------------------------------------------------

	public function compressFolder($source, $destination, $include_dir=false)
	{
		if (! extension_loaded('zip') ) {
			CLI::error('ZipArchive extension is required.');
			exit(1);
		}

		if (! file_exists($source)) {
			CLI::error('Source folder not found for zipping.');
			exit(1);
		}

		if (file_exists($destination))
		{
			unlink ($destination);
		}

		$zip = new ZipArchive();
		if (! $zip->open($destination, ZIPARCHIVE::CREATE))
		{
			CLI::error('Unknown error opening zip file.');
			exit(1);
		}

		$source = str_replace('\\', '/', realpath($source));

		if (is_dir($source) === true)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			if ($include_dir) {

				$arr = explode("/",$source);
				$maindir = $arr[count($arr)- 1];

				$source = "";
				for ($i=0; $i < count($arr) - 1; $i++) {
					$source .= '/' . $arr[$i];
				}

				$source = substr($source, 1);

				$zip->addEmptyDir($maindir);
			}

			foreach ($files as $file)
			{
				$file = str_replace('\\', '/', $file);

				// Ignore "." and ".." folders
				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..', '.DS_Store')) )
					continue;

				$file = realpath($file);

				if (is_dir($file) === true)
				{
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				}
				else if (is_file($file) === true)
				{
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
				}
			}
		}
		else if (is_file($source) === true)
		{
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}

	//--------------------------------------------------------------------



}