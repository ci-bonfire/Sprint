<?php namespace Bonfire\Navigation;

/**
 * Class MenuCollection
 *
 * Provides a simple collection of menus that is accessible from any other file.
 *
 * @package Bonfire\Navigation
 */
class MenuCollection {

	protected static $menus = [];

	//--------------------------------------------------------------------

	/**
	 * Returns a menu object identified by $name. If one doesn't already
	 * exist, then it will create a new one.
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function menu($name)
	{
		$name = strtolower($name);

		if (! array_key_exists($name, self::$menus))
		{
			self::$menus[$name] = new Menu($name);
		}

		return self::$menus[$name];
	}

	//--------------------------------------------------------------------

	/**
	 * Removes a single menu from the collection.
	 *
	 * @param $name
	 */
	public function deleteMenu($name)
	{
	    $name = strtolower($name);

		if (array_key_exists($name, self::$menus))
		{
			unset(self::$menus[$name]);
		}
	}

	//--------------------------------------------------------------------


}