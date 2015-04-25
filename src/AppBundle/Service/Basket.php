<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Product;

class Basket{
    
    private $session;
    
    public function __construct(Session $session) 
    {
        $this->session = $session;
    }
    
    public function getProducts() 
    {
        return $this->session->get('basket', array());        
        
    }
    
    public function add(Product $product, $quantity = 1) 
    {
        if ($product->getAmount() <= 0){
            throw new \Exception('Produkt który próbujesz dodać jest niedostępny');
        }
        
        $products = $this->getProducts();
        if (!array_key_exists($product->getId(), $products)) {
        
        $products[$product->getId()] = array(
            'id'        => $product->getId(),
            'slug'      => $product->getSlug(),
            'name'      => $product->getName(),
            'price'     => $product->getPrice(),
            'quantity'  => 0
        );
        }
        
        //aktualizuje ilosc w koszyku
        $products[$product->getId()]['quantity'] += $quantity;
        
        //zapisuje do sesji
        $this->session->set('basket', $products);
        
        return $this;
    }
    
    public function remove(Product $product)
    {
        $products = $this->getProducts();
        
        if(!array_key_exists($product->getId(), $products)) {
            throw new \Exception(sprintf('Produkt "%s" nie znajduje się w towim koszyku', $product->getName()));
        }
            
        unset($products[$product->getId()]);

        $this->session->set('basket', $products);

        return $this;
    }
    
    public function clear() 
    {                  
        $this->session->remove('basket');

        return $this;
    }
    
    public function getPrice()
    {
        $price = 0;
        
        foreach ($this->getProducts() as $product){
            $price += $product['price'] * $product['quantity'];
        }
        return $price;
    }
    public function getQuantity()
    {
        $quantity = 0;
        
        foreach ($this->getProducts() as $product){
            $quantity += $product['quantity'];
        }
        return $quantity;
    }
}
