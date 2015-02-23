<?php
use \Bonfire\Navigation\MenuItem as MenuItem;
use \Bonfire\Navigation\MenuCollection as MenuCollection;

class usersModule extends \Bonfire\Module {

	protected $name = 'Users';
	protected $version = '1.0';

	//--------------------------------------------------------------------

	public function initAdmin()
	{
		// Add the main menu item
		$menu = MenuCollection::menu('admin');

		$item = new MenuItem('users', 'Users', site_url( \Myth\Route::getAreaName('admin') .'/users'), 'fa-group' );
		$item->setOrder(50);

		$menu->addItem( $item );
	}

	//--------------------------------------------------------------------


}