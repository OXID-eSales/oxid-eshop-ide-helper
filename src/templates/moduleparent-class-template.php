
<?php if (!empty($class['namespace'])) {?>
    namespace <?=$class['namespace']?> {
    <?php if ($class['isInterface']): ?>interface <?php elseif ($class['isAbstract']): ?>abstract class <?php else: ?>class <?php endif; ?><?= $class['childClassName'] ?> extends \<?=$class['parentClassName']?>

    {

    }
<?php } ?>