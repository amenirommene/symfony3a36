<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', 
        [
            'controller_n' => 'abdessalem',
            'variable2' => '3a36'
        ]);
    }
    #[Route('/show/{name}', name: 'app_show_author')]
    public function showauthor($name): Response
    {
        return $this->render('author/show.html.twig', 
        [
            'mavariable' => $name
        ]);
    }
    #[Route('/goto', name: 'app_goto')]
    public function goToIndex(): Response
    {
        return $this->redirectToRoute('app_home');
    }

    #[Route('/list', name: 'app_list')]
    public function list(): Response
    {
        $authors = array( 
            array('id' => 1, 'picture' => 'images/victor-hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100), 
            array('id' => 2, 'picture' => 'images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' => ' william.shakespeare@gmail.com', 'nb_books' => 200 ), 
            array('id' => 3, 'picture' => 'images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300) );
        return $this->render('author/list.html.twig', ['list'=> $authors]);
    }

    #[Route('/authordetails/{id}', name: 'app_author_details')]
    public function authordetails($id): Response
    {
        return $this->render('author/showauthor.html.twig');
    }
    #[Route('/author/add', name: 'app_author_add')]
    public function addAuthor(ManagerRegistry $doctrine): Response
    {
        //traitement
        //1- objet à ajouter
        $author1=new Author();
        $author1->setUsername("Ahmed");
        $author1->setEmail("Ahmed@esprit.tn");
        $author2=new Author();
        $author2->setUsername("Ahmed2");
        $author2->setEmail("Ahmed2@esprit.tn");
        //2- créer le entity manager
        $em=$doctrine->getManager();
        //3- préparer la requête d'ajout
        $em->persist($author1);
        $em->persist($author2);
        //4- exécuter la requête
        $em->flush();

        return new Response("Success");
    }

    #[Route('/author/all', name: 'app_author_all')]
    public function getAll(ManagerRegistry $doctrine): Response
    {
        $repo=$doctrine->getRepository(Author::class);
        $authors=$repo->findAll();
        return $this->render('author/list.html.twig', ['list'=> $authors]);


    }

    #[Route('/author/addF', name: 'app_author_addF')]
    public function addAuthorF(Request $req, ManagerRegistry $doctrine): Response
    {
        $a=new Author(); //notre objet est vide
        
        //instancier le formulaire
        $form=$this->createForm(AuthorType::class,$a);
        //récupérer les données
        /* $a->setUsername($req->get('username'));
        $a->setEmail($req->get('email'));*/
        $form->handleRequest($req);
        //si on a cliqué sur le bouton submit
        if ($form->isSubmitted()){
        $em=$doctrine->getManager();
        //3- préparer la requête d'ajout
        $em->persist($a);
        //4- exécuter la requête
        $em->flush();
        return $this->redirectToRoute("app_author_all");
        }
        //renvoyer le form vers la vue
      //  return $this->render("author/add.html.twig", ["myForm"=>$form->createView()]);
        return $this->renderForm("author/add.html.twig", ["myForm"=>$form]);
    }
}
