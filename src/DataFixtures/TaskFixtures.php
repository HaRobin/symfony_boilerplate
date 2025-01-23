<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public const TASK_REFERENCE = 'task';

    public function load(ObjectManager $manager): void
    {
        $tasks = [
            [
                'name' => 'Task 1 created 6 days ago by user',
                'description' => 'Description 1',
                'author' => $this->getReference(UserFixtures::USER_REFERENCE . '_1'),
                'createdAt' => new \DateTimeImmutable('-6 days'),
            ],
            [
                'name' => 'Task 2 created 8 days ago by admin',
                'description' => 'Description 2',
                'author' => $this->getReference(UserFixtures::USER_REFERENCE . '_2'),
                'createdAt' => new \DateTimeImmutable('-8 days'),
            ],
            [
                'name' => 'Task 3 created 8 days ago by user',
                'description' => 'Description 3',
                'author' => $this->getReference(UserFixtures::USER_REFERENCE . '_1'),
                'createdAt' => new \DateTimeImmutable('-8 days'),
            ],
            [
                'name' => 'Task 4 created 6 days ago by admin',
                'description' => 'Description 4',
                'author' => $this->getReference(UserFixtures::USER_REFERENCE . '_2'),
                'createdAt' => new \DateTimeImmutable('-6 days'),
            ],
        ];

        foreach ($tasks as $key => $taskData) {
            $task = new Task();

            // Set task data
            $task->setName($taskData['name']);
            $task->setDescription($taskData['description']);
            $task->setAuthor($taskData['author']);
            $task->setCreatedAt($taskData['createdAt']);

            $manager->persist($task);
            $this->addReference($this::TASK_REFERENCE . '_' . $key + 1, $task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
