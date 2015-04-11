<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Product;

class BasketController extends Controller
{
    /**
     * @Route("/koszyk", name="basket")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'basket' => $this->get('basket'),
        );
    }

    /**
     * @Route("/koszyk/{id}/dodaj", name="basket_add")
     * @Template()
     */
    public function addAction(Request $request, Product $product)
    {
        if (is_null($product)){
            $this->addFlash('error', 'Produkt który próbujesz dodać jest niedostępny');
            return $this->redirectToRoute('products_list');
        }
        
        try {
            
            $basket = $this->get('basket');
            $basket->add($product);
            
        } catch (\Exception $e) {
             $this->addFlash('error', $e->getMessage());
             return $this->redirect($request->headers->get('referer'));
        }    
                
        $this->addFlash('notice', sprintf('Produkt "%s" został dodany do koszyka', $product->getName()));

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/koszyk/{id}/usun", name="basket_remove")
     * @Template()
     */
    public function removeAction(Product $product)
    {
        $basket = $this->get('basket');
        
        try{
        $basket->remove($product);

        $this->addFlash('notice', sprintf('Produkt "%s" został usunięty z koszyka', $product->getName()));
                    
        } catch (\Exception $ex) {
            $this->addFlash('notice', $ex->getMessage());
        }

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/koszyk/{id}/zaktualizuj-ilosc/{quantity}", name="basket_update")
     * @Template()
     */
    public function updateAction($id, $quantity)
    {
        $session = $request->getSession();

        $basket = $session->get('basket', array());

        $basket[$id] = 0;

        $session->set('basket', $basket);
        $this->addFlash('notice', 'Produkt został usunięty z koszyka');

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/koszyk/wyczysc", name="basket_clear")
     * @Template()
     */
    public function clearAction()
    {
        $basket = $this
                ->get('basket')
                ->clear();
          
        $this->addFlash('notice', 'Wszystkie produkty zostały usunięte z koszyka');
                    
        return $this->redirectToRoute('basket');
        
    }

    /**
     * @Route("/koszyk/kup")
     * @Template()
     */
    public function buyAction()
    {
        return array(
                // ...
            );
    }

    private function getProducts()
    {
        $file = file('product.txt');
        $products = array();
        foreach ($file as $p) {
            $e = explode(':', trim($p));
            $products[$e[0]] = array(
                'id' => $e[0],
                'name' => $e[1],
                'price' => $e[2],
                'desc' => $e[3],
            );
        }

        return $products;
    }
}
