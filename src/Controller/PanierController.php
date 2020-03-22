<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produits;
use App\Form\ProduitsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{


        
        
        /**
         * @Route("panier/{id}", name="ma_categorie")
         */
        public function categorie(Request $request, Categories $categorie=null) {

            if ($categorie != null) {
            $form = $this->createForm(CategoriesType::class, $categorie);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($categorie);
                $pdo->flush();
            }
            
            return $this->render('categorie/categorie.html.twig', [
                'categorie' => $categorie,
                'form' => $form->createView()
            ]);


        } else {
            return $this->redirectToRoute('categorie');
            
        } 
        
            
    }
}