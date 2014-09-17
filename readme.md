# Sprint PHP

Develop Branch: [![Build Status](https://travis-ci.org/ci-bonfire/Sprint.svg?branch=develop)](https://travis-ci.org/ci-bonfire/Sprint)

Based on the CodeIgniter 3 PHP framework, Sprint provides the essential running start to get you to the fun part of building your web applications. It provides additional utilities and workflow enhancements to stock CodeIgniter. In the process, it helps to modernize the code a bit. 

Sprint is intended to be the heart of [Bonfire Next](https://github.com/ci-bonfire/Bonfire-Next), though that integration has not happened yet. 

## Why Sprint?
I found that for a number of my recent projects, the current Bonfire code was too much, too opinionated and fully developed. And the clients were requesting for me to use the Foundation CSS Framework since that's what they use in house, and Bonfire was built on Bootstrap. Besides, sometimes Bonfire is just too big of an application for your projects. 

While working on Bonfire Next and an in-progress book on modernizing CodeIgniter and it's practical usage,  I realized that I could reform the current slimmer codebase that I've been using and make it the core of Bonfire Next. The goal is to have basic functionality and workflow in place in Sprint, and to build on that in Bonfire. 

Where Sprint has a very simple, view-based approach to themes, Bonfire will add the option of the current robust templating solution. Where Sprint will have a super simple role-based auth system that is intended to hard code into the controllers, Bonfire will continue to use a version of the current RBAC with fully assignable roles/permissions. Bonfire will also ship with more modules for extra functionality, along with the admin area already up and running, where Sprint requires you to build out your own. 

So Sprint is basically CodeIgniter, but nicer.

NOTE: THIS IS UNDER HEAVY DEVELOPMENT AT THE MOMENT AND IS NOT INTENDED FOR PUBLIC CONSUMPTION AT THE MOMENT. (That said - it's got some great stuff coming, so keep an eye on it, or take the parts you like at the moment.)

## What's In The Box?

The following is being built for the initial release:

* (Done) Powerful MY_Model with standard CRUD, db wrappers, observer methods and in-model validation
* (Done) MY_Controller with simple theming, rendering methods for other data types (like json) and more
* (Done) Extended Router to include module support, named routes, HTTP verb-based routing, Restful resources and scoped routes/areas.
* (Done, needs more testing) Simple, but flexible, Template system
* Simple Auth system 
* Module Support, without being able to call other controllers. That simply gets too complex and causes too many problems. Instead, it's simply the ability to keep MVC triads in modules that can still be called from the URI.
* (Partially Done) Ready-to-roll AJAX system using [Eldarion AJAX](https://github.com/eldarion/eldarion-ajax)
* Better Database Migrations, with CLI tool for building and running
* Database Seeding with CLI tool
* Code Builder with simple generators in place, but fully customizable and easy to add your own.
* Simple, GUI-less cron controller that can be used either through standard crontab, or from the web (for use with third-party cron runners)
* Markdown-based documentation system.

## What's NOT included?

Sprint will not include much in the way of a built-in admin area, though it will have default views that can be incorporated into your own areas.

It will not include a method for working with assets as much of this can be handled easier and with higher performance on a per-project basis, often using something like [CodeKit](http://incident57.com/codekit/).