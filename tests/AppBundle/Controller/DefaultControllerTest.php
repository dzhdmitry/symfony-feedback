<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Message;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class DefaultControllerTest extends BaseControllerTest
{
    public function testDateDesc()
    {
        // Sorted by default (date DESC)
        $crawler = $this->createCrawler(new Client());

        $this->checkMessagesOrder($crawler, ['Fourth', 'Third', 'Second', 'First']);
    }

    public function testDateAsc()
    {
        // Sorted by date ASC
        $crawler = $this->createCrawler(new Client(), 'GET', "?sort=createdAt&direction=asc");

        $this->checkMessagesOrder($crawler, ['First', 'Second', 'Third', 'Fourth']);
    }

    public function testEmailDesc()
    {
        // Sorted By email DESC
        $crawler = $this->createCrawler(new Client(), 'GET', "?sort=email&direction=desc");

        $this->checkMessagesOrder($crawler, ['Fourth', 'Second', 'Third', 'First']);
    }

    public function testEmailAsc()
    {
        // Sorted By email ASC
        $crawler = $this->createCrawler(new Client(), 'GET', "?sort=email&direction=asc");

        $this->checkMessagesOrder($crawler, ['First', 'Third', 'Second', 'Fourth']);
    }

    public function testAuthorDesc()
    {
        // Sorted By author DESC
        $crawler = $this->createCrawler(new Client(), 'GET', "?sort=author&direction=desc");

        $this->checkMessagesOrder($crawler, ['First', 'Third', 'Fourth', 'Second']);
    }

    public function testAuthorAsc()
    {
        // Sorted By author ASC
        $crawler = $this->createCrawler(new Client(), 'GET', "?sort=author&direction=asc");

        $this->checkMessagesOrder($crawler, ['Second', 'Fourth', 'Third', 'First']);
    }

    public function testChangedNotice()
    {
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
        $this->loadMessages(true);
    }

    protected function checkMessagesOrder(Crawler $crawler, $data)
    {
        $messages = $crawler->filter('div.messages-container')->children();

        $messages->each(function(Crawler $message, $i) use ($data) {
            $this->assertContains($data[$i], $message->filter('div.panel-heading')->text());
        });
    }
}
