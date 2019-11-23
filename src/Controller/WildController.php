<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */

    public function index() :Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/wild/show/{page}",
     *      requirements={"page"="[a-z0-9\-]+"},
     *      defaults={"page"=""},
     *      name="wild_show")
     */
    public function show(string $page): Response
    {
      if ($page == "") {
          echo "Aucune série sélectionnée, veuillez choisir une série";
          }else {
            $page = str_replace("-", " ", $page);
            $page = ucwords ($page , $delimiters = " \t\r\n\f\v");
      }
            return $this->render('wild/show.html.twig', ['page' => $page]);
    }

}
