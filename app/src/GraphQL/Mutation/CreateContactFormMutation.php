<?php

namespace App\GraphQL\Mutation;

use App\Entity\ContactForm;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

readonly class CreateContactFormMutation implements MutationInterface, AliasedInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * @param array $args
     * @return ContactForm
     */
    public function createContactForm(array $args): ContactForm
    {
        $contactForm = new ContactForm();
        $contactForm->setName($args['name']);
        $contactForm->setEmail($args['email']);
        $contactForm->setSubject($args['subject']);
        $contactForm->setMessage($args['message']);

        $this->em->persist($contactForm);
        $this->em->flush();

        return $contactForm;
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        return [
            'createContactForm' => 'create_contact_form'
        ];
    }
}