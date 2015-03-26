<?php
namespace AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Category;
use AppBundle\Form\ProductType;

class ProductsController extends Controller
{
    /**
     * @Route("/produkty/{id}", name="products_list", defaults={"id" = false}, requirements={"id": "\d+"})
     * 
     */
    public function indexAction(Request $request, Category $category = null)
    {
        $getProductsQuery = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->getProductsQuery($category);
        
        $paginator = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $getProductsQuery,
            $request->query->get('page', 1),
            8
        );
        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    /**
     * 
     * @Route("/produkty/dodaj", name="products_add")
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(new ProductType());
        $form->handleRequest($request);
        
        return $this->render('products/add.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/produkty/{id}/show", name="products_show")
     */
    public function showAction($id)
    {
        
    $product = $this->getDoctrine()
        ->getRepository('AppBundle:Product')
        ->find($id);

    if (!$product) {
        throw $this->createNotFoundException(
            'No product found for id '.getName($id)
        );
    }
        return $this->render('products/show.html.twig',[
            'product' => $product,
        ]);
        
    }
}