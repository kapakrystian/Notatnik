<?php

declare(strict_types=1);

namespace App\Controllers;


class NoteController extends AbstractController
{

    private const PAGE_SIZE = 10;

    public function createAction(): void
    {
        if ($this->request->hasPost()) {

            $noteData = [
                'title' => $this->request->postParam('title'),
                'description' => $this->request->postParam('description')
            ];

            $this->noteModel->create($noteData);
            $this->redirect('/', ['before' => 'created']);
        }
        $this->view->render('create');
    }


    public function showAction(): void
    {
        $this->view->render('show', ['note' => $this->getNote()]);
    }


    public function listAction(): void
    {
        $phrase = $this->request->getParam('phrase');
        $date = $this->request->getParam('date');
        $pageNumber = (int) $this->request->getParam('page', 1);
        $pageSize = (int) $this->request->getParam('pagesize', self::PAGE_SIZE);

        $sortBy = $this->request->getParam('sortby', 'title');
        $sortOrder = $this->request->getParam('sortorder', 'desc');

        if (!in_array($pageSize, [1, 5, 10, 25])) {
            $pageSize = self::PAGE_SIZE;
        }

        $note = $this->noteModel->search($sortBy, $sortOrder, $pageNumber, $pageSize, $phrase, $date);
        $notes = $this->noteModel->searchCount($phrase, $date);

        $this->view->render('list', [
            'page' => [
                'number' => $pageNumber,
                'size' => $pageSize,
                'pages' => (int)ceil($notes / $pageSize)
            ],
            'sort' => ['by' => $sortBy, 'order' => $sortOrder],
            'notes' => $note,
            'phrase' => $phrase,
            'date' => $date,
            'before' => $this->request->getParam('before'),
            'error' => $this->request->getParam('error')
        ]);
    }


    public function editAction(): void
    {
        /*--------------------------------------------------------
        Po przekazaniu nowych danych do istniejącej notatki.
        Przekazanie nowych danych do istniejącej notatki w bazie.
        --------------------------------------------------------*/
        if ($this->request->isPost()) {
            $noteId = (int) $this->request->postParam('id');
            $noteData = [
                'title' => $this->request->postParam('title'),
                'description' => $this->request->postParam('description')
            ];
            $this->noteModel->edit($noteId, $noteData);
            $this->redirect('/', ['before' => 'edited']);
        }

        /*-------------------------------------------
        Po wejściu w formularz edycji.
        Pobranie danych o istniejącej notatce z bazy.
        --------------------------------------------*/

        $this->view->render('edit', ['note' => $this->getNote()]);
    }


    public function deleteAction(): void
    {
        if ($this->request->isPost()) {
            $id = (int) $this->request->postParam('id');
            $this->noteModel->delete($id);
            $this->redirect('/', ['before' => 'deleted']);
        }

        $this->view->render('delete', ['note' => $this->getNote()]);
    }

    private function getNote(): array
    {
        $noteId = (int) $this->request->getParam('id');
        if (!$noteId) {
            $this->redirect('/', ['error' => 'missingNoteId']);
            exit;
        }

        return $this->noteModel->get($noteId);
    }
}
