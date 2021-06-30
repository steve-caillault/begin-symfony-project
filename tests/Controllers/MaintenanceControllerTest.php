<?php

/**
 * Test pour vérifier les appels aux contrôleurs lorsque la maintenance est active
 */

namespace App\Tests\Controllers;

use App\Tests\BaseTestCase;
use App\Tests\WithMaintenanceTrait;

final class MaintenanceControllerTest extends BaseTestCase {
    
    use WithMaintenanceTrait;

    /**
     * Setup
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->disableMaintenance();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        $this->disableMaintenance();
        parent::tearDown();
    }

    /**
     * Appel d'un contrôleur lorsque la maintenance est active
     * @return void
     */
    public function testEnabled() : void
    {
        $this->enableMaintenance();
        $response = $this->getHttpClient()->request('GET', '/testing');

        $message = $response->filter('p')->first()->text();
        $expectedMessage = 'Le site est actuellement en maintenance. Vous pourrez rafraîchir cette page dans quelques minutes.';

        $this->assertResponseStatusCodeSame(503);
        $this->assertPageTitleSame('Maintenance');
        $this->assertSelectorTextContains('h1', 'Maintenance');
        $this->assertEquals($expectedMessage, $message);
    }

    /**
     * Appel d'un contrôleur lorsque la maintenance est désactivée
     * @return void
     */
    public function testDisabled() : void
    {
        $this->getHttpClient()->request('GET', '/testing');

        $client = $this->getHttpClient();
        $responseContent = $client->getResponse()->getContent();
       
        $this->assertResponseStatusCodeSame(200);
        $this->assertStringNotContainsStringIgnoringCase('Maintenance', $responseContent);
    }

    /**
     * Appel Ajax lorsque la maintenance est active
     * @return void
     */
    public function testEnabledWithAjaxCalling() : void
    {
        $this->enableMaintenance();

        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/ajax');
       
        $responseContent = $client->getResponse()->getContent();
        $expectedContent = json_encode([
            'status' => 'ERROR',
            'data' => [
                'maintenance' => true,
            ],
        ]);

        $this->assertResponseStatusCodeSame(503);
        $this->assertEquals($expectedContent, $responseContent);
    }

    /**
     * Appel Ajax lorsque la maintenance n'est pas activée
     * @return void
     */
    public function testDisabledWithAjaxCalling() : void
    {
        $client = $this->getHttpClient();
        $client->xmlHttpRequest('GET', '/testing/ajax');
       
        $responseContent = $client->getResponse()->getContent();
        $expectedContent = json_encode([
            'status' => 'SUCCESS',
            'data' => [
                'success' => true,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($expectedContent, $responseContent);
    }

    /**
     * Appel au panneau d'administration lorsque la maintenance est activée
     * @return void
     */
    public function testAdminEnabled() : void
    {
        $this->enableMaintenance();
        $this->getHttpClient()->xmlHttpRequest('GET', '/testing/admin');

        $client = $this->getHttpClient();
        $responseContent = $client->getResponse()->getContent();

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringNotContainsStringIgnoringCase('Maintenance', $responseContent);
    }

    /**
     * Appel Ajax au panneau d'administration lorsque la maintenance n'est pas activée
     * @return void
     */
    public function testAdminAjaxDisabled() : void
    {
        $this->getHttpClient()->xmlHttpRequest('GET', '/testing/admin/ajax');

        $client = $this->getHttpClient();
        $responseContent = $client->getResponse()->getContent();

        $expectedContent = json_encode([
            'status' => 'SUCCESS',
            'data' => [
                'admin' => true,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($expectedContent, $responseContent);
    }

}