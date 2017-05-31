<?php

namespace tests\AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;

abstract class BaseUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $_application;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->_application->getKernel()->getContainer();
    }

    /**
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->getContainer()->get($id);
    }

    protected function setUp()
    {
        $kernel = new \AppKernel("test", true);

        $kernel->boot();

        $this->_application = new Application($kernel);

        $this->_application->setAutoExit(false);
    }
}
