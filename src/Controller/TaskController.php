<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(TaskRepository $taskRepository): Response
    {

        $tasks = $taskRepository->findAll();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/create', name: 'app_task_create')]
    public function create(): Response
    {
        return $this->render('task/create.html.twig');
    }

    #[Route('/task/edit', name: 'app_task_edit')]
    public function edit(): Response
    {
        return $this->render('task/edit.html.twig');
    }

    #[Route('/task/view', name: 'app_task_view')]
    public function view(TaskRepository $taskRepository, Request $request): Response
    {
        $task = $taskRepository->find($request->get('id'));

        return $this->render('task/view.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/task/delete', name: 'app_task_delete')]
    public function delete(TaskRepository $taskRepository, Request $request): Response
    {
        return $this->render('task/delete.html.twig');
    }
}
