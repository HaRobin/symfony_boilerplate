<?php

namespace App\Command;

use App\Service\TaskFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:task',
    description: 'Gestion des tâches via la console',
)]
class TaskCommand extends Command
{
    private $taskFileService;

    public function __construct(TaskFileService $taskFileService)
    {
        $this->taskFileService = $taskFileService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::REQUIRED, 'Action à effectuer (create, update, list, get, delete)')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $action = $input->getArgument('action');

        switch ($action) {
            case 'create':
                $title = $io->ask('Entrez le titre de la tâche');
                $description = $io->ask('Entrez la description de la tâche');
                $this->taskFileService->createTask($title, $description);
                break;

            case 'update':
                $taskId = $io->ask('Entrez l\'ID de la tâche à mettre à jour');
                $newTitle = $io->ask('Entrez le nouveau titre de la tâche');
                $newDescription = $io->ask('Entrez la nouvelle description');

                $this->taskFileService->updateTask($taskId, $newTitle, $newDescription);
                $io->success('Tâche mise à jour avec succès.');
                break;

            case 'list':
                $tasks = $this->taskFileService->listTasks();
                if (empty($tasks)) {
                    $io->warning('Aucune tâche trouvée.');
                } else {
                    $formattedTasks = array_map(function ($task) {
                        $task['createdAt'] = $task['createdAt']->format('d/m/Y H:i:s');
                        return $task;
                    }, $tasks);
                    $io->table(['ID', 'Titre', 'Description', 'Crée le'], $formattedTasks);
                }
                break;

            case 'get':
                $taskId = $io->ask('Entrez l\'ID de la tâche à afficher');
                $task = $this->taskFileService->getTask($taskId);
                if (!$task) {
                    $io->error('Tâche non trouvée.');
                } else {
                    $io->section('Détails de la tâche');
                    $io->text([
                        'ID: ' . $task['id'],
                        'Titre: ' . $task['title'],
                        'Description: ' . $task['description'],
                        'Crée le: ' . $task['createdAt']->format('d/m/Y H:i:s'),
                    ]);
                }
                break;

            case 'delete':
                $taskId = $io->ask('Entrez l\'ID de la tâche à supprimer');
                if ($io->confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?', false)) {
                    $this->taskFileService->deleteTask($taskId);
                    $io->success('Tâche supprimée avec succès.');
                } else {
                    $io->note('Suppression annulée.');
                }
                break;

            default:
                $io->error('Action invalide. Utilisez "create", "update", "list", "get" ou "delete".');
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
