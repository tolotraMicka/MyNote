<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Form\NoteType;
use App\Repository\NotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request,NotesRepository $note_repo): Response
    {
        $note = new Notes();

        $form = $this->createForm(NoteType::class,$note);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($note);
            $this->em->flush();
        }

        $note_All = $note_repo->findAll();        
        return $this->render('home/index.html.twig', [
            'form'=>$form->createView(),
            'noteAll'=>$note_All
        ]);
    }

    /**
     * @Route("/modification/{id}", name="app_modification", methods= {"GET", "POST"})
     */
    function update(int $id,Request $request,NotesRepository $note_repo,SerializerInterface $serializer) {

        $note = $note_repo->findOneBy(['id'=>$id]);
    
        $form = $this->createForm(NoteType::class,$note);
 
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('app_home');
        }
        $note_All = $note_repo->findAll();        
        return $this->render('home/index.html.twig', [
            'form'=>$form->createView(),
            'noteAll'=>$note_All
        ]);
        // $jsonContent = $serializer->serialize($note, 'json');
        // $response = new JsonResponse($jsonContent,200,[],true);
    }
    /**
     * @Route("/supprimer/{id}", name="app_delete", methods= "GET")
     */
    function delete(int $id,Request $request,NotesRepository $note_repo) {

        $note = $note_repo->findOneBy(['id'=>$id]);
        $this->em->remove($note);
        $this->em->flush();
        return $this->redirectToRoute('app_home');
        
    }
}
