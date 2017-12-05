<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageCreateType;
use AppBundle\Util\MessagesCriteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Template
     * @Route("/")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $criteria = MessagesCriteria::fromRequest($request)->setApproved(MessagesCriteria::FILTER_APPROVED);
        $messages = $this->getDoctrine()->getRepository(Message::class)->findMessages($criteria);
        $message = new Message();
        $form = $this->createForm(MessageCreateType::class, $message, [
            'action' => $this->generateUrl('app_messages_create')
        ]);

        return [
            'messages' => $messages,
            'criteria' => $criteria,
            'messageForm' => $form->createView()
        ];
    }

    /**
     * @Route("/locale/{_locale}", defaults={"_locale": "en"})
     * @param Request $request
     * @param $_locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setLocaleAction(Request $request, $_locale)
    {
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }
}
