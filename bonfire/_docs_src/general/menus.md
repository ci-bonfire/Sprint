# Menus
Menus are created with a three pretty simple classes: Bonfire\Navigation\MenuCollection, Bonfire\Navigation\Menu and Bonfire\Navigation\MenuItem. The classes only provide the collection of links and some utilities to work with them. 

## Usage

For the examples here, we'll use the Admin menu that is shown in the Admin area's sidebar.

### Create A Menu
To create a new menu, you simply grab an instance of that menu from the MenuCollection class. The only parameter is the name you want to reference the menu by. If the menu hasn't been created, the collection takes care of that for you. 
    
    $menu = new \Bonfire\Navigation\MenuCollection::menu('admin');



You should always grab your menus from the collection so that all files can share the same menus, unless that menu is only used by a single class, then you can create an instance of Menu yourself. Again, the only parameter is the name you want to reference the menu by.

    $menu = new \Bonfire\Navigation\Menu('admin');

### Adding Menu Items
Once you have an instance of a menu ready for use, you will start adding menu items using the `addItem()` method. The only parameter is an instance of a MenuItem. 

    $menu->addItem( $menuItem );

The simplest way to work with this in most cases is to create the new item at the same time you pass it in, but you have to pass several pieces of information in when creating a new MenuItem. The first (and only required) parameter is name to reference the item by. The second parameter is the title displayed in the link. The third parameter is the URL for the link itself. The fourth parameter is a placeholder for icon name. For the admin area, this would be the FontAwesome name. The final parameter is the order, or weight, of the link. That is described in more depth in the [MenuItem Reference](general/menu_item).

    $menu->addItem( new MenuItem('tools', 'Tools', '#', 'fa-wrench', 90) );
    
If you want to ensure that a parent menu item exists, you can use the `ensureItem()` method to create a new menuItem in it's place if one doesn't exist already. If one does exist, it is left untouched. 

    $menu->ensureItem( new MenuItem('tools', 'Tools', '#', 'fa-wrench', 90 ) );

### Creating Child Links
You can add child links to any other links, creating as many levels of nesting as you might need. It is recommended to keep the depth as shallow as possible, though, for usability.

This is done with the `addChild()` method, which works just like the addItem link, except it has an additional second parameter: the alias of the parent link. 

    $newItem = new MenuItem( ... );
    $menu->addChild( $newItem, 'tools');
    

