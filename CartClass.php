<?php

class Cart {
    protected array $items = [];

    public function addProduct(Product $product, int $quantity): void {
        if (isset($this->items[$product->getId()])) {
            $this->items[$product->getId()]['quantity'] += $quantity;
        } else {
            $this->items[$product->getId()] = ['product' => $product, 'quantity' => $quantity];
        }
    }

    public function removeProduct(string $productId): void {
        unset($this->items[$productId]);
    }

    public function getTotalPrice(): float {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function getItems(): array {
        return $this->items;
    }
}

?>