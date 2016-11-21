IDE code completion support for OXID eShop
==========================================

Synapsis
--------

This component generates a helper file for your IDE, which enables you to use 
autocompletion for OXID eShop specific classes.

The name of the generated file is `.ide-helper.php` (note the leading dot). 
It's located in the root directory of your OXID eShop project. The file maps 
the generic virtual namespace classes to their edition-specific implementations,
depending on the OXID eShop edition you use in your project.

Installation
------------

To install this component, run the following command in the root directory of 
your OXID eShop: 

```
composer require --dev oxid-esales/eshop-ide-helper
```

Usage
-----

To create or update the helper file, run the following command in the root 
directory of your OXID eShop project:  

```
vendor/bin/oxid-eshop-ide-helper
```

If you upgrade an existing OXID eShop edition (>= 6.x), the IDE helper file 
should be updated afterwards. Simply run the above command after the upgrade 
process.

Note for PhpStorm users
-----------------------

There is an excellent third-party plug-in for PhpStorm, which complements the 
functionality of this IDE helper component. See [Haenchen's IntelliJ IDEA / 
PhpStorm Plugin for OXID](https://github.com/Haehnchen/idea-php-oxid-plugin) 
for details and installation instructions.
