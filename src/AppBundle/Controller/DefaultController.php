<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
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

        $form = $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl("create_message")
        ]);

        $sort = self::getSort($request);
        $order = self::getOrder($request);

        $messages = $this->getDoctrine()->getRepository(Message::class)
            ->createQueryBuilder("message")
            ->orderBy("message.".$sort, $order)
            ->getQuery()
            ->getResult();

        return [
            'messages' => $messages,
            'p' => [
                'sort' => $sort,
                'direction' => $order
            ],
            'messageForm' => $form->createView()
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    protected static function getSort(Request $request)
    {
        $sort = $request->query->get("sort", "createdAt");
        $sorts = ["author", "email", "createdAt"];

        return in_array($sort, $sorts, true) ? $sort : "createdAt";
    }

    /**
     * @param Request $request
     * @return array
     */
    protected static function getOrder(Request $request)
    {
        $direction = $request->query->get("direction", "desc");
        $directions = ["asc", "desc"];

        return in_array($direction, $directions, true) ? $direction : "desc";
    }
}
