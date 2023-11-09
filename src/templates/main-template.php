<?= '<?php' ?>

/**
 * This file is used for IDE code completion
 */

namespace {

<?php foreach ($backwardsCompatibleClasses as $class) {
    include 'backwards-compatible-class-template.php';
}?>
    exit("This file should not be included, only analyzed by your IDE");
}