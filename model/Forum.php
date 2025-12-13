<?php
class Forum {
    private ?int $id;
    private ?string $title;
    private ?string $category;
    private ?string $author;
    private ?string $content;
    private ?string $created_at;
    private ?string $updated_at;
    private ?int $views;
    private ?bool $is_pinned;
    private ?bool $is_locked;

    // Constructeur
    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $category = null,
        ?string $author = null,
        ?string $content = null,
        ?string $created_at = null,
        ?string $updated_at = null,
        ?int $views = 0,
        ?bool $is_pinned = false,
        ?bool $is_locked = false
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->author = $author;
        $this->content = $content;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->views = $views;
        $this->is_pinned = $is_pinned;
        $this->is_locked = $is_locked;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function getCategory(): ?string {
        return $this->category;
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

    public function getViews(): ?int {
        return $this->views;
    }

    public function getIsPinned(): ?bool {
        return $this->is_pinned;
    }

    public function getIsLocked(): ?bool {
        return $this->is_locked;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function setCategory(?string $category): void {
        $this->category = $category;
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

    public function setViews(?int $views): void {
        $this->views = $views;
    }

    public function setIsPinned(?bool $is_pinned): void {
        $this->is_pinned = $is_pinned;
    }

    public function setIsLocked(?bool $is_locked): void {
        $this->is_locked = $is_locked;
    }

    // Méthode d'affichage
    public function show(): void {
        echo "<div class='forum-display'>";
        echo "<h3>" . htmlspecialchars($this->title) . "</h3>";
        echo "<p><strong>Catégorie:</strong> " . htmlspecialchars($this->category) . "</p>";
        echo "<p><strong>Auteur:</strong> " . htmlspecialchars($this->author) . "</p>";
        echo "<p><strong>Contenu:</strong> " . nl2br(htmlspecialchars($this->content)) . "</p>";
        echo "<p><strong>Créé le:</strong> " . $this->created_at . "</p>";
        echo "<p><strong>Vues:</strong> " . $this->views . "</p>";
        echo "</div>";
    }
}
?>