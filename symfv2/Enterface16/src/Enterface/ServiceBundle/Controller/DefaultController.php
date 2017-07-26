<?php

namespace Enterface\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;

class DefaultController extends Controller {
    
    
private function getServicesPrices($mail) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('Enterface\ServiceBundle\Entity\PrixService');

        $priceArray = $repository->findby(array('idService' => 1, 'idTypeService' => 1));
        $basicCleoPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 2, 'idTypeService' => 1));
        $basicClesPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 3, 'idTypeService' => 1));
        $basicMedBoxPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 1, 'idTypeService' => 2));
        $advancedCleoPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 2, 'idTypeService' => 2));
        $advancedClesPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 3, 'idTypeService' => 2));
        $advancedMedBoxPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 1, 'idTypeService' => 3));
        $personalizedCleoPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 2, 'idTypeService' => 3));
        $PersonalizedClesPrice = $priceArray[0]->getPrix();

        $priceArray = $repository->findby(array('idService' => 3, 'idTypeService' => 3));
        $PersonalizedMedBoxPrice = $priceArray[0]->getPrix();


        return array('mail' => $mail,
            'basicCleoPrice' => $basicCleoPrice,
            'basicClesPrice' => $basicClesPrice,
            'basicMedBoxPrice' => $basicMedBoxPrice,
            'advancedCleoPrice' => $advancedCleoPrice,
            'advancedClesPrice' => $advancedClesPrice,
            'advancedMedBoxPrice' => $advancedMedBoxPrice,
            'personalizedCleoPrice' => $personalizedCleoPrice,
            'PersonalizedClesPrice' => $PersonalizedClesPrice,
            'PersonalizedMedBoxPrice' => $PersonalizedMedBoxPrice
        );
    }
    
    
    public function indexAction() {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository('Enterface\ServiceBundle\Entity\PrixService');
//
//        $priceArray = $repository->findby(array('idService' => 1, 'idTypeService' => 1));
//        $basicCleoPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 2, 'idTypeService' => 1));
//        $basicClesPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 3, 'idTypeService' => 1));
//        $basicMedBoxPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 1, 'idTypeService' => 2));
//        $advancedCleoPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 2, 'idTypeService' => 2));
//        $advancedClesPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 3, 'idTypeService' => 2));
//        $advancedMedBoxPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 1, 'idTypeService' => 3));
//        $personalizedCleoPrice = $priceArray[0]->getPrix();
//
//        $priceArray = $repository->findby(array('idService' => 2, 'idTypeService' => 3));
//        $PersonalizedClesPrice = $priceArray[0]->getPrix();
//        
//        $priceArray = $repository->findby(array('idService' => 3, 'idTypeService' => 3));
//        $PersonalizedMedBoxPrice = $priceArray[0]->getPrix();
//
//
//        return $this->render('EnterfaceServiceBundle:Default:index.html.twig', array('mail' => 0,
//                    'basicCleoPrice' => $basicCleoPrice,
//                    'basicClesPrice' => $basicClesPrice,
//                    'basicMedBoxPrice' => $basicMedBoxPrice,
//                    'advancedCleoPrice' => $advancedCleoPrice,
//                    'advancedClesPrice' => $advancedClesPrice,
//                    'advancedMedBoxPrice'=>$advancedMedBoxPrice,
//                    'personalizedCleoPrice'=>$personalizedCleoPrice,
//                    'PersonalizedClesPrice'=>$PersonalizedClesPrice,
//                    'PersonalizedMedBoxPrice'=>$PersonalizedMedBoxPrice
//                    ));
   
        $arrayServicesPrices = $this->getServicesPrices(0);
        return $this->render('EnterfaceServiceBundle:Default:index.html.twig', $arrayServicesPrices);
    }        
    
     public function mailAction() {
      
        $arrayServicesPrices = $this->getServicesPrices(1);

        return $this->render('EnterfaceServiceBundle:Default:index.html.twig', $arrayServicesPrices);
    }

}
