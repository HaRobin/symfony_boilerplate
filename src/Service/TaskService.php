<?php

namespace App\Service;

use App\Entity\Task;

class TaskService
{
    public function canEdit(Task $task): bool
    {
        $createdAt = $task->getCreatedAt();
        $now = new \DateTimeImmutable();
        $diff = $now->diff($createdAt);

        return $diff->days <= 7;
    }
}
