<?php

namespace BlogBundle\Repository;

use BlogBundle\Entity\Tag;
use BlogBundle\Entity\EntryTag;

class EntryRepository extends \Doctrine\ORM\EntityRepository {
    public function saveEntryTags($tags=null, $title=null, $category=null, $user=null, $entry=null){
        $em = $this->getEntityManager();
        $tag_repo = $em->getRepository("BlogBundle:Tag");
        if($entry == null){
            $entry = $this->findOneBy(
                array("title"=>$title,
                    "category"=>$category,
                    "user"=>$user)
            );
        }else{

        }
        $tags = explode(",", $tags);
        foreach ($tags as $tag){
            $isset_tag = $tag_repo->findOneBy(array(
               "name"=>$tag
            ));
            if(count($isset_tag) == 0){
                $tag_obj = new Tag();
                $tag_obj->setName($tag);
                $tag_obj->setDescription($tag);
                $em->persist($tag_obj);
                $em->flush();
            }

            $tag = $tag_repo->findOneBy(array(
                "name"=>$tag
            ));

            $entryTag = new EntryTag();
            $entryTag->setEntry($entry);
            $entryTag->setTag($tag);
            $em->persist($entryTag);
        }
        $flush = $em->flush();

        return $flush;
    }
}