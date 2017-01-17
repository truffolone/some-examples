<?php

namespace AppBundle\Controller;
 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Carte;
use AppBundle\Entity\Valori;

class ImportController extends Controller {

     public function cardsAction()
     {
          if (($handle = fopen("/var/www/html/cards/import/cards.csv", "r")) !== FALSE) {
              $cards = $this->getDoctrine()->getRepository('AppBundle:Carte');
              while(($row = fgetcsv($handle)) !== FALSE) {
                $nome = trim($row[0]);
                $esp = strtoupper(trim($row[1]));
                $rarity = trim(strtolower($row[2]));
                if(count($cards->findOneBy(
                      array('nome' => $nome, 'esp' => $esp)
                  )) === 0) {
                     $data = new Carte;
                     $data->setNome($nome);
                     $data->setEsp($esp);
                     $data->setRarity($rarity);
                     $em = $this->getDoctrine()->getManager();
                     $em->persist($data);
                     $em->flush();
                }
                  echo $row[0] . " - " . $row[1] . " - " . $row[2] . "<br>";
              }
          }
         //return response.
     }
}