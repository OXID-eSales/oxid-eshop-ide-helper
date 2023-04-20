# Change Log for OXID eShop IDE helper

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [v6.1.0] - 2023-04-20

### Removed
- Dependency to webmozart/path-util

## [v6.0.0] - 2022-10-28

### Removed
- PHP v7 support

## [v5.0.0] - 2021-07-06

### Changed
- Update symfony components to version 5

## [v4.2.0] - Unreleased

### Removed
- Support for PHP < v7.4

## [v4.1.0] - 2021-04-13

### Added

* Support for PHP 8.0

## [v4.0.0] - 2020-11-04

### Changed

* Update testing library version.
* Add support for PHP 7.2, drop support for PHP 5.6.
* Support for the composer v2

### Added

* Added support for namespaced modules "clickable virtual *_parent classes" for PhpStorm IDE.
 
## [v3.1.2] - 2018-07-31

### Changed

* Added oxideshop-unified-namespace-generator dependency

## [v3.1.1] - 2018-07-31

### Changed

* Removed oxideshop-unified-namespace-generator dependency

## [v3.1.0] - 2018-02-12

### Added

* `oxNew` autocompletion in PhpStorm.

   Now IDE helper generates helper file `.phpstorm.meta.php/oxid.meta.php` for PhpStorm IDE, which adds autocompletion
   for created objects via `oxNew`.
