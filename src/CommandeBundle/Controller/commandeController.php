<?php

namespace CommandeBundle\Controller;

use CommandeBundle\Entity\Article;
use CommandeBundle\Entity\Commande;
use CommandeBundle\Entity\Lignecommande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Fournisseur;

class commandeController extends AbstractController
{


    public function newCommandeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(Commande::class)->findBy(array( 'etat'=>'en cours'));

        return $this->render('@Commande/Article/newCommande.html.twig',
            ['commandes'=> $commande]
        );


    }

    public function addArticleAction(Request $request)
    {

        $refArticle = $request->request->get('refArticle');
        $qte = $request->request->get('qte');


        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($refArticle);

        //$dql=" SELECT p.idCommande FROM CommandeBundle:Commande p ORDER BY idCommande DSC";
        //$query= $em->createQueryBuilder($dql);
        //$bugs = $query->getFirstResult();

        //$last= $bugs+1;
        $commandeEnCours = $em->getRepository(Commande::class)->findOneBy(array('etat' => 'en cours'));
        if ($commandeEnCours == false) {
            $commande = new Commande();
            // $commande->setIdCommande($last);

            $commande->setEtat('en cours');
            $commande->setType('entrée');
            $em->persist($commande);

            $ligneCommande = new Lignecommande();
            $ligneCommande->setRefArticle($article);
            $ligneCommande->setIdCommande($commande);
            $ligneCommande->setQte(intval($qte));
            //dump($ligneCommande);die;
            $em->persist($ligneCommande);

            $em->flush();
        }
        else {
            $ligneCommandeExite = $em->getRepository(Lignecommande::class)->findOneBy(array('idCommande' => $commandeEnCours, 'refArticle' => $article));
            if ($ligneCommandeExite) {
                $ligneCommandeExite->setQte($qte);

            } else {
                $ligneCommande = new LigneCommande();
                $ligneCommande->setrefArticle($article);
                $ligneCommande->setIdCommande($commandeEnCours);
                $ligneCommande->setQte($qte);
                $em->persist($ligneCommande);
            }
            $em->flush();
        }
       return $this->redirectToRoute('show_article');


    }


    public function supprimerLigneAction(Request $request)
    {
        $id=$request->get('id');
        $em=$this->getDoctrine()->getManager();
        $LigneCommande=$em->getRepository(Lignecommande::class)->find($id);
        $em->remove($LigneCommande);
        $em->flush();
            $em->flush();


        return $this->redirectToRoute('new_commande');
    }

    public function updateLigneAction(Request $request, Commande $commande)
    {
            $em = $this->getDoctrine()->getManager();
            $lignes = $commande->getLignes();
            foreach ($lignes as $ligne){
                $ligne->setQte($request->request->get($ligne->getId()));
                $em->flush();
            }


        return $this->redirectToRoute('new_commande');
    }

    public function AjouterAction(Commande $commande, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($commande->getEtat() == "en cours") {
           // $total = $request->request->get($commande->getMontant());


            $commande->setMontant($request->request->get("montant"));
            $commande->setNumCommande($request->request->get("numCommande"));

            $commande->setDateLivraison(new \DateTime($request->request->get("dateLivraison")) );
            $commande->setDateCommande(new \DateTime($request->request->get("dateCommande")));



             $commande->setEtat('en attente');
            $em->flush();


        }

        return $this->redirectToRoute('show_article');
    }









}