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

    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $entry_repository = $em->getRepository("BlogBundle:Entry");
        $category_repo = $em->getRepository("BlogBundle:Category");
        $entries = $entry_repository->findAll();
        $categories = $category_repo->findAll();
        return $this->render("BlogBundle:Entry:index.html.twig", array(
            "entries"=> $entries,
            "categories" => $categories
        ));
    }

    public function addAction(Request $request){
        $entry = new Entry();
        $em = $this->getDoctrine()->getManager();
        $dpto = $em->getRepository("BlogBundle:Departamentos");
        $dptos = $dpto->findAll();
        $form = $this->createForm(EntryType::class, $entry);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $entry_repo = $em->getRepository("BlogBundle:Entry");
                $category_repo = $em->getRepository("BlogBundle:Category");

                $entry = new Entry();
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time().".".$ext;
                $file->move("uploads", $file_name);

                $entry->setImage($file_name);
                $category = $category_repo->find($form->get("category")->getData());

                $entry->setCategory($category);
                $user = $this->getUser();
                $entry->setUser($user);

                $em->persist($entry);
                $flush = $em->flush();
                $entry_repo->saveEntryTags($form->get("tags")->getData(),
                    $form->get("title")->getData(),
                    $category,
                    $user);

                if($flush == null){
                    $status = true;
                }else{
                    $status = false;
                }
            }else{
                $status = false;
            }
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("blog_homepage");
        }
        return $this->render("BlogBundle:Entry:add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $entry_repository = $em->getRepository("BlogBundle:Entry");
        $entry_tag_repository = $em->getRepository("BlogBundle:EntryTag");
        $entry = $entry_repository->find($id);
        $entryTag = $entry_tag_repository->findBy(array("entry" => $entry));
        foreach ($entryTag as $et){
            if(is_object($et)){
                $em->remove($et);
                $em->flush();
            }
        }
        if(is_object($entry)){
            $em->remove($entry);
            $em->flush();
        }
        return $this->redirectToRoute("blog_homepage");
    }
    public function editAction(Request $request, $id){
        $em = $this->getDoctrine()->getEntityManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $category_repo = $em->getRepository("BlogBundle:Category");
        $entry = $entry_repo->find($id);
        $tags = "";
        foreach ($entry->getEntryTag() as $entryTag){
            $tags .= $entryTag->getTag()->getName().", ";
        }
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                $entry->setTitle($form->get("title")->getData());
                $entry->setContent($form->get("content")->getData());
                $entry->setStatus($form->get("status")->getData());

                $file = $form["image"]->getData();
                $ext = $file->guessExtension();
                $file_name = time().".".$ext;
                $file->move("uploads", $file_name);

                $entry->setImage($file_name);
                $category = $category_repo->find($form->get("category")->getData());

                $entry->setCategory($category);
                $user = $this->getUser();
                $entry->setUser($user);

                $em->persist($entry);

                $flush = $em->flush();
                $entry_tag_repository = $em->getRepository("BlogBundle:EntryTag");
                $entryTag = $entry_tag_repository->findBy(array("entry" => $entry));
                foreach ($entryTag as $et){
                    if(is_object($et)){
                        $em->remove($et);
                        $em->flush();
                    }
                }
                $entry_repo->saveEntryTags($form->get("tags")->getData(),
                    $form->get("title")->getData(),
                    $category,
                    $user);
                if($flush == null){
                    $status = "La entrada se ha editado correctamente";
                }else{
                    $status = "La entrada no se ha editado";
                }
            }else{
                $status = "El formulario no es válido";
            }
            $this->session->getFlashBag()->add("status", $status);
            return  $this->redirectToRoute("blog_homepage");
        }
        return $this->render("BlogBundle:Entry:edit.html.twig",
            array("form" => $form->createView(),
                "entry" => $entry,
                "tags" => $tags));
    }
}
