Helper for IDE code completion  
==============================

This component generates a helper file for the currently installed edition of OXID eSales eShop.
The file is called .ide-helper.php and is generated in the project root directory of OXID eSales eShop.
It holds associations of all classes in the virtual namespace to the real classes of your eShop Edition.
Like this your IDE should be able to do proper auto-completion of classes in the virtual namespace. 

Installation
------------

To install this component, run the following command in the root directory of your OXID eSales eShop: 

```
composer require --dev oxid-esales/eshop-ide-helper
```

Usage
-----

To create or update the helper file, run the following command in the root directory of your OXID eSales eShop:  

```
vendor/bin/oe-eshop-ide_helper
```

Note for PHPStorm users
-----------------------

There is an excellent third-party plug-in for PHPStorm, which complements the functionality of this IDE helper component.
See [https://github.com/Haehnchen/idea-php-oxid-plugin](https://github.com/Haehnchen/idea-php-oxid-plugin) for details and installation instructions.
