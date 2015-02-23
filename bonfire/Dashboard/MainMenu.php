<?php namespace Bonfire\Dashboard;

use Bonfire\Navigation\MenuCollection;

class MainMenu {

	/**
	 * Used to display the main menu in the SourceBar in the admin area.
	 *
	 * Intended to be used from a $themer->call() method.
	 */
	public function display()
	{
		$cacheName = 'dashboard_mainMenu';

		if (! $output = get_instance()->cache->get($cacheName))
		{
			$output = $this->buildMenu();

			// todo save the cache here
		}

		return $output;
	}

	//--------------------------------------------------------------------

	private function buildMenu()
	{
		$output = "<ul>\n";
		$output .= "<li><a href='". site_url( \Myth\Route::getAreaName('admin') ) ."'><i class='fa fa-dashboard fa-lg'></i>Dashboard</a></li>\n";
		$output .= "<li class='spacer'></li>\n";

		/*
		 * Loop through all of the modules, checking for a
		 */
		$menu = MenuCollection::menu('admin');

		if ($menu->hasItems())
		{
			$menu->sortBy('order', 'asc');

			foreach ($menu->items() as $item)
			{
				$output .= $this->renderItem( $item );
			}
		}


		$output .= "</ul>\n";

		return $output;
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a single item and any of it's children.
	 *
	 * @param $item
	 *
	 * @return string
	 */
	private function renderItem( $item, $child=false )
	{
		$output = '';

		// Children should have smaller icons
		$icon_size = $child === true ? '' : 'fa-lg';

		$output .= "<li><a href='{$item->link()}'><i class='fa {$item->icon()} {$icon_size}'></i>{$item->title()}</a>\n";

		if ($item->hasChildren())
		{
			$output .= "<ul>\n";

			foreach ($item->children() as $child)
			{
				$output .= $this->renderItem( $child, true );
			}

			$output .= "</ul>\n";
		}

		$output .= "</li>\n";

		return $output;
	}

	//--------------------------------------------------------------------

}