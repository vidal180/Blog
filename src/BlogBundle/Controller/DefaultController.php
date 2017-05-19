<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexOld()
    {
        /*
        $em = $this->getDoctrine()->getManager();
        $entry_repo = $em->getRepository("BlogBundle:Entry");
        $entries = $entry_repo->findAll();

        foreach ($entries as $entry){
            echo $entry->getTitle()."<br>";
            echo $entry->getCategory()->getName()."<br>";
            echo $entry->getUser()->getName()."<br>";

            $tags = $entry->getEntryTag();
            foreach ($tags as $tag){
                echo $tag->getTag()->getName().", ";
            }

            echo "<hr>";
        }
        */
        /*
        $em = $this->getDoctrine()->getManager();
        $category_repo = $em->getRepository("BlogBundle:Category");
        $categories = $category_repo->findAll();

        foreach ($categories as $category){
            echo $category->getName()."<br/>";

            $entries = $category->getEntries();
            var_dump($entries);
            die();
            foreach ($entries as $entry){
                echo $entry->getTitle().", ";
            }

            echo "<hr>";
        }
        */
        $em = $this->getDoctrine()->getManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        $tags = $tag_repo->findAll();

        foreach ($tags as $tag){
            echo $tag->getName()."<br/>";

            $entryTag = $tag->getEntryTag();
            var_dump($entryTag);
            die();
            foreach ($entryTag as $entry){
                echo $entry->getEntry()->getTitle().", ";
            }

            echo "<hr>";
        }
        die();
        return $this->render('BlogBundle:Default:index.html.twig');
    }

    public function indexAction(){

        return $this->render('BlogBundle:Default:index.html.twig');
    }
}
