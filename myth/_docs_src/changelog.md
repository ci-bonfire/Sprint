# Change Log

## Under Development

### 1.0 (all changes after beta1)
Release Date TBD

#### New Features

* data sent to views with `setVar` is now automatically escaped based on context, using [Zend Framework's Escaper](http://framework.zend.com/manual/current/en/modules/zend.escaper.introduction.html).
* Application Views can override module-based views
* Theme views can override application views
* No-cache headers are now sent whenever a user is logged in to protect back-button discovery

#### Closes Issues
* #113 - Insufficient escaping in ThemedController
* #93 - Login Model Fix bad calcul date
* #91 - Primary Keys
* #89 - 404 Not Found issue with controllers placed in subdirectories within subdirectories
* #88 - Added improved documentation and consistency for CIDbModel observers
* #82 - CIMailService now sets correct email format for HTML emails
* #81 - Revised session handling in LocalAuthentication for better security and ability to use flash messages
* #73 - Moved session library to the BaseController so it's always available to detect login status

#### Additional Changes
* Upgraded to latest CI 3 versions
* Scaffolding now works with or without an existing db table
* Changed default session cookie settings for better security
* Standardized uses of CIDbModel's observers as much as possible. 
* Migrations for Settings and User_meta tables converted to use composite primary_keys for better performance


## Released Versions

### 1.0-beta1
March 24, 2015