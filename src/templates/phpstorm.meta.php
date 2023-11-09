<?= '<?php' ?>
/**
* Used by PhpStorm to map factory methods to classes for code completion, source code analysis, etc.
*
* The code is not ever actually executed and it only needed during development when coding with PhpStorm.
*
* @see https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
*/

namespace PHPSTORM_META {
'override(\oxNew(0), type(0));';
}

// Simulating the iteration in PHP for moduleParentClasses
<?php foreach ($moduleParentClasses as $class) {
    include 'moduleparent-class-template.php';
}?>
