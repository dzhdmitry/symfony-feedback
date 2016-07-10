<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Message;
use Goutte\Client;

class DefaultControllerTest extends BaseControllerTest
{
    public function testIndex()
    {
        $container = $this->getContainer();
        $client = new Client();
        $crawler = $client->request('GET', $container->getParameter("test_base_url"));

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertContains('No messages yet', $crawler->filter('.alert.alert-info')->text());
    }

    public function testCreate()
    {
        $container = $this->getContainer();
        $client = new Client();
        $crawler = $client->request('GET', $container->getParameter("test_base_url"));
        $form = $crawler->filter('form[name="message_create"]')->form();
        $crawler = $client->submit($form, [
            'message_create[author]' => "Test",
            'message_create[email]' => "test@test.com",
            'message_create[body]' => "Text"
        ]);

        $this->assertContains("Message has been successfully created", $crawler->filter(".alert.alert-success")->text());

        $messages = $this->getEntityManager()->getRepository(Message::class)->findAll();

        $this->assertEquals(1, count($messages));
        $this->assertEquals($messages[0]->getAuthor(), "Test");
        $this->assertEquals($messages[0]->getEmail(), "test@test.com");
        $this->assertEquals($messages[0]->getBody(), "Text");
        $this->assertEquals($messages[0]->isApproved(), false);
    }

    /**
     * Runs before each $this::test* function
     */
    protected function setUp()
    {
        $this->runCommand("doctrine:fixtures:load");
    }
}
