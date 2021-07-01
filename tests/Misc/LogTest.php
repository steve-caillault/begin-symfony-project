<?php

/**
 * Tests d'enregistrement de log
 */

namespace App\Tests\Misc;

use Psr\Log\LoggerInterface;
/***/
use App\Tests\BaseTestCase;
use App\Entity\Log;

final class LogTest extends BaseTestCase {

    /**
     * Vérification de l'enregistrement
     * @return void
     */
    public function testSaving() : void
    {
        $faker = $this->getFaker();
        $client = $this->getHttpClient();
        $client->setServerParameters([
            'HTTP_USER_AGENT' => $faker->userAgent(),
        ]);
        $client->request('GET', '/testing/admin');

        $logger = $this->getService(LoggerInterface::class);
        $message = $faker->text();
        $logger->debug($message);

        $repository = $this->getRepository(Log::class);
        $lastLog = $repository->findOneBy([], orderBy: [ 'date' => 'desc' ]);

        $this->assertEquals($message, $lastLog->getMessage());
    }

}