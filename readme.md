# Sprint PHP

Based on the CodeIgniter 2.1.3 PHP framework, Sprint provides the essential running start to get you to the fun part of building your web applications. It can be thought of as [Bonfire's](http://cibonfire.com) little brother. Where Bonfire is geared toward flexibility and power, Sprint is intended to focus more on simple solutions and performance.

NOTE: THIS IS UNDER HEAVY DEVELOPMENT AT THE MOMENT AND IS NOT INTENDED FOR PUBLIC CONSUMPTION AT THE MOMENT. (That said - it's got some great stuff coming, so keep an eye on it, or take the parts you like at the moment.)

## What's In The Box?

The following is being built for the initial release:

* Powerful MY_Model with standard CRUD, db wrappers, observer methods and in-model validation
* MY_Controller with simple theming, rendering methods for other data types (like json) and more
* Extended Router to include module support, named routes, HTTP verb-based routing, Restful resources and scoped routes/areas.
* Simple Auth system similar to Laravel 4
* Module Support using a variation of [segersjens HMVC system](https://github.com/segersjens/CodeIgniter-HMVC-Modules)
* Ready-to-roll AJAX system using [Bootstrap AJAX](https://github.com/eldarion/bootstrap-ajax)

## What's NOT included?

Sprint will not include much in the way of a built-in admin area, though it will have default views that can be incorporated into your own areas.

It will not include a method for working with assets as much of this can be handled easier and with higher performance on a per-project basis, often using something like [CodeKit](http://incident57.com/codekit/).