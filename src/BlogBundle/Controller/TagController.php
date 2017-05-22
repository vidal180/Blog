<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Tag;
use BlogBundle\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TagController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $tags_repository = $em->getRepository("BlogBundle:Tag");
        $tags = $tags_repository->findAll();
        return $this->render("BlogBundle:Tag:index.html.twig", array(
            "tags" => $tags
        ));
    }

    public function addAction(Request $request){

        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $tag = new Tag();
                $tag->setName($form->get("name")->getData());
                $tag->setDescription($form->get("description")->getData());
                $em->persist($tag);
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
            return $this->redirectToRoute("blog_index_tag");
        }
        return $this->render("BlogBundle:Tag:add.html.twig", array(
            "form" => $form->createView()
        ));
    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $tag_repository = $em->getRepository("BlogBundle:Tag");
        $tag = $tag_repository->find($id);
        if(count($tag->getEntryTag()) == 0){
            $em->remove($tag);
            $em->flush();
        }
        return $this->redirectToRoute("blog_index_tag");
    }
}
