<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/admin")
 */
class AdminController extends BaseController
{
    /**
     * @Template
     * @Route("", name="admin")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        return $this->response($request);
    }

    /**
     * @Template("@App/Admin/index.html.twig")
     * @Route("/approved", name="admin_approved")
     * @param Request $request
     * @return array
     */
    public function approvedAction(Request $request)
    {
        return $this->response($request, true);
    }

    /**
     * @Template("@App/Admin/index.html.twig")
     * @Route("/disapproved", name="admin_disapproved")
     * @param Request $request
     * @return array
     */
    public function disapprovedAction(Request $request)
    {
        return $this->response($request, false);
    }

    /**
     * @param Request $request
     * @param bool|null $approved
     * @return array
     */
    protected function response(Request $request, $approved = null)
    {
        $sort = self::getSort($request);
        $order = self::getOrder($request);
        $repo = $this->getDoctrine()->getRepository(Message::class);
        $messages = $repo->findMessages($sort, $order, $approved);

        return [
            'messages' => $messages,
            'count' => [
                'all' => $repo->countMessages(),
                'approved' => $repo->countMessages(true),
                'disapproved' => $repo->countMessages(false),
            ],
            'p' => [
                'sort' => $sort,
                'direction' => $order
            ]
        ];
    }
}
