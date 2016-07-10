<?php

namespace tests\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
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
}
