<?php

namespace App\Controller;

use App\Form\BookType;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/book/add', name: 'app_add_book')]
    public function add(ManagerRegistry $doctrine, Request $req,BookRepository $repo): Response
    {
        $book=new Book();
        $f=$this->createForm(BookType::class,$book);
        $f->add("Save", SubmitType::class);
        $f->handleRequest($req);
        $ref = $book->getRef();
        if($repo->findBy(["ref"=>$ref])){
            return $this->renderForm("book/error.html.twig", ['errorMessage' => "ERROR : The ref is already exist "]);
         }
         if ($f->isSubmitted()){
        $em=$doctrine->getManager();
        $book->setPublished(true);
        $author=$book->getAuthor();
        $nbancien = $author->getNbBooks();
        $author->setNbBooks($nbancien+1);
        $em->persist($book);
        $em->flush();
        //return new Response("Ajout ok");
        return $this->redirectToRoute("app_book_all");
         }

        return $this->renderForm('book/add.html.twig', [
            'myForm' => $f,
        ]);
    }

    #[Route('/book/all', name: 'app_book_all')]
    public function getAll(ManagerRegistry $doctrine): Response
    {
        $repo=$doctrine->getRepository(Book::class);
        $books=$repo->findAll();
        return $this->render('book/list.html.twig', ['list'=> $books]);
    }
    #[Route('/book/all2', name: 'app_book_all2')]
    public function getAll2(BookRepository $repo): Response
    {
        $books=$repo->findBy(['published'=>1]);
        $nb1=count($books);
        $nb2=count($repo->findBy(['published'=>0]));
        return $this->render('book/list.html.twig', ['list'=> $books,'nb1'=>$nb1,'nb2'=>$nb2]);


    }

    #[Route('/book/edit/{ref}', name: 'app_edit_book')]
    public function edit($ref, ManagerRegistry $doctrine, Request $req): Response
    {
        $book=$doctrine->getRepository(Book::class)->find($ref);
        $f=$this->createForm(BookType::class,$book);
        $f->add('published');
        $f->add("Save", SubmitType::class);
        $f->handleRequest($req);
         if ($f->isSubmitted()){
        $em=$doctrine->getManager();
        $em->persist($book);
        $em->flush();
        //return new Response("Ajout ok");
        return $this->redirectToRoute("app_book_all");
         }
        return $this->renderForm('book/add.html.twig', [
            'myForm' => $f,
        ]);
    }

    #[Route('/book/all/{title}/{name}', name: 'app_book_all_title')]
    public function getAllBuTitle(Request $req,BookRepository $repo): Response
    {
        $t=$req->get('title');
        $name=$req->get('name');
        $books=$repo->findBookByTitle($t,$name);
        return $this->render('book/list.html.twig', ['list'=> $books]);


    }
}
