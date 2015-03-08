<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BasketController extends Controller
{
    /**
     * @Route("/koszyk", name="basket")
     * @Template()
     */
    public function indexAction()
    {
        return array(
                // ...
            );
    }

    /**
     * @Route("/koszyk/{id}/dodaj")
     * @Template()
     */
    public function addAction($id)
    {
        return array(
                // ...
            );
    }

    /**
     * @Route("/koszyk/{id}/usun")
     * @Template()
     */
    public function removeAction($id)
    {
        return array(
                // ...
            );
    }

    /**
     * @Route("/koszyk/{id}/zaktualizuj-ilosc/{quantity}")
     * @Template()
     */
    public function updateAction($id, $quantity)
    {
        return array(
                // ...
            );
    }

    /**
     * @Route("/koszyk/wyczysc")
     * @Template()
     */
    public function clearAction()
    {
        return array(
                // ...
            );
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

}
