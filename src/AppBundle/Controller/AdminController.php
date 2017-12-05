<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Util\MessagesCriteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Template
     * @Route("")
     *
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        return $this->response($request);
    }

    /**
     * @Template("@App/Admin/index.html.twig")
     * @Route("/approved")
     *
     * @param Request $request
     * @return array
     */
    public function approvedAction(Request $request)
    {
        return $this->response($request, MessagesCriteria::FILTER_APPROVED);
    }

    /**
     * @Template("@App/Admin/index.html.twig")
     * @Route("/disapproved")
     *
     * @param Request $request
     * @return array
     */
    public function disapprovedAction(Request $request)
    {
        return $this->response($request, MessagesCriteria::FILTER_DISAPPROVED);
    }

    /**
     * @param Request $request
     * @param string $approved
     * @return array
     */
    protected function response(Request $request, $approved = MessagesCriteria::FILTER_ALL)
    {
        $criteria = MessagesCriteria::fromRequest($request)->setApproved($approved);
        $repo = $this->getDoctrine()->getRepository(Message::class);

        return [
            'messages' => $repo->findMessages($criteria),
            'criteria' => $criteria,
            'amount' => [
                'all' => $repo->countMessages(),
                'approved' => $repo->countMessages(MessagesCriteria::FILTER_APPROVED),
                'disapproved' => $repo->countMessages(MessagesCriteria::FILTER_DISAPPROVED),
            ]
        ];
    }
}
