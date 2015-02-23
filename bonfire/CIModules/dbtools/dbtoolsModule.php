<?php

use Bonfire\Navigation\Menu as Menu;
use Bonfire\Navigation\MenuItem as MenuItem;
use Bonfire\Navigation\MenuCollection as MenuCollection;

/**
 * Class dbtoolsModule
 *
 * Module definition file for the dbtools Module.
 */
class dbtoolsModule extends \Bonfire\Module {

	public $version = '1.0';
	public $name = 'Database Tools';

	//--------------------------------------------------------------------

	public function initAdmin()
	{
		// Add the main menu item
		$menu = MenuCollection::menu('admin');

		$menu->ensureItem( new MenuItem('tools', 'Tools', '#', 'fa-wrench', 90 ) );

		$menu->addChild( new MenuItem('dbtools', 'Database', site_url( \Myth\Route::getAreaName('admin') .'/dbtools' ), 'fa-database' ), 'tools' );
	}

	//--------------------------------------------------------------------


}