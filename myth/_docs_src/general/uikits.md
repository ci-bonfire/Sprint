# UI Kits

UIKits are libraries of common UI Elements, like buttons, navbars, etc, that are used throughout websites, but implemented differently by different CSS Frameworks. This makes it simple to create view files that will display the same no matter what the CSS Framework in use is. 

When developing an application solely for yourself or your client, you will likely never need UIKits. However, if you are developing modules for re-use or for sharing with other developers, than you might want to use UIKit methods in order to create the most re-usable modules possible.

Sprint ships with UIKits for Bootstrap 3 and Foundation 5 CSS frameworks.

## Loading UIKits
The only thing that you need to do to load a UIKit is to specify the name in `application/config/application.php` config file. This must be the fully namespaced class name. The current available options are either `\Myth\UIKits\Bootstrap` or `\Myth\UIKits\Foundation`.

	$config['theme.uikit'] = '\Myth\UIKits\Bootstrap';
	
Any controllers that extend `ThemedController` will have the UIKit automatically instantiated and available inside the controller at `$this->uikit`, or within any view as `$uikit`.

## Using UIKit Methods
Many of the UIKit methods use Closures to allow custom code, HTML and even other UIKit methods inside of a tag. A great example of this is the `navbar` tag that requires lists of objects, a heading and allows an assortment of other tags inside of it. The grid system is another example of this. In all cases, you must remember to `use ($uikit)` with the closure or you will be unable to access the `$uikit` object while inside of that view. 

	// Won't work:
	$uikit->row([], function() {
		. . . 
	});
	
	// Will work: 
	$uikit->row([], function() use($uikit) {
		. . . 
	});
	
### $options
Most of the UIKit methods support an `$options` array to allow you to fully customize the output. The $options array may contain any of the following keys: 

- `class` Allows one or more, space-separated classes to be added to the object. 
- `id` Assigns an id property to the object.
- `attributes` Supports an array of strings to be passed in. These will be turned into their appropriate attribute tags and inserted into the object as appropriate. 

```
$options = array(
	'class'	=> 'hidden',
	'id'		=> 'myButton',
	'attributes'	=> array(
		'required',
		'data-id=13'
	)
);
```

## Grid System
The grid system is based on a 12-column grid, no matter how many other columns the CSS framework might support. This was chosen becuase 12-columns are available in almost every single CSS framework that I could find and is a suitable grid to work with. 

### row()
Creates a single `row` for a grid, that will surround the columns inside of it. The first parameter is the `$options` array.  The second parameter is the closure. 

	<?= $uikit->row([], function() use($uikit) {
		. . .
	}); ?>

### column()
Creates a single column. Should be used within a `row()` call. The first parameter is the `$options` array. In addition to the standard options, you can also specify the `sizes` property, which contains an array of sizes that can be attached to the row. Valid `sizes` properties are: 

- `s`
- `m`
- `l`
- `xl`
- `s-offset`
- `m-offset`
- `l-offset`
- `xl-offset`

For each size that you specify, it's value is the number of columns. 

	$sizes = [
		's'	=> 3,
		'm' => 2,
		's-offset'	=> 9,
		'm-offset' => 10
	];
	
	<?= $uikit->row([], function() use($uikit) {
		echo $uikit->column(['sizes' => $sizes, function() use($uikit) {
			. . .
		});
	}); ?>
	
## Navigation Elements

### navbar
Creates the wrapper for a `navbar`. Note that this does not include the responsive menu button. That element is contained in the `navbarTitle` element. The first parameter is the $options array. Along with the standard options, this array may also have the one of the follow items set, to describe the type of navbar: `sticky-top`, `fixed`, `inverse`. Note that inverse is not supported in Foundation.

	<?= $uikit->navbar(['sticky-top'], function() use($uikit) {
		. . . 
	}); ?>

### navbarTitle
Creates the title on the left of the navbar, along with the responsive menu-button. The first parameter is a string with the title to display. The second is the URL the title should link to.

	<?= $uikit->navbar(['sticky-top'], function() use($uikit) {
		echo $uikit->navbarTitle('SprintPHP Demo Site', 'http://sprintphp.com/demos'); 
	}); ?>
	

### navbarRight
Creates a list of navigation items that displays on the right-hand side of the navbar. The first parameter is the $options array. The second parameter is the closure.

	<?= $uikit->navbar(['sticky-top'], function() use($uikit) {
		echo $uikit->navbarRight([], function() use($uikit){
			. . .
		});
	}); ?>

### navItem
Creates a single navigation item. To be used within `nav`, `navbarRight`, or `sideNav` elements. The first parameter is the title that is displayed on the item. The second parameter is the url it should link to. The third parameter is the $options array. The fourth parameter accepts a boolean value to determine if this element is the currently selected item in this list.

	<?= $uikit->navbar(['sticky-top'], function() use($uikit) {
		echo $uikit->navbarRight([], function() use($uikit){
			echo $uikit->navItem('Grid System', '#grids');
            echo $uikit->navItem('Offset Grids', '#offset-grids');
            echo $uikit->navItem('Tables', '#tables');
            echo $uikit->navItem('Buttons', '#buttons');
		});
	}); ?>

### navDropdown
Creates the outer wrapper for a dropdown button within a navbar. The first parameter is the title to be displayed. The second parameter is the $options array. The third parameter is the closure.

	<?= $uikit->navbar(['sticky-top'], function() use($uikit) {
		echo $uikit->navDropdown('Reports', [], function() use($uikit) {
			echo $uikit->navItem('Sales', '/reports/sales');
			echo $uikit->navItem('Visitors', '/reports/visits');
		});
	}); ?>

### navDivider
Creates a simple divider in a list of items within one of the nav elements.

	echo $uikit->navItem('Grid System', '#grids');
    echo $uikit->navDivider();
    echo $uikit->navItem('Buttons', '#buttons');	

### sideNav
Creates a vertical nav element, typically used within a sidebar. The first parameter is the $options array. The second parameter is the closure. 

	<?= $uikit->sideNav([], function() use($uikit) {
		echo $uikit->navItem('Grid System', '#grids');
        echo $uikit->navDivider();
        echo $uikit->navItem('Buttons', '#buttons');	
	}); ?>

### breadcrumb
Creates the wrapper for a breadcrumb style of navigation. The first parameter is the $options array. The second parameter is the closure. 

	<?= $uikit->breadcrumb([], function() use($uikit) {
		echo $uikit->navItem('Grid System', '#grids');
        echo $uikit->navDivider();
        echo $uikit->navItem('Buttons', '#buttons');	
	}); ?>
	
	
	
## Buttons

The UIKits provide ways of creating buttons across frameworks, without having to worry about the difference in styles needed. To help with consistent intent for the style/color of a button, the styles have been standardized into the following options: 

- `default` Typically a non-obtrusive color, like a gray.
- `primary` A bold, bright color. Both Foundation and Bootstrap default these to a bold blue.
- `success`
- `info`
- `warning`
- `danger` 

As well as the styles, the sizes have been standardized, also.

- `default`
- `small`
- `xsmall`
- `large`

### button
Creates a `<button>` element. The first parameter is the text that is displayed in the button. The second parameter is the $style. The third parameter is the $size. The fourth parameter is the $options array.

	echo $uikit->button('Save Changes', 'primary', 'default', $options);

### buttonLink
Creates an `<a>` element that is styled like a button. The first parameter is the text that is displayed in the button. The second parameter is the URL to link to. The third parameter is the $style. The fourth parameter is the $size. The fifth parameter is the $options array.

	echo $uikit->buttonLink('Complete Task', '/tasks/complete/15', 'success', 'xsmall', $options);

### buttonGroup
Creates the wrapping code for a group of buttons that are joined together into a single UI element. Much like you would see on a toolbar. The first parameter is the $options array. The second parameter is the closure. 

	<?= $uikit->buttonGroup([], function() use($uikit) {
		echo $uikit->button('Save', 'primary', 'default', $options);
		echo $uikit->button('Revert', 'warning', 'default', $options);
	}); ?>

### buttonBar
Holds multiple button groups, for when you need a more complex UI, like a toolbar. The first parameter is the $options array. The second parameter is the closure. 

	<?= $uikit->buttonBar([], function() use($uikit) {
		echo $uikit->buttonGroup([], function() use($uikit) {
			echo $uikit->button('Save', 'primary', 'default', $options);
			echo $uikit->button('Revert', 'warning', 'default', $options);
		});
		echo $uikit->buttonGroup([], function() use($uikit) {
			echo $uikit->button('Save', 'primary', 'default', $options);
			echo $uikit->button('Revert', 'warning', 'default', $options);
		});
	}); ?>

### buttonDropdown
Creates a button that also has a dropdown button. Some frameworks call these Split Buttons. The first parameter is the text to be displayed. The second parameter is the $style. The third parameter is the $size. The fourth parameter is the $options array. The fifth parameter is the closure. 

	<?= $uikit->buttonDropdown('Dropdown', 'primary', 'default', [], function() use($uikit){
		echo $uikit->navItem('Item 1');
		echo $uikit->navItem('Item 2');
		echo $uikit->navDivider();
		echo $uikit->navItem('Item 3');
	}); ?>



## Notices
Notices, or alert boxes, can be created with the `notice` command.  The classes have been standardized to the following options:

- `default`
- `success`
- `info`
- `warning`
- `danger`

The first parameter is the content to go within the notice. The second parameter is the $style. The second is a boolean value for whether the notice can be dismissed/closed. The fourth parameter is the $options array.

	<?= $uikit->notice('Item successfully updated.', 'success', true, $options); ?>

## Forms
UIKits don't provide code for every item in a form, only the most common uses. 

### inputWrap()
Creates the wrapping code around a form input element. This is intended for full-width controls. The first parameter is the text for the Label. The second parameter is the $options array. The third parameter is a closure to allow you to place your input in it. 

	<?= $uikit->inputWrap('Email Address', $options, function() use($uikit) { ?>
		<input type="text" name="email" placeholder="Email Address" />
	<?php }); ?> 


