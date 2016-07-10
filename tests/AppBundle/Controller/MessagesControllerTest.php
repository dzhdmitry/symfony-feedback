<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Message;
use Goutte\Client;

class MessagesControllerTest extends BaseControllerTest
{
    public function testIndex()
    {
        $client = new Client();
        $crawler = $this->createCrawler($client);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertContains('No messages yet', $crawler->filter('.alert.alert-info')->text());
    }

    public function testCreate()
    {
        $client = new Client();
        $crawler = $this->createCrawler($client);
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

    public function testValidation()
    {
        $client = new Client();
        $crawler = $this->createCrawler($client);
        $form = $crawler->filter('form[name="message_create"]')->form();
        $crawler = $client->submit($form, [
            'message_create[author]' => "",
            'message_create[email]' => "invalid",
            'message_create[body]' => ""
        ]);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertContains("Creating message", $crawler->filter('h1')->text());
        $this->assertEquals(3, $crawler->filter('.has-error')->count());
    }

    /**
     * Runs before each $this::test* function
     */
    protected function setUp()
    {
        $this->runCommand("doctrine:fixtures:load");
    }
}
