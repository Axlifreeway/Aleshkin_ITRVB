<?php
abstract class Product {
    protected $price;

    public function __construct($price) {
        $this->price = $price;
    }

    abstract public function calculateFinalPrice();

    public function calculateRevenue($soldAmount) {
        return $this->calculateFinalPrice() * $soldAmount;
    }
}

class DigitalProduct extends Product {
    public function calculateFinalPrice() {
        return $this->price / 2;
    }
}

class PieceProduct extends Product {
    private $quantity;

    public function __construct($price, $quantity) {
        parent::__construct($price);
        $this->quantity = $quantity;
    }

    public function calculateFinalPrice() {
        return $this->price * $this->quantity;
    }
}

class WeightProduct extends Product {
    private $weight;

    public function __construct($price, $weight) {
        parent::__construct($price);
        $this->weight = $weight;
    }

    public function calculateFinalPrice() {
        return $this->price * $this->weight;
    }
}

// Пример использования
$digital = new DigitalProduct(100);
echo "Digital Product Price: " . $digital->calculateFinalPrice() . "\n";
echo "Revenue from Digital Product: " . $digital->calculateRevenue(10) . "\n";

$piece = new PieceProduct(50, 5);
echo "Piece Product Price: " . $piece->calculateFinalPrice() . "\n";
echo "Revenue from Piece Product: " . $piece->calculateRevenue(5) . "\n";

$weight = new WeightProduct(20, 3);
echo "Weight Product Price: " . $weight->calculateFinalPrice() . "\n";
echo "Revenue from Weight Product: " . $weight->calculateRevenue(3) . "\n";
?>
