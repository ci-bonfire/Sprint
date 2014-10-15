<?php

namespace Myth\UIKits;

/**
 * Class Foundation5UIKit
 *
 * Provides a UIKit designed to work with Foundation 5.
 */
class Foundation extends BaseUIKit {

    //--------------------------------------------------------------------

    public function name()
    {
        return 'Foundation5UIKit';
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Grid
    //--------------------------------------------------------------------

    /**
     * Creates a row wrapper of HTML. We would have simple returned the
     * the class for it, but some frameworks use a completely different
     * approach to rows and columns than the reference Bootstrap and Foundation.
     *
     * @param array $options
     * @return mixed
     */
    public function row($options=[], \Closure $c)
    {
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'row', true);

        $output = "<div {$classes} {$id} {$attributes}>";

        $output .= $this->runClosure($c);

        $output .= "</div>";

        return $output;
    }

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
    public function column($options=[], \Closure $c)
    {
        // Build our classes
        $classes = '';

        foreach ($options['sizes'] as $size => $value)
        {
            switch ($size)
            {
                case 's':
                    $classes .= ' small-'. $value;
                    break;
                case 'm':
                    $classes .= ' medium-'. $value;
                    break;
                case 'l':
                    $classes .= ' large-'. $value;
                    break;
                case 'xl':
                    $classes .= ' large-'. $value;
                    break;
                case 's-offset':
                    $classes .= ' small-offset-'. $value;
                    break;
                case 'm-offset':
                    $classes .= ' medium-offset-'. $value;
                    break;
                case 'l-offset':
                    $classes .= ' large-offset-'. $value;
                    break;
                case 'xl-offset':
                    $classes .= ' large-offset-'. $value;
                    break;
            }
        }

        $classes = $this->buildClassString($classes .' columns', $options, true);

        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        $output = "<div {$classes} {$id} {$attributes}>";

        $output .= $this->runClosure($c);

        $output .= "</div>";

        return $output;
    }

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
    public function navbar($options=[], \Closure $c)
    {
        $output = '';

        /*
         * Open the navbar
         */
        $classes = "top-bar ";

        foreach ($options as $option)
        {
            switch ($option)
            {
                case 'sticky-top':
                    $classes .= " navbar-static-top";
                    break;
                case 'fixed':
                    $classes .= " navbar-fixed-top";
                    break;
            }
        }

        $classes = $this->buildClassString($classes, $options, true);

        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        $output .= "<nav {$classes} {$id} {$attributes} data-topbar>";

        /*
         * Do any user content inside the bar
         */
        $output .= $this->runClosure($c);

        if (isset($this->states['nav-section-open']))
        {
            $output .= "</section>";
            unset($this->states['nav-section-open']);
        }

        /*
         * Close out the navbar
         */
        $output .= '</nav>';

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Builds the HTML for the Title portion of the navbar. This typically
     * includes the code for the hamburger menu on small resolutions.
     *
     * @param        $title
     * @param string $url
     * @return string
     */
    public function navbarTitle($title, $url='#')
    {
        return "<ul class='title-area'>
    <li class='name'>
      <h1><a href='{$url}'>{$title}</a></h1>
    </li>
    <li class='toggle-topbar menu-icon'><a href='#'><span>Menu</span></a></li>
  </ul>";
    }

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
    public function navbarRight($options=[], \Closure $c)
    {
        $output = '';

        if (! isset($this->states['nav-section-open']))
        {
            $output .= "<section class='top-bar-section'>\n";
            $this->states['nav-section-open'] = true;
        }

        // Class
        $classes = $this->buildClassString('right', $options);

        // ID
        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        $output .= "<ul class='{$classes}' {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</ul>\n";

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Creates a UL meant to pull to the left within the navbar.
     *
     * Available options:
     *      'class'     - An additional class to add
     *
     * @param array    $options
     * @param callable $c
     * @return string
     */
    public function navbarLeft($options=[], \Closure $c)
    {
        $output = '';

        if (! isset($this->states['nav-section-open']))
        {
            $output .= "<section class='top-bar-section'>\n";
            $this->states['nav-section-open'] = true;
        }

        // Class
        $classes = $this->buildClassString('left', $options);

        // ID
        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        $output .= "<ul class='{$classes}' {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</ul>\n";

        return $output;
    }

    //--------------------------------------------------------------------

    public function nav()
    {

    }

    //--------------------------------------------------------------------


    /**
     * Creates a single list item for use within a nav section.
     *
     * @param       $title
     * @param       $url
     * @param array $options
     * @return string
     */
    public function navItem($title, $url='#', $options=[], $active=false)
    {
        $options['active'] = $active;

        $classes = $this->buildClassString('', $options, true);

        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        return "\t<li {$classes} {$id} {$attributes}><a href='{$url}'>{$title}</a></li>";
    }

    //--------------------------------------------------------------------

    /**
     * Builds the shell of a Dropdown button for use within a nav area.
     *
     * @param          $title
     * @param array    $options
     * @param callable $c
     */
    public function navDropdown($title,$options=[], \Closure $c)
    {
        $classes = $this->buildClassString('has-dropdown', $options, true);

        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        $output = "\t<li {$classes} {$id} {$attributes}>
        <a href='#'>{$title}</a>
        <ul class='dropdown'>";

        $output .= $this->runClosure($c);

        $output .= "\t</ul></li>";

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Creates a divider for use within a nav list.
     *
     * @return string
     */
    public function navDivider()
    {
        return '<li class="divider"></li>';
    }

    //--------------------------------------------------------------------

    public function sideNav($options=[], \Closure $c)
    {
        $classes = $this->buildClassString('side-nav', $options, true);

        $id = $this->buildIdFromOptions($options);

        $attributes = $this->buildAttributesFromOptions($options);

        $output = "<ul {$classes} {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</ul>\n";

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Creates a list of nav items to function as breadcrumbs for a site.
     *
     * @param array    $options
     * @param callable $c
     * @return mixed
     */
    public function breadcrumb($options=[], \Closure $c)
    {
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'breadcrumbs', true);

        $output = "<ul {$classes} {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</ul>\n";

        return $output;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Tables
    //--------------------------------------------------------------------

    public function table()
    {
        return 'table';
    }

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
    public function button($title, $style='default', $size='default', $options=[])
    {
        $tag= "<button type='button' {classes} {id} {attributes}>{$title}</button>";

        return $this->renderButtonElement($title, $style, $size, $options, $tag);
    }

    //--------------------------------------------------------------------

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
    public function buttonLink($title, $url='#', $style='default', $size='default', $options=[])
    {
        $class = isset($options['class']) ? $options['class'] .' button' : 'button';
        $options['class'] = $class;

        $tag = "<a {classes} {id} {attributes} role='button'>{$title}</a>";

        return $this->renderButtonElement($title, $style, $size, $options, $tag);
    }

    //--------------------------------------------------------------------

    /**
     * Helper method to render out our buttons in a DRY manner.
     *
     * @param $title
     * @param $style
     * @param $size
     * @param $tag
     */
    protected function renderButtonElement($title, $style, $size, $options, $tag)
    {
        $valid_styles = ['default', 'primary', 'success', 'info', 'warning', 'danger'];
        $valid_sizes  = ['default', 'small', 'xsmall', 'large'];

        if (! in_array($style, $valid_styles))
        {
            $style = 'default';
            $options['attributes'][] = 'data-error="Invalid Style passed to button method."';
        }

        $classes = 'btn ';

        // Sizes
        switch($size)
        {
            case 'small':
                $classes .= 'small ';
                break;
            case 'xsmall':
                $classes .= 'tiny ';
                break;
            case 'large':
                $classes .= 'large ';
                break;
            default:
                break;
        }

        // Styles
        switch ($style)
        {
            case 'primary':
                $classes .= '';
                break;
            case 'success':
                $classes .= 'success ';
                break;
            case 'info':
                $classes .= 'secondary ';
                break;
            case 'warning':
                $classes .= 'alert ';
                break;
            case 'danger':
                $classes .= 'alert ';
                break;
            case 'default':
                $classes .= 'secondary ';
                break;
        }

        list($classes, $id, $attributes) = $this->parseStandardOptions($options, $classes, true);

        $tag = str_replace('{classes}', $classes, $tag);
        $tag = str_replace('{id}', $id, $tag);
        $tag = str_replace('{attributes}', $attributes, $tag);
        $tag = str_replace('{title}', $title, $tag);

        // If we're in a button group we need to wrap each item in li tags.
        if (isset($this->states['inButtonGroup']))
        {
            $tag = '<li>'. $tag .'</li>';
        }
        return $tag;
    }

    //--------------------------------------------------------------------

    /**
     * Creates button groups wrapping HTML.
     *
     * @param          $options
     * @param callable $c
     * @return mixed
     */
    public function buttonGroup($options, \Closure $c)
    {
        $this->states['inButtonGroup'] = true;

        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'button-group', true);

        $output = "<ul {$classes} {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</ul>\n";

        unset($this->states['inButtonGroup']);

        return $output;
    }

    //--------------------------------------------------------------------

    /**
     * Creates the button bar wrapping HTML.
     *
     * @param          $options
     * @param callable $c
     * @return mixed
     */
    public function buttonBar($options, \Closure $c)
    {
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'button-bar', true);

        $output = "<div {$classes} {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</div>\n";

        return $output;
    }

    //--------------------------------------------------------------------

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
    public function buttonDropdown($title, $style='default', $size='default', $options=[], \Closure $c)
    {
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'button split', true);

        $output = "<a href='#' {$classes} {$id} {$attributes}>{$title} <span data-dropdown='drop'></span></a><br>\n
                  <ul id='drop' class='f-dropdown' data-dropdown-content>\n";

        $output .= $this->runClosure($c);

        $output .= "</ul>\n";

        return $output;
    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // Notices
    //--------------------------------------------------------------------

    /**
     * Creates an 'alert-box' style of notice grid.
     *
     * $style can be 'default', 'success', 'info', 'warning', 'danger'
     *
     * @param $content
     * @param string $style
     * @param bool $closable
     * @return mixed
     */
    public function notice($content, $style='success', $closable=true, $options=[])
    {
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'alert-box ', false);

        // Styles
        switch ($style)
        {
            case 'success':
                $classes .= ' success ';
                break;
            case 'info':
                $classes .= ' secondary ';
                break;
            case 'warning':
                $classes .= ' alert ';
                break;
            case 'danger':
                $classes .= ' alert ';
                break;
            case 'default':
                $classes .= ' secondary ';
                break;
        }

        $output = "<div data-alert class='{$classes}'>\n";

        $output .= "\t$content\n";

        if ($closable)
        {
            $output .= "\t<a href='#' class='close'>&times;</a>\n";
        }

        $output .= "</div>\n";

        return $output;
    }

    //--------------------------------------------------------------------

}