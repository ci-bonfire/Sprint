<?php

/*
|--------------------------------------------------------------------
| Docs Cache Time
|--------------------------------------------------------------------
| The number of minutes that a full-page cache of the docs should
| last for. This only takes effect if the application has caching
| with an engine other than 'dummy'.
*/
$config['docs.cache_time'] = 0;

/*
|--------------------------------------------------------------------
| Docs Folders
|--------------------------------------------------------------------
| Lists the folders that docs should be searched for in. The expected
| format is:
|       array(
|           'alias' => 'path'
|       );
|
| The 'alias' is used to match against the URI and specifies which
| folder the docs are expected to be found in.
|
| The path is expanded through realpath() later and will be ignored
| if realpath cannot find/read the folder.
*/
$config['docs.folders'] = [
    'application'   => APPPATH .'docs',
    'developer'     => APPPATH .'../myth/_docs_src'
];

/*
|--------------------------------------------------------------------
| Docs Folders
|--------------------------------------------------------------------
| The name of the theme that the docs are rendered with. Must match
| the folder name of the theme.
*/
$config['docs.theme'] = 'docs';

/*
|--------------------------------------------------------------------
| Default Group
|--------------------------------------------------------------------
| Sets the default group that the docs will redirect to if no area is
| provided. Must match the alias of one of the paths in 'docs.folders'.
*/
$config['docs.default_group'] = 'developer';

/*
|--------------------------------------------------------------------
| File Extension
|--------------------------------------------------------------------
| The file extension that all docs are expected to have. Currently,
| we only support Markdown files, so the extension defaults to '.md'.
|
| If you change it, ensure that it includes the period (.).
*/

$config['docs.extension']    = '.md';

/*
 * If true, the 'developer' docs will be displayed in environments other than
 * the development environment.
 */
$config['docs.show_dev_docs'] = true;

/*
 * If true, the 'application' specific documentation will be shown.
 */
$config['docs.show_app_docs'] = true;

/*
 * Environments in which displaying the docs is permitted. If the environment
 * is not included in the array, an error message will be displayed and the user
 * will be redirected to the site's base URL.
 */
$config['docs.permitted_environments'] = array('development', 'testing', 'production');