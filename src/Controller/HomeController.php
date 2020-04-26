<?php

namespace App\Controller;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {   
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->findAll();
        

        return $this->render('home/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
