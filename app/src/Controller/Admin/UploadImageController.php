<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UploadImageController extends AbstractController
{
    #[Route('/admin/upload-image', name: 'admin_upload_image', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        $fileName = uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('kernel.project_dir') . '/public/uploads',
            $fileName
        );

        return new JsonResponse(['location' => '/uploads/' . $fileName]);
    }
}