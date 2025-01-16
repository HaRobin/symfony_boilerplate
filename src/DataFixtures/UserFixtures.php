<?php

namespace App\DataFixtures;

use App\Entity\Enum\UserRoles;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    
    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'user@example.com',
                'password' => 'password123',
                'role' => [UserRoles::User],
            ],
            [
                'email' => 'admin@example.com',
                'firstName' => 'Admin',
                'lastName' => 'User',
                'password' => 'adminpassword',
                'role' => [UserRoles::Admin],
            ],
        ];

        foreach ($users as $key => $userData) {
            $user = new User();

            // Set user data
            $user->setEmail($userData['email']);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $userData['password']
                )
            );
            // $user->setPassword($pass);
            $user->setRoles($userData['role']);

            $manager->persist($user);
            $this->addReference($this::USER_REFERENCE . '_' . $key + 1, $user);
        }

        $manager->flush();
    }
}
