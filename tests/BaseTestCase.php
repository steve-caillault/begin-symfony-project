<?php

/**
 * Test de base
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseTestCase extends KernelTestCase {

    /**
     * Retourne le service correspondant à la classe en paramètre
     * @param string $class
     * @return mixed
     */
    protected function getService(string $class)
    {
        return static::getContainer()->get($class);
    }

    /**
     * Setup
     * @return void
     */
    protected function setUp() : void
    {
        $kernel = static::bootKernel();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

}