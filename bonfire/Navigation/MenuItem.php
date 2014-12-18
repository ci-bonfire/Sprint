<?php

namespace Bonfire\Navigation;

/**
 * Class MenuItem
 *
 * @package Bonfire\Libraries\Navigation
 */
class MenuItem {

    protected $name = null;

    protected $title = null;

    protected $link = null;

	protected $icon = null;

    protected $children = null;

    protected $attributes = null;

    protected $order = 0;

    //--------------------------------------------------------------------

    public function __construct($name, $title=null, $link=null, $icon=null)
    {
        $this->name = $this->prepareName($name);

        $this->title = is_null($title) ? ucwords($this->name) : $title;

        $this->link = $link;

	    $this->icon = $icon;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the name(slug) of this item.
     *
     * @return null|string
     */
    public function name()
    {
        return $this->name;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the current Title set for this item.
     *
     * @return null|string
     */
    public function title()
    {
        return $this->title;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the currently set link for this item.
     *
     * @return null
     */
    public function link()
    {
        return $this->link;
    }

    //--------------------------------------------------------------------

	/**
	 * Returns the currently set icon for this item.
	 *
	 * @return mixed
	 */
	public function icon()
	{
	    return $this->icon;
	}

	//--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Attributes
    //--------------------------------------------------------------------

    /**
     * Retrieves an existing attribute.
     *
     * @param $name
     * @return null
     */
    public function attribute($name)
    {
        if (! is_string($name))
        {
            return false;
        }

        $name = strtolower($name);

        return isset($this->attributes[$name]) ? $this->attributes[$name] : NULL;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the raw attributes as set.
     *
     * @return array|null
     */
    public function attributes()
    {
        return $this->attributes;
    }

    //--------------------------------------------------------------------


    /**
     * Sets an attribute for this item. If an existing attribute of this
     * name exists, will overwrite the value.
     *
     * @param        $name
     * @param string $value
     * @return $this
     */
    public function setAttribute($name, $value='')
    {
        if (! is_string($name))
        {
            return false;
        }

        $this->attributes[ strtolower($name) ] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Adds a new value to an attribute. If the attribute isn't set yet,
     * it adds it. If it is set, it creates an array and merges the two.
     *
     * @param        $name
     * @param string $value
     * @return $this
     */
    public function mergeAttribute($name, $value='')
    {
        if (! is_string($name))
        {
            return false;
        }

        $name = strtolower($name);

        // If the attribute isn't set, then we'll create it as a string.
        if (! isset($this->attributes[$name]))
        {
            return $this->setAttribute($name, $value);
        }

        // If it does exist, and isn't an array already, we'll need to make it one
        if (isset($this->attributes[$name]) && ! is_array($this->attributes[$name]))
        {
            $this->attributes[$name] = [ $this->attributes[$name] ];
        }

        // Merge our value in
        $this->attributes[$name][] = $value;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Removes an attribute.
     *
     * @param $name
     * @return $this|bool
     */
    public function unsetAttribute($name)
    {
        if (! is_string($name))
        {
            return false;
        }

        $name = strtolower($name);

        unset($this->attributes[$name]);

        return $this;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Order
    //--------------------------------------------------------------------

    /**
     * Sets the order the item should appear in the collection.
     * Will often be reset by the Menu sorting.
     *
     * @param int $order
     */
    public function setOrder($order=0)
    {
        $this->order = (int)$order;
    }

    //--------------------------------------------------------------------

    /**
     * @return int
     */
    public function order()
    {
        return (int)$this->order;
    }

    //--------------------------------------------------------------------




    //--------------------------------------------------------------------
    // Children
    //--------------------------------------------------------------------

    /**
     * Returns the raw list of child items.
     *
     * @return mixed
     */
    public function children()
    {
        return $this->children;
    }

    //--------------------------------------------------------------------


    /**
     * Adds a child menu item to this item. Allows us to create
     * groups of links within a menu.
     *
     * @param MenuItem $item
     * @return $this
     */
    public function addChild(MenuItem $item)
    {
        if (! is_array($this->children))
        {
            $this->children = [];
        }

        $this->children[] = $item;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Finds and returns a child with a matching name.
     *
     * @param $name
     * @return null
     */
    public function childNamed($name)
    {
        $name = $this->prepareName($name);

        foreach ($this->children as $child)
        {
            if ($child->name() == $name)
            {
                return $child;
            }
        }

        return NULL;
    }

    //--------------------------------------------------------------------

    public function hasChildren()
    {
        return (bool)count($this->children);
    }

    //--------------------------------------------------------------------


    //--------------------------------------------------------------------
    // Private Methods
    //--------------------------------------------------------------------

    private function prepareName($name)
    {
        return strtolower( strip_tags($name) );
    }
    //--------------------------------------------------------------------

}