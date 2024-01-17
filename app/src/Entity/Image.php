<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Image
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    const SERVER_PATH_TO_IMAGE_FOLDER = 'app/public/images';

    private ?UploadedFile $file = null;
    protected ?string $filename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFile(?UploadedFile $file = null): void
    {
        $this->file = $file;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function upload(): ?string
    {
        if (null === $this->getFile()) {
            return null;
        }

        $filepath = $this->getFile()->move(
            self::SERVER_PATH_TO_IMAGE_FOLDER,
            $this->getFile()->getClientOriginalName()
        );

        $this->filename = $this->getFile()->getClientOriginalName();

        $this->setFile(null);

        return $filepath;
    }

    public function lifecycleFileUpload(): void
    {
        $this->upload();
    }
}