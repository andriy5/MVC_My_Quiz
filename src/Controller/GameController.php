<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Categorie;
use App\Entity\Reponse;
use App\Form\QuestionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// ROUTE GENERAL avec cat_id
class GameController extends AbstractController
{
    /**
     * @Route("/game/{cat_id}/{quest_id}", name="game")
     */
    public function question($cat_id, $quest_id, Request $request)
    {

        // Retrieve categorie
        $repository = $this->getDoctrine()->getRepository(Categorie::class);
        $categorie = $repository->findOneBy([
            'id' => $cat_id
        ]);
        
        // Retrieve questions
        $repository = $this->getDoctrine()->getRepository(Question::class);
        $question = $repository->findOneBy([
            'id' => $quest_id,
            'idCategorie' => $cat_id,
        ]);


        // Retrieve answers
        $repository = $this->getDoctrine()->getRepository(Reponse::class);
        $answers = $repository->findBy([
            'idQuestion' => $quest_id,
        ]);
        // dd($answers);
            
        return $this->render('game/question.html.twig', [
            'categorie' => $categorie,
            'question' => $question,
            'answers' => $answers
        ]);
    }
}
