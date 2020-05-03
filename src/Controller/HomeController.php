<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
// use Symfony\Component\HttpFoundation\Session\Session;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request)
    {   
        // Check the current cookie
        $currentCookie = $request->cookies->get('temp_id');
        if (!$currentCookie) {
            // Set cookie if doesn't exists
            $response = new Response();

            $cookieValue1 = rand(0, 2147483647);
            $cookieValue2 = rand(0, 2147483647);
            $cookieValue = $cookieValue1 . $cookieValue2;
            
            $cookie = new Cookie("temp_id", $cookieValue);
            $response->headers->setCookie($cookie);
            $response->send();
        }
        
        // Retrieve all categories
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->findAll();


        // Check that there is minimum 10 questions
        $categories_ready = [];
        foreach ($categories as $key => $category) {
            if (count($category->getQuestion()) >= 10 ) {
                array_push($categories_ready, $category);
            }
        }
    

        return $this->render('home/index.html.twig', [
            'categories' => $categories_ready,
            'categories_create' => $categories
        ]);
    }
}
