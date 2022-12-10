<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("postsList")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("postsList")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("postsList")]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("postsList")]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("postsList")]
    private ?string $video = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups("postsList")]
    private ?int $status = null;

    #[ORM\Column]
    #[Groups("postsList")]
    private ?\DateTimeImmutable $createdAt = null;
    
    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups("postsList")]
    private ?User $author = null;
    
    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups("postsList")]
    private ?ProductCategory $product_category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getProductCategory(): ?ProductCategory
    {
        return $this->product_category;
    }

    public function setProductCategory(?ProductCategory $product_category): self
    {
        $this->product_category = $product_category;

        return $this;
    }
}
