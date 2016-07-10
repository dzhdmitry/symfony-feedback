<?php

namespace tests\AppBundle\Controller;

use AppBundle\Entity\Message;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class BaseControllerTest extends WebTestCase
{
    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get("doctrine.orm.entity_manager");
    }

    /**
     * @param Client $client
     * @param string $method
     * @param string $uri
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function createCrawler($client, $method = 'GET', $uri = '')
    {
        $base = $this->getContainer()->getParameter("test_base_url");

        return $client->request($method, $base.$uri);
    }

    protected function getMessagesData()
    {
        return [
            [
                'author' => "D First",
                'email' => "a_first@gmail.com",
                'body' => "First message body"
            ], [
                'author' => "A Second",
                'email' => "c_second@gmail.com",
                'body' => "Second message body"
            ], [
                'author' => "C Third",
                'email' => "b_third@gmail.com",
                'body' => "Third message body"
            ], [
                'author' => "B Fourth",
                'email' => "d_fourth@gmail.com",
                'body' => "Fourth message body"
            ]
        ];
    }

    protected function loadMessages($approved = false)
    {
        $em = $this->getEntityManager();

        foreach ($this->getMessagesData() as $data) {
            $message = new Message();

            $message->setAuthor($data["author"]);
            $message->setEmail($data["email"]);
            $message->setBody($data["body"]);
            $message->setApproved($approved);

            // Save each message to guarantee createdAt differences
            $em->persist($message);
            $em->flush();

            usleep(1000000);
        }
    }

    protected function setMessagesApproved($approved)
    {
        $em = $this->getEntityManager();
        $messages = $em->getRepository(Message::class)->findAll();

        foreach ($messages as $message) {
            $message->setApproved($approved);
            $em->persist($message);
        }

        $em->flush();
    }

    /**
     * @return Client
     */
    protected function logIn()
    {
        $client = new Client();
        $crawler = $this->createCrawler($client, 'GET', "/login");
        $form = $crawler->filter('form[action="/login_check"]')->form();

        $client->submit($form, [
            '_username' => "admin",
            '_password' => "123",
        ]);

        return $client;
    }
}
