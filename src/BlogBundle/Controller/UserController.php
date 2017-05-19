<?php

namespace BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
   public function loginAction(Request $request){
       $authenticationUtils = $this->get("security.authentication_utils");
       $error = $authenticationUtils->getLastAuthenticationError();
       $lastUsername = $authenticationUtils->getLastUsername();

       $user = new User();

       return $this->render("BlogBundle:User:login.html.twig", array(
           "error" => $error,
           "last_username" => $lastUsername
       ));
   }
}
