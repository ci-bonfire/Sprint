<?php

namespace Myth\UIKits;

/**
 * Class Bootstrap3UIKit
 *
 * Provides a UIKit designed to work with the Bootstrap 3.2 CSS Framework.
 */
class Bootstrap extends BaseUIKit {

    protected $name = 'Bootstrap3UIKit';

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
                    $classes .= ' col-xs-'. $value;
                    break;
                case 'm':
                    $classes .= ' col-sm-'. $value;
                    break;
                case 'l':
                    $classes .= ' col-md-'. $value;
                    break;
                case 'xl':
                    $classes .= ' col-lg-'. $value;
                    break;
                case 's-offset':
                    $classes .= ' col-xs-offset-'. $value;
                    break;
                case 'm-offset':
                    $classes .= ' col-sm-offset-'. $value;
                    break;
                case 'l-offset':
                    $classes .= ' col-md-offset-'. $value;
                    break;
                case 'xl-offset':
                    $classes .= ' col-lg-offset-'. $value;
                    break;
            }
        }

        list($classes, $id, $attributes) = $this->parseStandardOptions($options, $classes, true);

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
        $classes = "navbar navbar-default ";

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
                case 'inverse':
                    $classes .= " navbar-inverse";
            }
        }

        list($class, $id, $attributes) = $this->parseStandardOptions($options, $classes, true);

        $output .= "<nav {$class} {$id} {$attributes} role='navigation'>
  <div class='container-fluid'>";

        /*
         * Do any user content inside the bar
         */
        $output .= $this->runClosure($c);

        /*
         * Close out the navbar
         */
        $output .= '</div></nav>';

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
        return '<div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="'. $url .'">'. $title .'</a>
    </div>';
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
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'nav navbar-nav navbar-right', true);
        
        $output = "<ul {$classes} {$id} {$attributes}>\n";

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
    public function navItem($title, $url='#', $options=[], $isActive=false)
    {
        $this->states['activeNavTitle'] = $title;

        $class = $isActive ? $this->active_class : '';

        list($classes, $id, $attributes) = $this->parseStandardOptions($options, $class, true);

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
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'dropdown', true);

        $output = "\t<li {$classes} {$id} {$attributes}>
        <a href='#' class='dropdown-toggle' data-toggle='dropdown'>{$title} <span class='caret'></span></a>
        <ul class='dropdown-menu' role='menu'>";

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
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'nav nav-pills nav-stacked', true);

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
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'breadcrumb', true);

        $output = "<ol {$classes} {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</ol>\n";

        return $output;
    }

    //--------------------------------------------------------------------


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
        $tag = "<a href='{$url}' {classes} {id} {attributes} role='button'>{$title}</a>";

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
                $classes .= 'btn-sm ';
                break;
            case 'xsmall':
                $classes .= 'btn-xs ';
                break;
            case 'large':
                $classes .= 'btn-lg ';
                break;
            default:
                break;
        }

        // Styles
        switch ($style)
        {
            case 'primary':
                $classes .= 'btn-primary ';
                break;
            case 'success':
                $classes .= 'btn-success ';
                break;
            case 'info':
                $classes .= 'btn-info ';
                break;
            case 'warning':
                $classes .= 'btn-warning ';
                break;
            case 'danger':
                $classes .= 'btn-danger ';
                break;
            case 'default':
                $classes .= 'btn-default ';
                break;
        }

        list($classes, $id, $attributes) = $this->parseStandardOptions($options, $classes, true);

        $tag = str_replace('{classes}', $classes, $tag);
        $tag = str_replace('{id}', $id, $tag);
        $tag = str_replace('{attributes}', $attributes, $tag);
        $tag = str_replace('{title}', $title, $tag);

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
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'btn-group', true);

        $output = "<div {$classes} {$id} {$attributes}>\n";

        $output .= $this->runClosure($c);

        $output .= "</div>\n";

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
        $options['attributes'][] = 'role="toolbar"';

        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'btn-toolbar', true);

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
        $tag = "<button type='button' {classes} data-toggle='dropdown'>
    {title} <span class='caret'></span>
  </button>
  <ul class='dropdown-menu' role='menu'>";

        $output = $this->renderButtonElement($title, $style, $size, $options, $tag);

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
        list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'alert', false);

        // Styles
        switch ($style)
        {
            case 'success':
                $classes .= ' alert-success ';
                break;
            case 'info':
                $classes .= ' alert-info ';
                break;
            case 'warning':
                $classes .= ' alert-warning ';
                break;
            case 'danger':
                $classes .= ' alert-danger ';
                break;
            case 'default':
                $classes .= ' text-muted ';
                break;
        }

        $output = "<div class='{$classes}'>\n";

        $output .= "\t$content\n";

        if ($closable)
        {
            $output .= "\t<a href='#' class='close'>&times;</a>\n";
        }

        $output .= "</div>\n";

        return $output;
    }

    //--------------------------------------------------------------------

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
	public function inputWrap($label_text, $options=[], \Closure $c)
	{
		list($classes, $id, $attributes) = $this->parseStandardOptions($options, 'form-group', true);

		$output = "<div {$classes} {$id} {$attributes}>
		<label for=''>{$label_text}</label>\n";

		$output .= $this->runClosure($c);

		$output .= "\t\t</div>\n";

		return $output;
	}

	//--------------------------------------------------------------------

}