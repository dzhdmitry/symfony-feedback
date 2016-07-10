<?php

namespace Tests\AppBundle\Controller;

use Goutte\Client;

class SecurityControllerTest extends BaseControllerTest
{
    public function testNotAuthorized()
    {
        $client = new Client();
        $crawler = $this->createCrawler($client);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertEquals(1, $crawler->filter('a[href="/login"]')->count());
        $client->followRedirects(false);

        $this->checkRedirectToLogin($client, '/admin');
        $this->checkRedirectToLogin($client, '/admin/approved');
        $this->checkRedirectToLogin($client, '/admin/disapproved');
    }

    public function testLoginLogout()
    {
        $client = new Client();
        $crawler = $this->createCrawler($client, 'GET', '/login');
        $form = $crawler->filter('form[action="/login_check"]')->form();

        $crawler = $client->submit($form, [
            '_username' => "admin",
            '_password' => "1234",
        ]);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertContains("Invalid credentials", $crawler->filter('.alert.alert-warning')->text());

        $crawler = $client->submit($form, [
            '_username' => "admin",
            '_password' => "123",
        ]);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertContains("Logged in as admin", $crawler->filter('p.navbar-text')->text());

        $crawler = $client->request('GET', '/logout');

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertEquals(1, $crawler->filter('a[href="/login"]')->count());
    }

    protected function checkRedirectToLogin(Client $client, $url)
    {
        $client->request('GET', $url);
        $this->assertEquals(302, $client->getInternalResponse()->getStatus());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertEquals(1, $crawler->filter('form[action="/login_check"]')->count());
    }
}
