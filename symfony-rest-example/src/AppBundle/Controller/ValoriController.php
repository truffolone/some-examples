<?php

namespace AppBundle\Controller;
 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Carte;
use AppBundle\Entity\Valori;

class ValoriController extends FOSRestController {

    private $_rarities = array("c", "u", "r", "m");

    /**
     * @Rest\Get("/valori")
     */
    public function getAction()
    {
       $restresult = $this->getDoctrine()->getRepository('AppBundle:Valori')->findAll();
        if ($restresult === null || count($restresult) === 0) {
          return new View("Non sono state trovate carte", Response::HTTP_NOT_FOUND);
       }
        return $restresult;
    }

      /**
     * @Rest\Get("/valori/{id}")
     */
     public function idAction($id)
     {
       $singleresult = $this->getDoctrine()->getRepository('AppBundle:Valori')->find($id);
       if ($singleresult === null || count($restresult) === 0) {
       return new View("carta non trovata", Response::HTTP_NOT_FOUND);
       }
     return $singleresult;
     }

      /**
     * @Rest\Post("/valori/")
     */
     public function postAction(Request $request)
     {
       $data = new Valori;
       $regsellprice = $request->get('regsellprice');
       $foilsellprice = $request->get('foilsellprice');
       $regbuyprice = $request->get('regbuyprice');
       $foilbuyprice = $request->get('foilbuyprice');
       $maxregbuyqt = $request->get('maxregbuyqt');
       $maxfoilbuyqt = $request->get('maxfoilbuyqt');
       $cardid = $request->get('cardid');
       if(empty($regsellprice) || empty($foilsellprice) || empty($regbuyprice) || empty($foilbuyprice) || empty($cardid))
       {
           return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE); 
       } else {
            $cardcheck = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($cardid);
            if($cardcheck === null || count($cardcheck) === 0) {
                return new View("La carta ID " . (int) $cardid . " non esiste nel database delle carte", Response::HTTP_NOT_ACCEPTABLE);
            }
       }
       $data->setRegsellprice($regsellprice);
       $data->setFoilsellprice($foilsellprice);
       $data->setRegbuyprice($regbuyprice);
       $data->setFoilBuyPrice($foilbuyprice);
       $data->setMaxregbuyqt($maxregbuyqt);
       $data->setMaxfoilbuyqt($maxfoilbuyqt);
       $data->setCardid($cardid);
       $em = $this->getDoctrine()->getManager();
       $em->persist($data);
       $em->flush();
        return new View("Valori carta inseriti con successo", Response::HTTP_OK);
     }

         /**
         * @Rest\Put("/valori/{id}")
         */
         public function updateAction($id,Request $request)
         { 
             $data = new Valori;
             $data->setRegsellprice($regsellprice);
             $data->setFoilsellprice($foilsellprice);
             $data->setRegbuyprice($regbuyprice);
             $data->setFoilBuyPrice($foilbuyprice);
             $data->setMaxregbuyqt($maxregbuyqt);
             $data->setMaxfoilbuyqt($maxfoilbuyqt);
             $data->setCardid($cardid);
             $sn = $this->getDoctrine()->getManager();
             $valore = $this->getDoctrine()->getRepository('AppBundle:Valori')->find($id);
            if (empty($valore)) {
               return new View("Valori carta non trovati", Response::HTTP_NOT_FOUND);
             } else {
                if(!empty($regsellprice)) {
                    $valore->setRegsellprice($regsellprice);
                }
                if(!empty($foilsellprice)) {
                    $valore->setFoilsellprice($foilsellprice);
                }
                if(!empty($regbuyprice)) {
                    $valore->setRegbuyprice($regbuyprice);
                }
                if(!empty($foilbuyprice)) {
                    $valore->setFoilbuyprice($foilbuyprice);
                }
                if(!empty($maxregbuyqt)) {
                    $valore->setMaxregbuyqt($maxregbuyqt);
                }
                if(!empty($maxfoilbuyqt)) {
                    $valore->setMaxfoilbuyqt($maxfoilbuyqt);
                }
                if(!empty($cardid)) {
                    $cardcheck = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($cardid);
                    if($cardcheck === null || count($cardcheck) === 0) {
                        return new View("La carta ID " . (int) $cardid . " non esiste nel database delle carte", Response::HTTP_NOT_ACCEPTABLE);
                    } else {
                      $valore->setCardid($cardid);
                    }
                }
                $sn->flush();
                return new View("Valori carta aggiornato con successo", Response::HTTP_OK);
             }
       }

        /**
         * @Rest\Delete("/valori/{id}")
         */
         public function deleteAction($id)
         {
          $data = new Valori;
          $sn = $this->getDoctrine()->getManager();
          $valori = $this->getDoctrine()->getRepository('AppBundle:Valori')->find($id);
        if (empty($valori)) {
          return new View("valori carta non trovati", Response::HTTP_NOT_FOUND);
         }
         else {
          $sn->remove($valori);
          $sn->flush();
         }
          return new View("Valori Carta rimossi con successo", Response::HTTP_OK);
         }
}