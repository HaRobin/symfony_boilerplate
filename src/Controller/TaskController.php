<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
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
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('app_task_view', ['id' => $task->getId()]);
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/task/{id}/edit', name: 'app_task_edit')]
    public function edit(TaskRepository $taskRepository, Request $request, EntityManagerInterface $entityManager, TaskService $taskService): Response
    {
        $task = $taskRepository->find($request->get('id'));

        if (!$task) {
            $this->addFlash('error', 'Tâche introuvable.');
            return $this->redirectToRoute('app_task');
        }

        if (!$this->isGranted(TaskVoter::EDIT, $task)) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour modifier cette tache.');
            return $this->redirectToRoute('app_task');
        }

        if (!$taskService->canEdit($task)) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cette tâche 7 jours après sa création.');
            return $this->redirectToRoute('app_task');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('app_task_view', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/task/{id}/view', name: 'app_task_view')]
    public function view(TaskRepository $taskRepository, Request $request): Response
    {
        $task = $taskRepository->find($request->get('id'));

        if (!$task) {
            $this->addFlash('error', 'Tâche introuvable.');
            return $this->redirectToRoute('app_task');
        }

        if (!$this->isGranted(TaskVoter::VIEW, $task)) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour afficher cette tache.');
            return $this->redirectToRoute('app_task');
        }

        return $this->render('task/view.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/task/{id}/delete', name: 'app_task_delete')]
    public function delete(TaskRepository $taskRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $task = $taskRepository->find($request->get('id'));

        if (!$task) {
            $this->addFlash('error', 'Tâche introuvable.');
            return $this->redirectToRoute('app_task');
        }

        if (!$this->isGranted(TaskVoter::DELETE, $task)) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour supprimer cette tâche.');
        } else {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche supprimée avec succès.');
        }

        return $this->redirectToRoute('app_task');
    }
}
