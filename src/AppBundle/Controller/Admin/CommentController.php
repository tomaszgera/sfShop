<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;
use AppBundle\Form\AdminCommentType as CommentType;
use AppBundle\Form\CommentFilterType;

/**
 * Comment controller.
 *
 * @Route("/admin/comment")
 */
class CommentController extends Controller {

    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="admin_comment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return array(
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        );
    }

    /**
     * Create filter form and process filter request.
     *
     */
    protected function filter() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $filterForm = $this->createForm(new CommentFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Comment')->createQueryBuilder('e');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('CommentControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->bind($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('CommentControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('CommentControllerFilter')) {
                $filterData = $session->get('CommentControllerFilter');
                $filterForm = $this->createForm(new CommentFilterType(), $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
     * Get results from paginator and get paginator view.
     *
     */
    protected function paginator($queryBuilder) {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $this->getRequest()->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me) {
            return $me->generateUrl('admin_comment', array('page' => $page));
        };

        // Paginator - view
        $translator = $this->get('translator');
        $view = new TwitterBootstrapView();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => $translator->trans('views.index.pagprev', array(), 'JordiLlonchCrudGeneratorBundle'),
            'next_message' => $translator->trans('views.index.pagnext', array(), 'JordiLlonchCrudGeneratorBundle'),
        ));

        return array($entities, $pagerHtml);
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/", name="admin_comment_create")
     * @Method("POST")
     * @Template("AppBundle:Comment:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Comment();
        $form = $this->createForm(new CommentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');

            return $this->redirect($this->generateUrl('admin_comment_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Comment entity.
     *
     * @Route("/new", name="admin_comment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Comment();
        $form = $this->createForm(new CommentType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Comment entity.
     *
     * @Route("/{id}", name="admin_comment_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Comment entity.
     *
     * @Route("/{id}/edit", name="admin_comment_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $editForm = $this->createForm(new CommentType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Comment entity.
     *
     * @Route("/{id}", name="admin_comment_update")
     * @Method("PUT")
     * @Template("AppBundle:Comment:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CommentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

            return $this->redirect($this->generateUrl('admin_comment_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/{id}", name="admin_comment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Comment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Comment entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('admin_comment'));
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
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
            $this->addFlash('danger', 'Możesz zagłosować na komentarz tylko raz');
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
            $this->addFlash('danger', 'Możesz zagłosować na komentarz tylko raz');
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
