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
        $crawler = $this->createMessage($client, [
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
        $crawler = $this->createMessage($client, [
            'message_create[author]' => "",
            'message_create[email]' => "invalid",
            'message_create[body]' => ""
        ]);

        $this->assertContains("Creating message", $crawler->filter('h1')->text());
        $this->assertEquals(3, $crawler->filter('.has-error')->count());
    }

    public function testChanged()
    {
        $client = $this->logIn();

        $this->createMessage($client, [
            'message_create[author]' => "not edited",
            'message_create[email]' => "test@test.com",
            'message_create[body]' => "Text"
        ]);

        $crawler = $client->request('GET', '/messages/1');
        $editForm = $crawler->filter('form[name="message_edit"]')->form();

        $editForm['message_edit[approved]']->tick();

        $client->submit($editForm, [
            'message_edit[body]' => "Text edited"
        ]);

        $crawler = $client->request('GET', "/");

        $this->assertContains("Text edited", $crawler->filter(".panel-body")->filter('p')->text());
        $this->assertEquals(1, $crawler->filter(".label.label-info")->count());
    }

    public function testNotChanged()
    {
        $client = $this->logIn();

        $this->createMessage($client, [
            'message_create[author]' => "not edited",
            'message_create[email]' => "test@test.com",
            'message_create[body]' => "Text"
        ]);

        $crawler = $client->request('GET', '/messages/1');
        $editForm = $crawler->filter('form[name="message_edit"]')->form();

        $editForm['message_edit[approved]']->tick();

        $client->submit($editForm, []);

        $crawler = $client->request('GET', "/");

        $this->assertEquals(0, $crawler->filter(".label.label-info")->count());
    }

    public function testPictureSmall()
    {
        $message = $this->createMessageWithPicture("319x239.jpg");

        $this->assertPictureSize($message, 319, 239);
    }

    public function testPictureFit()
    {
        $message = $this->createMessageWithPicture("320x240.jpg");

        $this->assertPictureSize($message, 320, 240);
    }

    public function testPictureTall()
    {
        $message = $this->createMessageWithPicture("320x480.jpg");

        $this->assertPictureSize($message, 160, 240);
    }

    public function testPictureWide()
    {
        $message = $this->createMessageWithPicture("640x240.jpg");

        $this->assertPictureSize($message, 320, 120);
    }

    public function testPictureBigFit()
    {
        $message = $this->createMessageWithPicture("640x480.jpg");

        $this->assertPictureSize($message, 320, 240);
    }

    public function testPictureBigTall()
    {
        $message = $this->createMessageWithPicture("600x720.jpg");

        $this->assertPictureSize($message, 200, 240);
    }

    public function testPictureBigWide()
    {
        $message = $this->createMessageWithPicture("960x600.jpg");

        $this->assertPictureSize($message, 320, 200);
    }

    public function testApprove()
    {
        $this->loadMessages(false);

        $client = $this->logIn();
        $crawler = $client->request('GET', '/admin');
        $form = $crawler->filter('form[action="/messages/2/approve"]')->form();
        $crawler = $client->submit($form);
        $table = $crawler->filter('.messages-table');

        $this->assertEquals(1, $table->filter('.label.label-primary')->count());
        $this->assertEquals(3, $table->filter('.label.label-warning')->count());
    }

    public function testDisapprove()
    {
        $this->loadMessages(true);

        $client = $this->logIn();
        $crawler = $client->request('GET', '/admin');
        $form = $crawler->filter('form[action="/messages/3/disapprove"]')->form();
        $crawler = $client->submit($form);
        $table = $crawler->filter('.messages-table');

        $this->assertEquals(3, $table->filter('.label.label-primary')->count());
        $this->assertEquals(1, $table->filter('.label.label-warning')->count());
    }

    /**
     * @param string $filename
     * @return Message
     */
    protected function createMessageWithPicture($filename)
    {
        $client = new Client();
        $this->createMessage($client, [
            'message_create[author]' => "img",
            'message_create[email]' => "img@test.com",
            'message_create[body]' => "Img"
        ], $filename);

        return $this->getEntityManager()->getRepository(Message::class)->find(1);
    }

    /**
     * @param Client $client
     * @param array $data
     * @param string|null $picture
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function createMessage(Client $client, $data = [], $picture = null)
    {
        $crawler = $this->createCrawler($client);
        $form = $crawler->filter('form[name="message_create"]')->form();

        if ($picture) {
            /** @var $fileField \Symfony\Component\DomCrawler\Field\FileFormField */
            $fileField = $form->get('message_create[picture][originalFilename]');
            $filePath = realpath('tests/fixtures/'.$picture);

            $fileField->upload($filePath);
        }

        $crawler = $client->submit($form, $data);

        $this->assertEquals(200, $client->getInternalResponse()->getStatus());

        return $crawler;
    }

    /**
     * Runs before each $this::test* function
     */
    protected function setUp()
    {
        $this->runCommand("doctrine:fixtures:load");
    }
}
