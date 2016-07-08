<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends BaseController
{
    /**
     * @Template
     * @Route("/admin", name="admin")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $sort = self::getSort($request);
        $order = self::getOrder($request);
        $messages = $this->getDoctrine()->getRepository(Message::class)->findMessages($sort, $order, false);

        return [
            'messages' => $messages,
            'p' => [
                'sort' => $sort,
                'direction' => $order
            ]
        ];
    }
}
