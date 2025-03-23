<?php

namespace App\GraphQL\Mutation;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class CreateAccountMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function createAccount(array $input): User
    {
        $user = new User();
        $user->setEmail($input['email']);

        $user->setUsername($input['username'] ?? null)
            ->setFullName($input['fullName'] ?? null)
            ->setDateOfBirth($input['dateOfBirth'] ?? null)
            ->setGender($input['gender'] ?? null)
            ->setPhoneNumber($input['phoneNumber'] ?? null)
            ->setStreetAddressLine1($input['streetAddressLine1'] ?? null)
            ->setStreetAddressLine2($input['streetAddressLine2'] ?? null)
            ->setCity($input['city'] ?? null)
            ->setPostCode($input['postCode'] ?? null)
            ->setDisabilities($input['disabilities'] ?? null);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $input['password'])
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public static function getAliases(): array
    {
        return [
            'createAccount' => 'create_account',
        ];
    }
}
