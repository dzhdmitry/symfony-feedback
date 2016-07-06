<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            if ($picture = $message->getPicture()) {
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $picture->getOriginalFilename();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $directory = $this->container->getParameter('pictures_dir');

                $file->move($directory, $fileName);

                $picture->setFilename("/pictures/".$fileName);
                $picture->setOriginalFilename($file->getClientOriginalName());
            }

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

        $form = $this->createForm(MessageType::class, $message, [
            'action' => $this->generateUrl("create_message")
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($picture = $message->getPicture()) {
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
                $file = $picture->getOriginalFilename();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $directory = $this->container->getParameter('previews_dir');

                $file->move($directory, $fileName);

                $picture->setFilename("/previews/".$fileName);
                $picture->setOriginalFilename($file->getClientOriginalName());
            }

            return [
                'message' => $message
            ];
        } else {
            return $this->render("@App/Messages/create.html.twig", [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * Security("has_role('ROLE')")
     * @Route("/{id}/approve", name="approve_message")
     * @Method("PUT")
     * @param Request $request
     * @param $id
     */
    public function approveAction(Request $request, $id)
    {
        //
    }
}
