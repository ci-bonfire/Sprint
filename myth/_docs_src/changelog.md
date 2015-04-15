# Change Log

## Under Development

### 1.0
Release Date TBD

#### New Features

* Application Views can override module-based views
* Theme views can override application views
* No-cache headers are now sent whenever a user is logged in to protect back-button discovery

#### Closes Issues
* #88 - Added improved documentation and consistency for CIDbModel observers
* #82 - CIMailService now sets correct email format for HTML emails
* #81 - Revised session handling in LocalAuthentication for better security and ability to use flash messages
* #73 - Moved session library to the BaseController so it's always avialable to detect login status

#### Additional Changes
* Upgraded to latest CI 3 versions
* Scaffolding now works with or without an existing db table
* Changed default session cookie settings for better security


## Released Versions

### 1.0-beta1
March 24, 2015