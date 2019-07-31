<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testGetUsersAction()
    {
        $client = static::createClient();

        $client->request('GET', '/admin/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}