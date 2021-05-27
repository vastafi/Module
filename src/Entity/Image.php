<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tag;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTag(): ?string
    {
        return str_replace(["[", "]", "\""], " ", $this->tag);
    }

    public function getTagsArray(): ?array
    {
        return json_decode((string)$this->tag);
    }

    public function setTag(string $tag): self
    {
        $tag = strtolower($tag);
        $tag = explode(',', $tag);
        $trimmedTags = [];
        foreach ($tag as $tags) {
            $tags = ltrim($tags);
            $tags = rtrim($tags);
            array_push($trimmedTags, $tags);
        }
        $this->tag = json_encode($trimmedTags);
        return $this;
    }

    public function setTagsFromArray(array $tag): self
    {
        $this->tag = json_encode($tag);
        return $this;
    }
    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
