<?php

//--------------------------------------------------------------------
// Builds
//--------------------------------------------------------------------
// Our build system supports multiple build scripts that can be used.
// This array holds the alias of the build script as the key, and
// the name of the build class file itself that will be run
// to perform that build.
//
	$config['builds'] = [
		'release'           => 'SprintRelease',
		'postCreateProject' => 'InitialCleaning'
//		'demo'      => 'sprint_demo',
//		'docs'      => 'sprint_docs'
	];

//--------------------------------------------------------------------
// Destination Folders
//--------------------------------------------------------------------
// A list of folders that the results of the build scripts will use
// as their root folder. The key should be the alias of the build,
// as defined in $config['builds']. The value is the relative path
// to the 'build' folder. If this folder does not exist, it will be
// created.
//
	$config['destinations'] = [
		'release'           => '../../SprintBuilds/',
		'postCreateProject' => ''
//		'demo'      => '',
//		'docs'      => ''
	];
