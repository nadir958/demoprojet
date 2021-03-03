<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }
    
    /**
     * @Route("/command", name="command")
     */
    public function index(): Response
    {
        return $this->render('command/index.html.twig');
    }

    /**
     * @Route("/command/add", name="command_add")
     */
    public function add(Request $request)
    {
        $commandes = new Commandes();
        $form = $this->createForm(CommandeType::class, $commandes);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $commandes->setUser($this->getUser());
            $this->entityManager->persist($commandes);
            $this->entityManager->flush();
            return $this->redirectToRoute('command');
        }

        return $this->render('command/add.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/command/edit/{id}", name="command_edit")
     */
    public function edit(Request $request,$id)
    {
        $commade = $this->entityManager->getRepository(Commandes::class)->findOneByid($id);

        if (!$commade || $commade->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address');
        }
        $form = $this->createForm(CommandeType::class, $commade);
        $form->handleRequest($request);

        if  ($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();
            return $this->redirectToRoute('command');
        }
        return $this->render('command/add.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/command/delete/{id}", name="command_delete")
     */
    public function delete($id)
    {
        $commande = $this->entityManager->getRepository(commandes::class)->findOneByid($id);

        if ($commande && $commande->getUser() == $this->getUser()){
            $this->entityManager->remove($commande);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('command');
    }
}
