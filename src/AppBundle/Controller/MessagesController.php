<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageEditType;
use AppBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/messages")
 */
class MessagesController extends Controller
{
    /**
     * @Template
     * @Route("", name="create_message")
     * @Method("POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function createAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl("create_message")
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get("app.message_manager")->create($message);

            return $this->redirectToRoute("homepage");
        } else {
            return [
                'form' => $form->createView()
            ];
        }
    }

    /**
     * @Template
     * @Route("/preview", name="preview_message")
     * @param Request $request
     * @return array
     */
    public function previewAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl("create_message")
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get("app.picture_handler")->uploadPreview($message);

            $success = true;
            $html = $this->renderView("@App/Messages/preview.html.twig", [
                'message' => $message
            ]);
        } else {
            $success = false;
            $html = $this->renderView("@App/messageCreateForm.html.twig", [
                'form' => $form->createView()
            ]);
        }

        return JsonResponse::create([
            'success' => $success,
            'html' => $html
        ]);
    }

    /**
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}", name="edit_message")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function editAction(Request $request, $id)
    {
        $message = $this->findMessage($id);
        $form = $this->createForm(MessageEditType::class, $message, [
            'action' => $this->generateUrl("edit_message", [
                'id' => $id
            ])
        ]);

        if ($request->isMethod("get")) {
            // page
            return [
                'message' => $message,
                'form' => $form->createView()
            ];
        } else {
            $form->handleRequest($request);

            if ($request->isXmlHttpRequest()) {
                // preview
                if ($form->isValid()) {
                    $success = true;
                    $html = $this->renderView("@App/Messages/preview.html.twig", [
                        'message' => $message
                    ]);
                } else {
                    $success = false;
                    $html = $this->renderView("@App/messageEditForm.html.twig", [
                        'form' => $form->createView()
                    ]);
                }

                return JsonResponse::create([
                    'success' => $success,
                    'html' => $html
                ]);
            } else {
                // save
                if ($form->isValid()) {
                    $this->get("app.message_manager")->update($message);

                    return $this->redirectToRoute("admin");
                } else {
                    return [
                        'message' => $message,
                        'form' => $form->createView()
                    ];
                }
            }
        }
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/approve", name="approve_message")
     * @Method("PUT")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function approveAction($id)
    {
        $message = $this->findMessage($id);

        $this->get("app.message_manager")->setApproved($message, true);

        return $this->redirectToRoute("admin");
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/disapprove", name="disapprove_message")
     * @Method("PUT")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disapproveAction($id)
    {
        $message = $this->findMessage($id);

        $this->get("app.message_manager")->setApproved($message, false);

        return $this->redirectToRoute("admin");
    }

    /**
     * @param $id
     * @return Message
     */
    protected function findMessage($id)
    {
        if ($message = $this->getDoctrine()->getRepository(Message::class)->find($id)) {
            return $message;
        } else {
            throw $this->createNotFoundException("Message not found");
        }
    }
}
