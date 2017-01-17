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

class CardController extends FOSRestController {

    private $_rarities = array("c", "u", "r", "m");

    /**
     * @Rest\Get("/card")
     */
    public function getAction()
    {
       $restresult = $this->getDoctrine()->getRepository('AppBundle:Carte')->findAll();
        if ($restresult === null || count($restresult) === 0) {
          return new View("Non sono state trovate carte", Response::HTTP_NOT_FOUND);
       }
        return $restresult;
    }

      /**
     * @Rest\Get("/card/{id}")
     */
     public function idAction($id)
     {
       $singleresult = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($id);
       if ($singleresult === null || count($restresult) === 0) {
       return new View("carta non trovata", Response::HTTP_NOT_FOUND);
       }
     return $singleresult;
     }

      /**
     * @Rest\Post("/card/")
     */
     public function postAction(Request $request)
     {
       $data = new Carte;
       $nome = $request->get('nome');
       $esp = $request->get('esp');
       $rarity = $request->get('rarity');
       if(empty($nome) || empty($esp))
       {
           return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE); 
       } 
       if(!in_array($rarity, $this->_rarities)) {
            return new View("Rarity not found (" . $rarity . ")", Response::HTTP_NOT_ACCEPTABLE); 
       }
       $data->setNome($nome);
       $data->setEsp($esp);
       $data->setRarity($rarity);
       $em = $this->getDoctrine()->getManager();
       $em->persist($data);
       $em->flush();
        return new View("Carta aggiunta con successo", Response::HTTP_OK);
     }

         /**
         * @Rest\Put("/card/{id}")
         */
         public function updateAction($id,Request $request)
         { 
             $data = new Carte;
             $nome = $request->get('nome');
             $esp = $request->get('esp');
             $rarity = $request->get('rarity');
             $sn = $this->getDoctrine()->getManager();
             $card = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($id);
            if (empty($card)) {
               return new View("user not found", Response::HTTP_NOT_FOUND);
             } else {
                if(!empty($nome)) {
                    $card->setNome($nome);
                }
                if(!empty($esp)) {
                    $card->setEsp($esp);
                }
                if(!empty($rarity) && in_array($rarity, $this->_rarities)) {
                    $card->setRarity($rarity);
                }
                $sn->flush();
                return new View("Carta aggiornata con successo", Response::HTTP_OK);
             }
       }

        /**
         * @Rest\Delete("/card/{id}")
         */
         public function deleteAction($id)
         {
          $data = new Carte;
          $sn = $this->getDoctrine()->getManager();
          $card = $this->getDoctrine()->getRepository('AppBundle:Carte')->find($id);
        if (empty($card)) {
          return new View("carta non trovata", Response::HTTP_NOT_FOUND);
         }
         else {
          $sn->remove($card);
          $sn->flush();
         }
          return new View("Carta rimossa con successo", Response::HTTP_OK);
         }
}