<?php

namespace App\Security\Voter;

use App\Entity\Enum\UserRoles;
use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const VIEW = 'TASK_VIEW';
    public const DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT

                if ($user->getRoles() === [UserRoles::Admin]) {
                    return true;
                }

                if ($task->getAuthor() === $user) {
                    return true;
                }

                break;

            case self::VIEW:
                // logic to determine if the user can VIEW
                
                if ($user->getRoles() === [UserRoles::Admin]) {
                    return true;
                }

                if ($task->getAuthor() === $user) {
                    return true;
                }
                
                break;

            case self::DELETE:
                // logic to determine if the user can DELETE
                
                if ($user->getRoles() === [UserRoles::Admin]) {
                    return true;
                }

                break;
        }

        return false;
    }
}
