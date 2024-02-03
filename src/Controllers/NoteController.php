<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\NotFoundException;


class NoteController extends AbstractController
{

    public function createAction(): void
    {
        if ($this->request->hasPost()) {

            $noteData = [
                'title' => $this->request->postParam('title'),
                'description' => $this->request->postParam('description')
            ];

            $this->database->createNote($noteData);
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
        $sortBy = $this->request->getParam('sortby', 'title');
        $sortOrder = $this->request->getParam('sortorder', 'desc');

        $this->view->render('list', [
            'sort' => ['by' => $sortBy, 'order' => $sortOrder],
            'notes' => $this->database->getNotes($sortBy, $sortOrder),
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
            $this->database->editNote($noteId, $noteData);
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
            $this->database->deleteNote($id);
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

        try {
            $note = $this->database->getNote($noteId);
        } catch (NotFoundException $e) {
            $this->redirect('/', ['error' => 'noteNotFound']);
        }

        return $note;
    }
}
