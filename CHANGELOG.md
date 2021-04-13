# Change Log for OXID eShop IDE helper

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [4.1.0] - 2021-04-13

### Added

* Support for PHP 8.0

## [4.0.0] - 2020-11-04

### Changed

* Update testing library version.
* Add support for PHP 7.2, drop support for PHP 5.6.
* Support for the composer v2

### Added

* Added support for namespaced modules "clickable virtual *_parent classes" for PhpStorm IDE.
 
## [3.1.2] - 2018-07-31

### Changed

* Added oxideshop-unified-namespace-generator dependency

## [3.1.1] - 2018-07-31

### Changed

* Removed oxideshop-unified-namespace-generator dependency

## [3.1.0] - 2018-02-12

### Added

* `oxNew` autocompletion in PhpStorm.

   Now IDE helper generates helper file `.phpstorm.meta.php/oxid.meta.php` for PhpStorm IDE, which adds autocompletion
   for created objects via `oxNew`.
