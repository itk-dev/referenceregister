<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\Permission;
use App\Entity\User;
use App\EntryManager;
use App\Exception\LogicException;
use App\Form\EntryAddFormType;
use App\Form\EntryLookUpFormType;
use App\Form\EntryRemoveFormType;
use App\LookupSlotHelper;
use App\Model\EntryFormDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function Symfony\Component\Translation\t;

final class EntryController extends AbstractController
{
    public function __construct(
        private readonly EntryManager $manager,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Route('/add', name: 'app_entry_add', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted(Permission::AddEntry->value)]
    public function addEntry(Request $request): Response
    {
        $entry = new EntryFormDto();
        $form = $this->createForm(EntryAddFormType::class, $entry);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (null === $entry->department) {
                    throw new LogicException('Entry department cannot be null');
                }

                $this->manager->addEntry($entry->identifier, $entry->department);

                return $this->redirectToRoute('app_entry_add_success');
            } catch (\Exception $e) {
                // @todo Log exception.
                $this->addFlash('danger', t('Error adding entry ({message})', ['message' => $e->getMessage()]));
            }
        }

        return $this->render('entry/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/add/success', name: 'app_entry_add_success', methods: [Request::METHOD_GET])]
    #[IsGranted(Permission::AddEntry->value)]
    public function addEntrySuccess(): Response
    {
        return $this->render('entry/add-success.html.twig');
    }

    #[Route('/look-up', name: 'app_entry_look_up', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted(Permission::LookUpEntry->value)]
    public function lookUp(Request $request, LookupSlotHelper $helper): Response
    {
        $entry = new EntryFormDto();
        $form = $this->createForm(EntryLookUpFormType::class, $entry);

        /** @var User $user */
        $user = $this->getUser();
        $lookupSlot = $helper->getLookupSlot($user);

        if ($lookupSlot->allowsLookup()) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entries = $this->manager->lookUp($entry->identifier);

                // Redirect to a GET request.
                $this->setLookUpResult($entries);

                return $this->redirectToRoute('app_entry_look_up_result');
            }
        }

        return $this->render('entry/look-up.html.twig', [
            'form' => $form,
            'lookup_slot' => $lookupSlot,
        ]);
    }

    #[Route('/look-up/result', name: 'app_entry_look_up_result', methods: [Request::METHOD_GET])]
    #[IsGranted(Permission::LookUpEntry->value)]
    public function lookUpResult(): Response
    {
        if (!$this->hasLookUpResult()) {
            // @todo Should we tell the user? How do we tell it?
            // $this->addFlash('warning', t('No look-up result found.'));

            return $this->redirectToRoute('app_entry_look_up');
        }
        $entries = $this->getLookUpResult();

        return
            0 === count($entries)
                ? $this->render('entry/look-up-miss.html.twig')
                : $this->render('entry/look-up-hit.html.twig', [
                    'entries' => $entries,
                ])
        ;
    }

    #[Route('/remove', name: 'app_entry_remove', methods: [Request::METHOD_GET, Request::METHOD_DELETE])]
    #[IsGranted(Permission::RemoveEntry->value)]
    public function removeEntry(Request $request): Response
    {
        $entry = new EntryFormDto();
        $form = $this->createForm(EntryRemoveFormType::class, $entry, options: [
            'method' => Request::METHOD_DELETE,
            'submit_class' => 'btn btn-danger',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $entry->department) {
                throw new LogicException('Entry department cannot be null');
            }

            if ($this->manager->removeEntry($entry->identifier, $entry->department)) {
                return $this->redirectToRoute('app_entry_remove_success');
            }
        }

        return $this->render('entry/remove.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/remove/success', name: 'app_entry_remove_success', methods: [Request::METHOD_GET])]
    #[IsGranted(Permission::RemoveEntry->value)]
    public function removeEntrySuccess(): Response
    {
        return $this->render('entry/remove-success.html.twig');
    }

    private const string SESSION_LOOK_UP_RESULT = 'session_look_up_result';

    /**
     * @param Entry[] $entries
     */
    private function setLookUpResult(array $entries): void
    {
        $session = $this->requestStack->getSession();
        $session->set(self::SESSION_LOOK_UP_RESULT, $entries);
    }

    private function hasLookUpResult(): bool
    {
        $session = $this->requestStack->getSession();

        return $session->has(self::SESSION_LOOK_UP_RESULT);
    }

    /**
     * @return Entry[] $entries
     */
    private function getLookUpResult(): array
    {
        $session = $this->requestStack->getSession();
        // @mago-ignore analysis:mixed-assignment
        $value = $session->get(self::SESSION_LOOK_UP_RESULT);
        if (!is_array($value) || !array_is_list($value)) {
            return [];
        }
        $session->remove(self::SESSION_LOOK_UP_RESULT);

        $entries = [];
        /** @var mixed $entry */
        foreach ($value as $entry) {
            if (!$entry instanceof Entry) {
                return [];
            }
        }

        // Rehydrate the entries.
        return $this->manager->loadEntries($entries);
    }
}
