<?php

namespace App\Entity;

use App\Repository\AvatarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: AvatarRepository::class)]
class Avatar implements \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Vich\UploadableField(mapping="avatar", fileNameProperty="avatar", size="avatarSize")
     * @var File
     */
    private $avatarFile;

    #[ORM\Column(name: 'avatar', type: 'string', length: 255, nullable: true)]
    private $avatar;

    #[ORM\Column(name: 'avatar_size',type: 'integer', nullable: true)]
    private $avatarSize;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $utilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatarSize(): ?int
    {
        return $this->avatarSize;
    }

    /**
     * @return File|null
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * @param File|null $avatar
     */
    public function setAvatarFile(?File $avatar = null): void
    {
        $this->avatarFile = $avatar;

        if(null !== $avatar){
            $this->updated_at = new \DateTime();
        }
    }

    public function setAvatarSize(?int $avatarSize): self
    {
        $this->avatarSize = $avatarSize;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUtilisateur(): ?UserInterface
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(UserInterface $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function serialize()
    {
        $this->avatar = base64_encode($this->avatar);
    }

    public function unserialize($serialized)
    {
        $this->avatar = base64_decode($this->avatar);

    }
}
