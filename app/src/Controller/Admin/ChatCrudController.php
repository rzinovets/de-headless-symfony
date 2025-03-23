<?php

namespace App\Controller\Admin;

use App\Entity\Chat;
use App\Entity\Message;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class ChatCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Chat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Chat')
            ->setEntityLabelInPlural('Chats')
            ->setPageTitle('index', 'Chats Management')
            ->setPageTitle('detail', 'Chat Details')
            ->setSearchFields(['id', 'user.username', 'admin.username'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user', 'User'),
            AssociationField::new('admin', 'Admin'),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewMessages = Action::new('viewMessages', 'View Messages', 'fa fa-comments')
            ->linkToUrl(function (Chat $chat) {
                return $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('viewMessages')
                    ->setEntityId($chat->getId())
                    ->generateUrl();
            })
            ->setCssClass('btn btn-primary');

        return $actions
            ->add(Crud::PAGE_INDEX, $viewMessages)
            ->add(Crud::PAGE_DETAIL, $viewMessages);
    }

    public function viewMessages(Request $request, EntityManagerInterface $em): Response
    {
        $entityId = $request->query->get('entityId');
        $chat = $em->getRepository(Chat::class)->find($entityId);

        if (!$chat) {
            throw $this->createNotFoundException('Chat not found');
        }

        $messages = $em->getRepository(Message::class)->findBy(['chat' => $chat], ['createdAt' => 'ASC']);

        return $this->render('admin/chat_messages.html.twig', [
            'chat' => $chat,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, EntityManagerInterface $em): Response
    {
        $chatId = $request->request->get('chatId');
        $chat = $em->getRepository(Chat::class)->find($chatId);

        if (!$chat) {
            throw $this->createNotFoundException('Chat not found');
        }

        $messageContent = $request->request->get('message');

        if (empty($messageContent)) {
            $this->addFlash('error', 'Message cannot be empty.');
            return $this->redirectToRoute('admin', [
                'crudAction' => 'viewMessages',
                'crudControllerFqcn' => self::class,
                'entityId' => $chatId,
            ]);
        }

        $message = new Message();
        $message->setChat($chat);
        $message->setSender($this->getUser());
        $message->setContent($messageContent);

        $em->persist($message);
        $em->flush();

        $this->addFlash('success', 'Message sent successfully.');
        return $this->redirectToRoute('admin', [
            'crudAction' => 'viewMessages',
            'crudControllerFqcn' => self::class,
            'entityId' => $chatId,
        ]);
    }
}