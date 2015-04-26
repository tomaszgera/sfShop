<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Comment;
use AppBundle\Entity\CommentVote;

class CommentController extends Controller {

    /**
     * @Route("/komentarze", name="comments_list")
     */
    public function commentsAction(Request $request) {
        $getCommentsQuery = $this->getDoctrine()
                ->getRepository('AppBundle:Comment')
                ->getCommentsQuery($this->getUser());
        $paginator = $this->get('knp_paginator');
        $comments = $paginator->paginate(
                $getCommentsQuery, $request->query->get('page', 1), 10
        );
        return $this->render('Comment/list.html.twig', [
                    'comments' => $comments,
        ]);
    }

    /**
     * @Route("/komentarze/usun/{id}", name="comment_remove")
     * @Security("user == comment.getUser()")
     */
    public function removeAction(Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'Twój komentarz został usunięty');
        return $this->redirectToRoute('comments_list', []);
    }

    /**
     * 
     * @Route("/komentarze/glosuj-up/{id}/{productId}", name="vote_up")
     * 
     * 
     */
    public function voteUpAction(Comment $comment, $productId, Request $request) {

        $commentVote = $this->getDoctrine()
                ->getRepository('AppBundle:CommentVote')
                ->findOneBy([
            'user' => $this->getUser(),
            'comment' => $comment,
        ]);
        if ($commentVote) {
            $this->addFlash('error', 'Możesz zagłosować na komentarz tylko raz');
        } else {
            $em = $this->getDoctrine()->getManager();
            $commentVote = new CommentVote();
            $commentVote->setComment($comment);
            $commentVote->setUser($this->getUser());
            $em->persist($commentVote);
            $comment->setNbVoteUp($comment->getNbVoteUp() + 1);
            $em->persist($comment);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * 
     * @Route("/komentarze/glosuj-down/{id}/{productId}", name="vote_down")
     * 
     * 
     * 
     */
    public function voteDownAction(Comment $comment, $productId, Request $request) {

        $commentVote = $this->getDoctrine()
                ->getRepository('AppBundle:CommentVote')
                ->findOneBy([
            'user' => $this->getUser(),
            'comment' => $comment,
        ]);
        if ($commentVote) {
            $this->addFlash('error', 'Możesz zagłosować na komentarz tylko raz');
        } else {
            $em = $this->getDoctrine()->getManager();
            $commentVote = new CommentVote();
            $commentVote->setComment($comment);
            $commentVote->setUser($this->getUser());
            $em->persist($commentVote);
            $comment->setNbVoteDown($comment->getNbVoteDown() + 1);
            $em->persist($comment);
            $em->flush();
        }
        return $this->redirect($request->headers->get('referer'));
        
    }

}
