<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Image;
use App\Form\ArticleType;
use App\Services\FlashMessage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{
    /**
     * @Route("admin/article/new", name="article_new")
     * @param Request $request
     * @param FlashMessage $flashMessage
     * @return Response
     */
    public function newAction(Request $request, FlashMessage $flashMessage): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser());
            if (null == $article->getImage()->getFile()) {
                $article->setImage(null);
            }

            $em->persist($article);
            $em->flush();

            $flashMessage->createMessage($request, 'info', "L'article a été créé avec succès");

            return $this->redirectToRoute('admin-articles');
        }

        return $this->render('backoffice/article/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("admin/article/{id}/edit", name="article_edit")
     *
     * @param Request $request
     * @param int $id
     * @param FlashMessage $flashMessage
     * @return Response
     */
    public function editAction(Request $request, int $id, FlashMessage $flashMessage): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);
        $currentImage = $article->getImage();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null == $article->getImage()->getId() && null == $article->getImage()->getFile()) {
                $article->setImage(null);
            } else if (null != $article->getImage()->getFile()) {
                if (null != $currentImage) {
                    $em->remove($currentImage);
                }

                $image = new Image();
                $image->setFile($article->getImage()->getFile());
                $article->setImage($image);
            } else if (true == $form->getData()->getImage()->isDeletedImage()) {
                $article->setImage(null);
                $em->remove($currentImage);
            }
            $em->flush();

            $flashMessage->createMessage($request, 'info', "L'article a été mis à jour avec succès");

            return $this->redirect($request->getUri());
        }

        return $this->render('backoffice/article/edit.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
            'currentImage' => $currentImage
        ));
    }

    /**
     * @Route("admin/article/{id}/delete", name="article_delete")
     *
     * @param Request $request
     * @param int $id
     * @param FlashMessage $flashMessage
     * @return Response
     */
    public function deleteAction(Request $request, int $id, FlashMessage $flashMessage): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('App:Article')->find($id);

        $em->remove($article);
        $em->flush();

        $flashMessage->createMessage($request, "info", "L'annonce été supprimé avec succès");
        
        return $this->redirectToRoute('admin-articles');
    }
}