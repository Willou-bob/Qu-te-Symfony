<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use App\Entity\Episode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */

    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        return $this->render('wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show_program")
     * @return Response
     */
    public function showByProgram(?string $slug) :Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }


    /**
     * @param string $categoryName is the slugger
    * @Route("wild/category/{categoryName}", name="show_category")
    * @return Response
    */

    public function showByCategory(string $categoryName): Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category'=> $category],
                ['id'=> 'desc'],
                3);

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' id, found in program\'s table.'
            );
        }
        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'category'  => $category
        ]);
    }

    /**
     * @param string $id
     * @Route("show/season/{id}", name="show_season")
     * @return Response
     */
    public function showBySeason(int $id) :Response
    {
        if (!$id) {
            throw $this
                ->createNotFoundException('No id in season table.');
        }

        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);

        if (!$season) {
            throw $this
                ->createNotFoundException('No season in season table.');
        }

        return $this->render('Wild/showSeason.html.twig', [
            'season' => $season,
            'id'=> $id,
        ]);
    }

    /**
     * @param Episode $episode
     * @return Response
     * @Route("wild/episode/{id}", name="show_episode")
     */
    public function showEpisode(Episode $episode) :Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        return $this->render('Wild/episode.html.twig',
            ['episode'=>$episode,
            'season'=>$season,
            'program'=>$program]);
    }
}
