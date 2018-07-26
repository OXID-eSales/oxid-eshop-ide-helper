IDE code completion support for OXID eShop
==========================================

[![Build Status](https://travis-ci.org/OXID-eSales/oxid-eshop-ide-helper.svg?branch=master)](https://travis-ci.org/OXID-eSales/oxid-eshop-ide-helper)

Synapsis
--------

This component generates a helper file for your IDE, which enables you to use 
autocompletion for backwards compatible OXID eShop classes (e.g. class `oxArticle`).

The name of the generated file is `.ide-helper.php` (note the leading dot). 
It's located in the root directory of your OXID eShop project. 

Installation
------------

To install this component, run the following command in the root directory of 
your OXID eShop: 

```
composer require --dev oxid-esales/oxideshop-ide-helper
```

Usage
-----

To create or update the helper file, run the following command in the root 
directory of your OXID eShop project:  

```
vendor/bin/oe-eshop-ide_helper
```

If you upgrade an existing OXID eShop edition (>= 6.x), the IDE helper file 
should be updated afterwards. Simply run the above command after the upgrade 
process.

Bugs and Issues
---------------

If you experience any bugs or issues, please report them in the section **OXID eShop (all versions)** of https://bugs.oxid-esales.com.

Note for PhpStorm users
-----------------------

There is an excellent third-party plug-in for PhpStorm, which complements the 
functionality of this IDE helper component. See [Haenchen's IntelliJ IDEA / 
PhpStorm Plugin for OXID](https://github.com/Haehnchen/idea-php-oxid-plugin) 
for details and installation instructions.
