<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
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
        $form = $this->createMessageForm($message);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get("app.picture_handler")->uploadPicture($message);

            $em = $this->getDoctrine()->getManager();

            $em->persist($message);
            $em->flush();

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
        $form = $this->createMessageForm($message);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get("app.picture_handler")->uploadPreview($message);

            $success = true;
            $html = $this->renderView("@App/Messages/preview.html.twig", [
                'message' => $message
            ]);
        } else {
            $success = false;
            $html = $this->renderView("@App/messagesForm.html.twig", [
                'form' => $form->createView()
            ]);
        }

        return JsonResponse::create([
            'success' => $success,
            'html' => $html
        ]);
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
        $em = $this->getDoctrine()->getManager();

        $message->setApproved(true);

        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute("homepage");
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
        $em = $this->getDoctrine()->getManager();

        $message->setApproved(false);

        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute("homepage");
    }

    /**
     * @param Message $message
     * @return \Symfony\Component\Form\Form
     */
    protected function createMessageForm(Message $message)
    {
        return $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl("create_message")
        ]);
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
