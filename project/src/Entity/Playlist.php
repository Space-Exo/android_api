<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pathImg = null;

    #[ORM\Column]
    private ?bool $likedTitle = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'playlists')]
    private Collection $user;

    #[ORM\ManyToMany(targetEntity: Music::class, inversedBy: 'playlists')]
    private Collection $music;

    #[ORM\ManyToOne(inversedBy: 'playlistsOwner')]
    private ?User $owner = null;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->music = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPathImg(): ?string
    {
        return $this->pathImg;
    }

    public function setPathImg(?string $pathImg): static
    {
        $this->pathImg = $pathImg;

        return $this;
    }

    public function isLikedTitle(): ?bool
    {
        return $this->likedTitle;
    }

    public function setLikedTitle(bool $likedTitle): static
    {
        $this->likedTitle = $likedTitle;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Music>
     */

    /**
     * Check if a music exists in the playlist.
     *
     * @param Music $music The music to check.
     * @return bool True if the music exists in the playlist, false otherwise.
     */
    public function hasMusic(Music $music): bool
    {
        return $this->music->contains($music);
    }
    public function getMusic(): Collection
    {
        return $this->music;
    }

    public function addMusic(Music $music): static
    {
        if (!$this->music->contains($music)) {
            $this->music->add($music);
        }

        return $this;
    }

    public function removeMusic(Music $music): static
    {
        $this->music->removeElement($music);

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
