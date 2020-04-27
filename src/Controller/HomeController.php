<?php

namespace App\Controller;

use App\Entity\Categorie;
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
    // private $session;

    // public function _construct (SessionInterface $session)
    // {
    //     $this->session = $session;
    // }


    
    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request)
    {   
        // ESSAI 1: SESSION
        // note: session se rafraichit regulierement
        // $session = new Session();
        // $session->start();
    
        // set and get session attributes
        // $session->set('name', 'Drak');
        // $s = $session->getId();
        
        
        
        // ESSAI 2: COOKIE
        // $cookie = new Cookie('foo', 'bar', strtotime('now + 1 year'));
        // $response->headers->setCookie($cookie);
        // $request = new Request(
            //     $_GET,
            //     $_POST,
            //     [],
            //     $_COOKIE,
            //     $_FILES,
            //     $_SERVER
            // );
        // $request = Request::createFromGlobals();
        // $request->cookies->set('foo', 'bar');
        // $request->cookies->set('foo', 'bar');
        // $request->cookies->get('PHPSESSID');
        // $response = new Response(
        //     'Content',
        //     Response::HTTP_OK,
        //     ['content-type' => 'text/html']
        // );
        // $response->cookies->set('foo', 'bar');
        // $response->headers->setCookie(new Cookie('foo', 'bar'));
        // $response->prepare($request);
        // $response->send();
        // dump($request->request->get('foo'));

        $response = new Response();
        $cookie = new Cookie("testing", "testing");
        $response->headers->setCookie($cookie);
        $response->send();
        // dd($response);

    

        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categories = $repository->findAll();

        

        return $this->render('home/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
