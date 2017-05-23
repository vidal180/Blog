<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Entry;
use BlogBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use BlogBundle\Form\EntryType;

class EntryController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function addAction(Request $request){
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                /*$em = $this->getDoctrine()->getManager();
                $category = new Category();
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em->persist($category);
                $flush = $em->flush();
                if($flush == null){
                    $status = true;
                }else{
                    $status = false;
                }*/
            }else{
                $status = false;
            }
            //$this->session->getFlashBag()->add("status", $status);
            //return $this->redirectToRoute("blog_index_category");
        }
        return $this->render("BlogBundle:Entry:add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $category_repository = $em->getRepository("BlogBundle:Category");
        $category = $category_repository->find($id);
        if(count($category->getEntries()) == 0){
            $em->remove($category);
            $em->flush();
        }
        return $this->redirectToRoute("blog_index_category");
    }

    public function editAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $category_repository = $em->getRepository("BlogBundle:Category");
        $category = $category_repository->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $category->setName($form->get("name")->getData());
                $category->setDescription($form->get("description")->getData());
                $em->persist($category);
                $flush = $em->flush();
                if($flush == null){
                    $status = true;
                }else{
                    $status = false;
                }
            }else{
                $status = false;
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_index_category");
        }
        return $this->render("BlogBundle:Category:edit.html.twig", array(
            "form" => $form->createView()
        ));
    }
}