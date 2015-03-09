# Welcome to Sprint
Sprint provides you with a jump-start to your web-application development by providing you the framework, tools, and methodology you need to build a great product for your company or client.

This documentation is included with every download of Sprint. Please navigate within your site to `/docs` to view them. To learn about configuring docs or writing your own documentation, see our page on [Writing Documentation](writing_docs).

## How Do I Install It?
Installation is simple, but does require that [Composer](https://getcomposer.org/) is installed. You can find installation instructions for that over in [their docs](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx). I highly recomend installing it globallly. You'll use it a lot once you get used to it.

Once that's installed, just follow the simple steps in our [installation guide](installation).

## Server Requirements
Sprint requires the following server setup, at minimum:

* PHP version 5.4 or newer.
* SimpleXML enabled on your PHP installation for the docs to work.
* [Composer](http://getcomposer.org) installed on development server.
* A Database. We currently use PDO/MySQL but try to keep it fairly flexible.

## Sprint License Agreement
Multiple licenses apply to this application.

* All **Sprint-specific code** is licensed under the [MIT license](http://opensource.org/licenses/MIT), which means you can do pretty much anything you want to with it, though hopefully ethics will still win the day here.
* All **CodeIgniter-specific code** (anything within the `system` folder) is licensed by the British Columbia Institute of Technology under the [MIT license](http://opensource.org/licenses/MIT).

## Credits
Obviously, [CodeIgniter](http://codeigniter.com) and [EllisLab](http://www.ellislab.com/) are the heroes that brought this engine to the web so many years ago and inspired us with it's simplicity and elegance. True, it's got it's rough edges and might be aging a bit, but it works great and is solid code.

[Lonnie Ezell](http://lonnieezell.com) wrote the initial version of [Bonfire](http://ci-bonfire.com) based on code and patterns on three fairly large projects he had done. Since they all shared fairly standard bits of code, he combined them into a nice package, wrote some docs, and released it to the world, figuring that someone might be able to get some use out of it. It became much more popular than he expected and landed him several new clients whom he has built a fairly lasting relationship with.

Since then, he's found he often needs a much simpler architecture and that the complexity in the Bonfire was a bit of a burden at times. For those times, he ripped out the core of the app and streamlined and simplified everything. For Sprint, he kept the philosophy of his stripped down versions, but enhanced the power of a few pieces a bit and made it so that it could then work as the base of the next major version of Bonfire, a complete re-architecture.

Sprint also uses the following software packages:

* [Bootstrap CSS Framework](http://getbootstrap.com/)
* [Foundation CSS Framework](http://foundation.zurb.com/)
* [jQuery Javascript Framework](http://jquery.com/)
* [MobileDetect](http://mobiledetect.net/)
* [PHP Error](http://phperror.net/)
* [Ink: Responsive Email Framework](http://zurb.com/ink/)
* [Kint debugging tool](http://raveren.github.io/kint/)

For development and testing, Sprint uses:

* [Composer](https://getcomposer.org/)
* [Codeception 2](http://codeception.com/)
* [Mockery](https://github.com/padraic/mockery)
