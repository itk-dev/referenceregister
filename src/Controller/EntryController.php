<?php

namespace App\Controller;

use App\EntryManager;
use App\Form\EntryAddFormType;
use App\Form\EntryLookUpFormType;
use App\Form\EntryRemoveFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EntryController extends AbstractController
{
    public function __construct(
        private readonly EntryManager $manager,
    ) {
    }

    #[Route('/add', name: 'app_entry_add', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function addEntry(Request $request): Response
    {
        $form = $this->createForm(EntryAddFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('id')->getData();
            if ($this->manager->addEntry($id)) {
                return $this->redirectToRoute('app_entry_add_success');
            }
        }

        return $this->render('entry/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/add/success', name: 'app_entry_add_success', methods: [Request::METHOD_GET])]
    public function addEntrySuccess(): Response
    {
        return $this->render('entry/add-success.html.twig');
    }

    #[Route('/look-up', name: 'app_entry_look_up')]
    public function lookUp(Request $request): Response
    {
        $form = $this->createForm(EntryLookUpFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('id')->getData();
            $entry = $this->manager->lookUpEntry($id);

            return null === $entry
                ? $this->render('entry/look-up-miss.html.twig')
                : $this->render('entry/look-up-hit.html.twig', [
                    'entry' => $entry,
                ]);
        }

        return $this->render('entry/look-up.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/remove', name: 'app_entry_remove')]
    public function removeEntry(Request $request): Response
    {
        $form = $this->createForm(EntryRemoveFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $id = $form->get('id')->getData();
            if ($this->manager->removeEntry($id)) {
                return $this->redirectToRoute('app_entry_remove_success');
            }
        }

        return $this->render('entry/remove.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/remove/success', name: 'app_entry_remove_success', methods: [Request::METHOD_GET])]
    public function removeEntrySuccess(): Response
    {
        return $this->render('entry/remove-success.html.twig');
    }
}
