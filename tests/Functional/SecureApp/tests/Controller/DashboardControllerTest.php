<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Functional\SecureApp\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DashboardControllerTest extends WebTestCase
{
    public function testWelcomePageAsAnonymousUser()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testWelcomePageAsLoggedUser()
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/admin', [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => '1234']);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome to EasyAdmin 5');
    }
}
