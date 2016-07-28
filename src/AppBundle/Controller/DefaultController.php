<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Picture;
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

    /**
     * @Route("/locale/{_locale}", name="setLocale", defaults={"_locale": "en"})
     * @param Request $request
     * @param $_locale
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setLocaleAction(Request $request, $_locale)
    {
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * @Route("/picture/{slug}/{filename}", name="picture")
     * @param $slug
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function pictureAction($slug, $filename)
    {
        $picture = $this->getDoctrine()->getManager()->getRepository(Picture::class)->findOneBy([
            'slug' => $slug,
            'originalFilename' => $filename
        ]);

        if (!$picture) {
            throw $this->createNotFoundException();
        }

        return $this->get("app.picture_handler")->pictureResponse($picture->getFilename(), $picture->getOriginalFilename());
    }

    /**
     * @Route("/preview/{filename}", name="preview")
     * @param $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function previewAction($filename)
    {
        return $this->get("app.preview_handler")->pictureResponse($filename);
    }
}
