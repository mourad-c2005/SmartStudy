<?php
class Reply {
    private ?int $id;
    private ?int $forum_id;
    private ?string $author;
    private ?string $content;
    private ?string $created_at;
    private ?string $updated_at;
    private ?bool $is_solution;
    private ?int $likes;

    // Constructeur
    public function __construct(
        ?int $id = null,
        ?int $forum_id = null,
        ?string $author = null,
        ?string $content = null,
        ?string $created_at = null,
        ?string $updated_at = null,
        ?bool $is_solution = false,
        ?int $likes = 0
    ) {
        $this->id = $id;
        $this->forum_id = $forum_id;
        $this->author = $author;
        $this->content = $content;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->is_solution = $is_solution;
        $this->likes = $likes;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getForumId(): ?int {
        return $this->forum_id;
    }

    public function getAuthor(): ?string {
        return $this->author;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    public function getIsSolution(): ?bool {
        return $this->is_solution;
    }

    public function getLikes(): ?int {
        return $this->likes;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setForumId(?int $forum_id): void {
        $this->forum_id = $forum_id;
    }

    public function setAuthor(?string $author): void {
        $this->author = $author;
    }

    public function setContent(?string $content): void {
        $this->content = $content;
    }

    public function setCreatedAt(?string $created_at): void {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(?string $updated_at): void {
        $this->updated_at = $updated_at;
    }

    public function setIsSolution(?bool $is_solution): void {
        $this->is_solution = $is_solution;
    }

    public function setLikes(?int $likes): void {
        $this->likes = $likes;
    }

    // Méthode d'affichage
    public function show(): void {
        echo "<div class='reply-display'>";
        echo "<p><strong>Auteur:</strong> " . htmlspecialchars($this->author) . "</p>";
        echo "<p>" . nl2br(htmlspecialchars($this->content)) . "</p>";
        echo "<p><small>Posté le: " . $this->created_at . "</small></p>";
        if ($this->is_solution) {
            echo "<span class='badge bg-success'>✓ Solution</span>";
        }
        echo "<p><small>👍 " . $this->likes . " likes</small></p>";
        echo "</div>";
    }
}
?>