<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Form\NoteType;
use App\Repository\NotesRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;
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

        $note_All = $note_repo->findAll();        
        return $this->render('home/index.html.twig', [
            'form'=>$form->createView(),
            'noteAll'=>$note_All
        ]);
    }
    /**
     * @Route("/create", name="app_create")
     */
    public function create(Request $request,NotesRepository $note_repo)
    {
        $last_id = $note_repo->create_note();
       
        return $this->json($last_id,200);
    }
    /**
     * @Route("/modification", name="app_maj")
     */
    public function update_text(Request $request,NotesRepository $note_repo)
    {
        $type = $request->query->get('type');
        $text= $request->query->get('element');
        $id= $request->query->get('id');
        
        if($id) {
            if(($type == "input" && $text) || ($type == "textarea" && $text)) {
                $note_repo->update_note($id,$type,$text);
            }
        }
        return $this->json(['message' => 'insertion Faite'], 200);
    }
    /**
     * @Route("/modification/{id}", name="app_modification", methods= {"GET", "POST"})
     */
    function update_btn_modifier(int $id,Request $request,NotesRepository $note_repo,SerializerInterface $serializer) {

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
     /**
     * @Route("/supp", name="app_supprimer", methods= "GET")
     */
    function supprimer(Request $request,NotesRepository $note_repo) {

        $id = $request->query->get('id');
        $note = $note_repo->findOneBy(['id'=>$id]);
        $this->em->remove($note);
        $this->em->flush();
        return $this->json(['message' => 'Note supprimée avec succès'], 200);
        
    }
}
