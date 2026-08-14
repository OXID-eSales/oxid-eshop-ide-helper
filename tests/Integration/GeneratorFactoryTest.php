<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopIdeHelper\tests\Integration;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopIdeHelper\GeneratorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class GeneratorFactoryTest extends TestCase
{
    private readonly string $helperFile;

    public function setUp(): void
    {
        $this->helperFile = Path::join(
            (new ProjectRootLocator())->getProjectRoot(),
            '.phpstorm.meta.php',
            'oxid.meta.php'
        );
        (new Filesystem())->remove($this->helperFile);
    }

    public function testCreate(): void
    {
        (new GeneratorFactory())->create()->generate();

        $this->assertFileExists($this->helperFile);
    }
}
