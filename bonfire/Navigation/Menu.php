<?php

namespace Bonfire\Navigation;

/**
 * Class Menu
 *
 * @package Bonfire\Navigation
 */
class Menu {


    protected $attributes = '';

    protected $name = null;
    /**
     * The child items of this menu.
     * Note that each item can contain more items for nested menus.
     *
     * @var array
     */
    protected $items = [];

    //--------------------------------------------------------------------

    /**
     * Creates the new menu.
     *
     * @param $menu_name
     */
    public function __construct($name)
    {
        $this->name = $this->prepareName($name);
    }

    //--------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function name()
    {
        return $this->name;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Items
    //--------------------------------------------------------------------

    /**
     * Returns the raw array of items
     *
     * @return array
     */
    public function items()
    {
        return $this->items;
    }

    //--------------------------------------------------------------------

    /**
     * Adds a MenuItem to our list of items. This could be either
     *
     *
     * @param MenuItem $item
     * @return $this
     */
    public function addItem(MenuItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    //--------------------------------------------------------------------

    public function itemNamed($name)
    {
        $name = $this->prepareName($name);

        foreach ($this->items as $item)
        {
            if ($item->name() == $name)
            {
                return $item;
            }
        }
    }

    //--------------------------------------------------------------------

	/**
	 * A convenience method to create a new item if one with that
	 * name doesn't already exist.
	 *
	 * @param MenuItem $item
	 */
	public function ensureItem(MenuItem $item)
	{
	    if (! $this->itemNamed( $item->name() ))
	    {
		    $this->addItem($item);
	    }
	}

	//--------------------------------------------------------------------


    /**
     * Reports whether any items exist or not.
     *
     * @return bool
     */
    public function hasItems()
    {
        return (bool)count($this->items);
    }

    //--------------------------------------------------------------------

    /**
     * Adds a child menu item to an existing parent item. If the parent
     * item doesn't exist, one will be created.
     *
     * @param MenuItem $child
     * @param          $parent
     */
    public function addChild(MenuItem $child, $parent)
    {
        // If no parent item exists, create one.
        $parent_item = $this->itemNamed($parent);

        if (! $parent_item)
        {
            $this->addItem( new MenuItem($parent) );
            $parent_item = $this->itemNamed($parent);
        }

        $parent_item->addChild($child);

        return $this;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Sorting
    //--------------------------------------------------------------------

    public function sortBy($key, $dir='asc')
    {
        $method = $key .'_sorter';

        if (! method_exists($this, $method))
        {
            throw new \RuntimeException('Unable to sort by method: '. $key);
        }

        $this->items = $this->{$method}($this->items);

        if ($dir == 'desc')
        {
            $this->items = array_reverse($this->items);
        }

        return $this;
    }

    //--------------------------------------------------------------------




    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    /**
     * Helper method to cleanup our name to a common format.
     *
     * @param $name
     * @return string
     */
    private function prepareName($name)
    {
        return strtolower( strip_tags($name) );
    }

    //--------------------------------------------------------------------

    private function order_sorter($items)
    {
        usort($items, function ($a, $b)
            {
                return $a->order() - $b->order();
            }
        );

        return $items;
    }

    //--------------------------------------------------------------------

    private function name_sorter($items)
    {
        usort($items, function ($a, $b)
            {
                return strnatcmp($a->name(), $b->name() );
            }
        );

        return $items;
    }

    //--------------------------------------------------------------------

    private function title_sorter($items)
    {
        usort($items, function ($a, $b)
            {
                return strnatcmp($a->title(), $b->title() );
            }
        );

        return $items;
    }

    //--------------------------------------------------------------------
}