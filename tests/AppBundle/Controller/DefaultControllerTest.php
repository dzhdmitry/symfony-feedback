<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Message;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class DefaultControllerTest extends BaseControllerTest
{
    public function testIndex()
    {
        $this->loadMessages();

        $client = new Client();
        $crawler = $this->createCrawler($client);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());
        $this->assertContains('No messages yet', $crawler->filter('.alert.alert-info')->text());

        $this->setMessagesApproved(true);

        $crawler = $client->request('GET', '');

        $this->assertEquals(4, $crawler->filter('div.messages-container')->children()->count());
    }

    public function testSorting()
    {
        $this->loadMessages(true);

        $client = new Client();
        $crawler = $this->createCrawler($client);

        // Sorted by default (date DESC)
        $this->checkMessagesOrder($crawler, ['Fourth', 'Third', 'Second', 'First']);

        // Sorted by date ASC
        $crawler = $client->request('GET', "?sort=createdAt&direction=asc");

        $this->checkMessagesOrder($crawler, ['First', 'Second', 'Third', 'Fourth']);

        // Sorted By email DESC
        $crawler = $client->request('GET', "?sort=email&direction=desc");

        $this->checkMessagesOrder($crawler, ['Fourth', 'Second', 'Third', 'First']);

        // Sorted By email ASC
        $crawler = $client->request('GET', "?sort=email&direction=asc");

        $this->checkMessagesOrder($crawler, ['First', 'Third', 'Second', 'Fourth']);

        // Sorted By author DESC
        $crawler = $client->request('GET', "?sort=author&direction=desc");

        $this->checkMessagesOrder($crawler, ['First', 'Third', 'Fourth', 'Second']);

        // Sorted By author ASC
        $crawler = $client->request('GET', "?sort=author&direction=asc");

        $this->checkMessagesOrder($crawler, ['Second', 'Fourth', 'Third', 'First']);
    }

    public function testChangedNotice()
    {
        $this->loadMessages(true);

        $em = $this->getEntityManager();
        $second = $em->getRepository(Message::class)->find(4);

        $second->setChangedByAdmin(true);
        $em->persist($second);
        $em->flush();

        $client = new Client();
        $crawler = $this->createCrawler($client);
        $messages = $crawler->filter('div.messages-container')->children();

        $messages->each(function(Crawler $message, $i) {
            $label = $message->filter('.label.label-info');

            if ($i == 0) {
                $this->assertEquals(1, $label->count());
                $this->assertContains("Changed by admin", $label->text());
            } else {
                $this->assertEquals(0, $label->count());
            }
        });
    }

    /**
     * Runs before each $this::test* function
     */
    protected function setUp()
    {
        $this->runCommand("doctrine:fixtures:load");
    }

    protected function checkMessagesOrder(Crawler $crawler, $data)
    {
        $messages = $crawler->filter('div.messages-container')->children();

        $messages->each(function(Crawler $message, $i) use ($data) {
            $this->assertContains($data[$i], $message->filter('div.panel-heading')->text());
        });
    }
}
