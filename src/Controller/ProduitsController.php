<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class ProduitsController extends AbstractController
{
    

    /**
     * @Route("/produits", name="produit")
     */
    public function produit(Request $request, TranslatorInterface $translator)
    {
        $pdo = $this->getDoctrine()->getManager();

        //$produits = $pdo->getRepository(Produits::class)->findAll();


        $produit = new Produits();
        
        $form = $this->createForm(ProduitsType::class, $produit);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            
            $fichier = $form->get('image')->getData();
            if($fichier){
                $nomFichier = uniqid() .'.'. $fichier->guessExtension();  //uniqid() genere un id unique en fction de la date et l'heure || guessetension : recupe lextension du file

            

                try {
                    $fichier->move(
                        $this->getParameter('brochures_directory'),

                        $nomFichier
                    );
                } catch(FileException $e) {
                    $this->addFlash("sanger", "Le fichier n'a pas pu etre uploade",
                $translator->trans('file.error'));
                    return $this->redirectToRoute('produit');
                }

                $produit->setImage($nomFichier);
            
          } 
       
            $pdo->persist($produit);
            $pdo->flush();

            $this->addFlash("sucess", "Produit ajouter");


        }
                    $produits = $pdo->getRepository(Produits::class)->findAll();

            return $this->render('produits/index.html.twig', [  
                'controller' => "sur l'ensemble de nos produits ",
                'produits' => $produits,
                'form' => $form->createView()

            ]);

    }
    
               /**
         * @Route("produit/{id}", name="produit_panier")
         */
    public function produits(Request $request, Produits $produits){
        if($produits != null){
            
            $form = $this->createForm(ProduitsType::class, $produits);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $$form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($produits);
                $pdo->flush();

                $this->addFlash("sucess", "Produit mis à jour");

            }

            return $this->render('panier/panier.html.twig', [ // on renvoit à la vue
                'produit' => $produits,
                'form_produit_new' => $form->createView()          

                ]);
        }
        else{
            ///produit nexiste pas 
            $this->addFlash("danger", "Produit introuvable");
    
            return $this->redirectToRoute('produit');
            }
    }
    /**
     *@Route("/produit/delete/{id}", name="produit_categorie")
     */
    public function delete(Produits $produits=null){
        if($produits != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($produits);
            $pdo->flush();

            $this->addFlash("sucess", "Produit Supprimé");
        } else {
            $this->addFlash("error", "Produit introuvable");
        }
        return $this->redirectToRoute('produit');
    }
}
