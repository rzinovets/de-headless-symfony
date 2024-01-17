<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityController extends AbstractController
{
    const SECURITY_FORM = 'security/login.html.twig';

    use TargetPathTrait;

    #[Route('/login', name: 'security_login')]
    public function login(Request $request, AuthenticationUtils $helper): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        $this->saveTargetPath($request->getSession(), 'main', $this->generateUrl('admin'));

        return $this->render(self::SECURITY_FORM, [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    #[Route('/logout', name: 'security_logout')]
    public function logout(): void {
        throw new ('This should never be reached!');
    }
}
