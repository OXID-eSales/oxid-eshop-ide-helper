    /** @deprecated since v6.0.0. This class will be removed in the future. Please use the corresponding class from the unified namespace. */
    <?php if ($class['isInterface']): ?>interface <?php elseif ($class['isAbstract']): ?>abstract class <?php else: ?>class <?php endif; ?><?= $class['childClassName'] ?> extends \<?=$class['parentClassName']?>

    {

    }

