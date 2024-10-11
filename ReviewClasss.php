<?php

class Review {
    protected string $userId;
    protected string $productId;
    protected int $rating;
    protected string $comment;

    public function __construct(string $userId, string $productId, int $rating, string $comment) {
        $this->userId = $userId;
        $this->productId = $productId;
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function getRating(): int {
        return $this->rating;
    }

    public function getComment(): string {
        return $this->comment;
    }

    public function getUserId(): string {
        return $this->userId;
    }

    public function getProductId(): string {
        return $this->productId;
    }
}

?>