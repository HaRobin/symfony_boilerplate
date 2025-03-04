<?php

namespace App\Service;

use App\Entity\Task;
use DateTime;
use DateTimeZone;
use Symfony\Component\Filesystem\Filesystem;

class TaskFileService
{

    private $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function createTask(string $title, string $description): void
    {
        $id = uniqid();
        $content = 'Titre : ' . $title . PHP_EOL . 'Description : ' . $description;
        $this->filesystem->dumpFile(
            'public/tasks/' . $id . '.txt',
            $content
        );
    }

    public function updateTask(string $id, string $title, string $description): void
    {
        if ($this->filesystem->exists('public/tasks/' . $id . '.txt')) {
            $content = 'Titre : ' . $title . PHP_EOL . 'Description : ' . $description;
            $this->filesystem->dumpFile(
                'public/tasks/' . $id . '.txt',
                $content
            );
        } else {
            throw new \Exception('Task file not found');
        }
    }

    public function  listTasks(): array
    {
        $tasks = [];
        $files = scandir(directory: 'public/tasks');
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $content = file_get_contents('public/tasks/' . $file);
            $tasks[] = [
                'id' => pathinfo($file, PATHINFO_FILENAME),
                'title' => trim(explode(':', explode(PHP_EOL, $content)[0])[1]),
                'description' => trim(explode(':', explode(PHP_EOL, $content)[1])[1]),
                'createdAt' => (new DateTime('@' . filectime('public/tasks/' . $file)))->setTimezone(new DateTimeZone('Europe/Paris')),
            ];
        }
        return $tasks;
    }

    public function getTask(string $id): array
    {
        $content = $this->filesystem->exists('public/tasks/' . $id . '.txt') ? file_get_contents('public/tasks/' . $id . '.txt') : null;
        $task = [
            'id' => $id,
            'title' => trim(explode(':', explode(PHP_EOL, $content)[0])[1]),
            'description' => trim(explode(':', explode(PHP_EOL, $content)[1])[1]),
            'createdAt' => (new DateTime('@' . filectime('public/tasks/' . $id . '.txt')))->setTimezone(new DateTimeZone('Europe/Paris')),
        ];
        return $task;
    }

    public function  deleteTask(string $id): void
    {
        if ($this->filesystem->exists('public/tasks/' . $id . '.txt')) {
            $this->filesystem->remove('public/tasks/' . $id . '.txt');
        } else {
            throw new \Exception('Task file not found');
        }
    }
}
