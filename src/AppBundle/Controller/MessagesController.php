<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageEditType;
use AppBundle\Form\MessageCreateType;
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
     * @Route("", methods={"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm(MessageCreateType::class, $message, [
            'action' => $this->generateUrl("app_messages_create")
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get("app.message_manager")->create($message);
            $this->addFlash("success", "message.created");

            return $this->redirectToRoute("app_default_index");
        } else {
            return [
                'form' => $form->createView()
            ];
        }
    }

    /**
     * @Route("/preview")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \AppBundle\Exception\PictureHandlerException
     */
    public function previewDraftAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm(MessageCreateType::class, $message, [
            'action' => $this->generateUrl("app_messages_create")
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get("app.preview_handler")->upload($message);

            $success = true;
            $html = $this->renderView("@App/Messages/previewDraft.html.twig", [
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
     * @Route("/{id}")
     *
     * @param Request $request
     * @param Message $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction(Request $request, Message $message)
    {
        $form = $this->createForm(MessageEditType::class, $message, [
            'action' => $this->generateUrl("app_messages_edit", [
                'id' => $message->getId()
            ])
        ]);

        if ($request->isMethod("get")) {
            return [
                'message' => $message,
                'form' => $form->createView()
            ];
        } else {
            $data = [
                'body' => $message->getBody()
            ];

            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->get("app.message_manager")->update($message, $data);

                return $this->redirectToRoute("app_admin_index");
            } else {
                return [
                    'message' => $message,
                    'form' => $form->createView()
                ];
            }
        }
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/preview")
     *
     * @param Request $request
     * @param Message $message
     * @return JsonResponse
     */
    public function previewAction(Request $request, Message $message)
    {
        $form = $this->createForm(MessageEditType::class, $message, [
            'action' => $this->generateUrl("app_messages_edit", [
                'id' => $message->getId()
            ])
        ]);

        $form->handleRequest($request);

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
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/approve", methods={"PUT"})
     *
     * @param Message $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function approveAction(Message $message)
    {
        $this->get("app.message_manager")->setApproved($message, true);

        return $this->redirectToRoute("app_admin_index");
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/disapprove", methods={"PUT"})
     *
     * @param Message $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function disapproveAction(Message $message)
    {
        $this->get("app.message_manager")->setApproved($message, false);

        return $this->redirectToRoute("app_admin_index");
    }
}
