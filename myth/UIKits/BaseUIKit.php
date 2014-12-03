<?php

namespace Myth\UIKits;

/**
 * Class BaseUIKit
 *
 * Provides a foundation that other UIKits can build upon, as well as
 * common methods that are ready for use.
 */
abstract class BaseUIKit {

    /**
     * Bucket for methods to control their current state between method calls.
     * @var array
     */
    protected $states = [];

    protected $name = '';

    /**
     * Attached to nav items that are considered active.
     * @var string
     */
    protected $active_class = 'active';

    //--------------------------------------------------------------------

    public function name()
    {
        return $this->name;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Grids
    //--------------------------------------------------------------------

    /**
     * Creates a row wrapper of HTML. We would have simple returned the
     * the class for it, but some frameworks use a completely different
     * approach to rows and columns than the reference Bootstrap and Foundation.
     *
     * @param array $options
     * @return mixed
     */
    abstract public function row($options=[], \Closure $c);

    //--------------------------------------------------------------------

    /**
     * Creates the CSS for a column in a grid.
     *
     * The attribute array is made up of key/value pairs with the
     * key being the size, and the value being the number of columns/offset
     * in a 12-column grid.
     *
     * Note that we currently DO NOT support offset columns.
     *
     * Valid sizes - 's', 'm', 'l', 'xl', 's-offset', 'm-offset', 'l-offset', 'xl-offset'
     *
     * Please note that that sizes are different than in Bootstrap. For example, for a 'xs'
     * column size in Bootstrap, you would use 's' here. 'sm' = 'm', etc.
     *
     * @param array $attributes
     * @return mixed
     */
    abstract public function column($options=[], \Closure $c);

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Navigation
    //--------------------------------------------------------------------

    /**
     * Generates the container code for a navbar, typically used along the
     * top of a page.
     *
     * @param array    $options
     * @param callable $c
     * @return string
     */
    abstract public function navbar($options=[], \Closure $c);

    //--------------------------------------------------------------------

    /**
     * Builds the HTML for the Title portion of the navbar. This typically
     * includes the code for the hamburger menu on small resolutions.
     *
     * @param        $title
     * @param string $url
     * @return string
     */
    abstract public function navbarTitle($title, $url='#');

    //--------------------------------------------------------------------

    /**
     * Creates a UL meant to pull to the right within the navbar.
     *
     * Available options:
     *      'class'     - An additional class to add
     *
     * @param array    $options
     * @param callable $c
     * @return string
     */
    abstract public function navbarRight($options=[], \Closure $c);

    //--------------------------------------------------------------------

    /**
     * Creates a single list item for use within a nav section.
     *
     * @param       $title
     * @param       $url
     * @param array $options
     * @return string
     */
    abstract public function navItem($title, $url, $options=[], $isActive=false);

    //--------------------------------------------------------------------

    /**
     * Builds the shell of a Dropdown button for use within a nav area.
     *
     * @param          $title
     * @param array    $options
     * @param callable $c
     */
    abstract public function navDropdown($title,$options=[], \Closure $c);

    //--------------------------------------------------------------------

    /**
     * Creates a divider for use within a nav list.
     *
     * @return string
     */
    abstract public function navDivider();

    //--------------------------------------------------------------------

    /**
     * Creates a list of nav items to function as breadcrumbs for a site.
     *
     * @param array    $options
     * @param callable $c
     * @return mixed
     */
    abstract public function breadcrumb($options=[], \Closure $c);

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Buttons
    //--------------------------------------------------------------------

    /**
     * Creates a simple button.
     *
     * $style can be 'default', 'primary', 'success', 'info', 'warning', 'danger'
     * $size can be 'default', 'small', 'xsmall', 'large'
     *
     * @param       $title
     * @param string $style
     * @param array $options
     * @return mixed
     */
    abstract public function button($title, $style='default', $size='default', $options=[]);

    /**
     * Creates a simple link styled as a button.
     *
     * $style can be 'default', 'primary', 'success', 'info', 'warning', 'danger'
     * $size can be 'default', 'small', 'xsmall', 'large'
     *
     * @param       $title
     * @param string $url
     * @param string $style
     * @param array $options
     * @return mixed
     */
    abstract public function buttonLink($title, $url='#', $style='default', $size='default', $options=[]);

    /**
     * Creates button groups wrapping HTML.
     *
     * @param          $options
     * @param callable $c
     * @return mixed
     */
    abstract public function buttonGroup($options, \Closure $c);

    /**
     * Creates the button bar wrapping HTML.
     *
     * @param          $options
     * @param callable $c
     * @return mixed
     */
    abstract public function buttonBar($options, \Closure $c);

    /**
     * Creates a button that also has a dropdown menu. Also called Split Buttons
     * by some frameworks.
     *
     * @param        $title
     * @param string $style
     * @param string $size
     * @param array  $options
     * @param callable $c
     * @return mixed
     */
    abstract public function buttonDropdown($title, $style='default', $size='default', $options=[], \Closure $c);

    //--------------------------------------------------------------------
    // Notices
    //--------------------------------------------------------------------

    /**
     * Creates an 'alert-box' style of notice grid.
     *
     * $style can be 'default', 'primary', 'success', 'info', 'warning', 'danger'
     *
     * @param $content
     * @param string $style
     * @param bool $closable
     * @return mixed
     */
    abstract public function notice($content, $style='success', $closable=true);

	//--------------------------------------------------------------------
	// Forms
	//--------------------------------------------------------------------

	/**
	 * Creates the wrapping code around a form input. Will generate the
	 * label for you, but you will still need to supply the input itself
	 * since those are fairly standard HTML.
	 *
	 * @param $label_text
	 * @param array $options
	 * @param callable $c
	 *
	 * @return mixed
	 */
	abstract public function inputWrap($label_text, $options=[], \Closure $c);

    //--------------------------------------------------------------------
    // Utility Methods
    //--------------------------------------------------------------------

    /**
     * Helper method to run a Closure and collect the output of it.
     *
     * @param callable $c
     * @return string
     */
    protected function runClosure(\Closure $c)
    {
        if (! is_callable($c)) return '';

        ob_start();
        $c();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Provides a single method call to get the $classes, $id, and $attributes
     * from the options array.
     *
     * @param        $options
     * @param string $initial_classes
     * @param bool   $fullClassString
     * @return array
     */
    protected function parseStandardOptions($options, $initial_classes='', $fullClassString=false)
    {
        return [
            $this->buildClassString($initial_classes, $options, $fullClassString),
            $this->buildIdFromOptions($options),
            $this->buildAttributesFromOptions($options)
        ];
    }

    //--------------------------------------------------------------------

    /**
     * Sets the element that is to be considered the active item. This is
     * based on the navItem's $title so it must match, though it is NOT
     * case sensitive.
     *
     * @param $title
     * @return mixed
     */
    public function setActiveNavItem($title)
    {
        $this->states['activeNavItem'] = strtolower($title);
    }

    //--------------------------------------------------------------------

    /**
     * Combines an initial classes string with a 'class' item that
     * might be available within the options array.
     *
     * If 'buildEntireString' is TRUE will return the string with the 'class=""' portion.
     * Otherwise, just returns the raw classes.
     *
     * @param string $initial
     * @param array $options
     * @return array
     */
    protected function buildClassString($initial, $options, $buildEntireString=false)
    {
        $classes = explode(' ', $initial);

        if (isset($options['class']))
        {
            $classes = array_merge($classes, explode(' ', $options['class']));
        }

        if (isset($this->states['activeNavItem']) && isset($this->states['activeNavTitle']) &&
            $this->states['activeNavItem'] == strtolower($this->states['activeNavTitle']))
        {
            $classes[] = $this->active_class;
        }

        $classes = implode(' ', $classes);

        // Substitute the active class for a placeholder.
        $classes = str_replace('{active}', $this->active_class, $classes);

        return $buildEntireString ? "class='{$classes}'" : $classes;
    }
    //--------------------------------------------------------------------

    /**
     * Checks the options array for an ID and returns the entire string.
     *
     * Example Return:
     *      id='MyID'
     *
     * @param $options
     * @return string
     */
    protected function buildIdFromOptions($options)
    {
        return isset($options['id']) ? "id='{$options['id']}'" : ' ';
    }

    //--------------------------------------------------------------------

    /**
     * Parses out attributes from the options array. The attributes array
     * should all contain no key names, only values, so:
     *
     * 'attributes' => [
     *      'style="width:100%",
     *      'required'
     * ]
     *
     * @param $options
     * @return string
     */
    protected function buildAttributesFromOptions($options)
    {
        if (isset($options['attributes']) && ! is_array($options['attributes']))
        {
            $options['attributes'] = [ $options['attributes'] ];
        }

        return isset($options['attributes']) ? implode($options['attributes']) : '';
    }

    //--------------------------------------------------------------------
}