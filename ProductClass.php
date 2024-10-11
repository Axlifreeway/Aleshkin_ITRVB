<?php

class Product {
    protected string $id;
    protected string $name;
    protected float $price;
    protected string $description;

    public function __construct(string $id, string $name, float $price, string $description) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getId(): string {
        return $this->id;
    }
}

class DigitalProduct extends Product {
    public function getPrice(): float {
        return parent::getPrice() * 0.5;
    }
}

class PhysicalProduct extends Product {
    protected int $quantity;

    public function __construct(string $id, string $name, float $price, string $description, int $quantity) {
        parent::__construct($id, $name, $price, $description);
        $this->quantity = $quantity;
    }

    public function getPrice(): float {
        return parent::getPrice() * $this->quantity;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }
}

class WeightedProduct extends Product {
    protected float $weight;

    public function __construct(string $id, string $name, float $price, string $description, float $weight) {
        parent::__construct($id, $name, $price, $description);
        $this->weight = $weight;
    }

    public function getPrice(): float {
        return parent::getPrice() * $this->weight;
    }

    public function getWeight(): float {
        return $this->weight;
    }
}

?>