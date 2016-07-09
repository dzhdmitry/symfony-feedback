<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageCreateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @Template
     * @Route("/", name="homepage")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $message = new Message();

        $form = $this->createForm(MessageCreateType::class, $message, [
            'action' => $this->generateUrl("create_message")
        ]);

        $sort = self::getSort($request);
        $order = self::getOrder($request);
        $messages = $this->getDoctrine()->getRepository(Message::class)->findMessages($sort, $order, true);

        return [
            'messages' => $messages,
            'p' => [
                'sort' => $sort,
                'direction' => $order
            ],
            'messageForm' => $form->createView()
        ];
    }
}
