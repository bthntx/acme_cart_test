<?php

class Cart
{
    private $items = [];
    private $catalog = [];
    private $shippingCost = [];
    private $offerList = [];

    /**
     * Cart constructor.
     * @param array $catalog
     * @param array $shippingCost
     * @param array $offerList
     */
    public function __construct(array $catalog, array $shippingCost, array $offerList)
    {
        $this->catalog = $catalog;
        $this->shippingCost = $shippingCost;
        $this->offerList = $offerList;
    }


    public function addItem($itemCode)
    {
        $product = $this->findProductByCode($itemCode);

        if (!$product) {
            return;
        }

        if (array_key_exists($itemCode, $this->items)) {
            $this->items[$itemCode]["qty"]++;
        } else {
            $this->items[$itemCode] = [
                "product" => $product,
                "qty" => 1,
                "price" => $product["price"],
            ];
        }
    }

    private function findProductByCode(string $code)
    {
        $items = array_filter($this->catalog, function ($catalogItem) use ($code) {
            return ($catalogItem["code"] === $code) ? true : false;
        });

        return array_shift($items);
    }

    public function getTotal()
    {
        return $this->getTotalRows()["Total"];
    }

    public function getTotalRows()
    {
        $total = 0;
        foreach ($this->items as $k => $item) {
            for ($i = 0; $i < $item["qty"]; $i++) {
                $price = round($this->calcPriceChangePerItem($item["product"], $i), 2, PHP_ROUND_HALF_DOWN);
                $total += $price;
                $result["item-$k-$i"] = '::'.$price;
            }
        }
        $shipping = $this->findShippingByPrice($total);

        $result["SubTotal"] = $total;
        $result["Shipping"] = $total;
        $result["Total"] = round($total + $shipping, 2, PHP_ROUND_HALF_DOWN);

        return $result;
    }

    private function calcPriceChangePerItem($product, $i)
    {
        $offers = $this->findOffersForItem($product["code"]);
        $price = $product["price"];
        foreach ($offers as $offer) {
            if (($i + 1) % $offer["each_n_item"] == 0) {
                $price = $price * $offer["discount_percents"] / 100;
            }
        }

        return $price;
    }


    //Get First element from array with code
    //We assume that product code is uniq

    private function findOffersForItem($code)
    {
        return array_filter($this->offerList, function ($item) use ($code) {
            return ($item["codeCondition"] === $code) ? true : false;
        });

    }


    //Get last applicable shipping rate from array
    //We assume that array sorted by moreThan ASC

    private function findShippingByPrice(float $total): float
    {
        $items = array_filter($this->shippingCost, function ($shippingItem) use ($total) {
            return ($shippingItem["moreThan"] <= $total) ? true : false;
        });
        $result = array_pop($items);

        return $result ? $result["price"] : null;
    }


    //Apply discount on item if applicable

    public function printCart()
    {
        foreach ($this->getTotalRows() as $k => $row) {
            echo $k.':'.$row."\n";
        }
    }

    //Gets array of offers that could be possible to apply

    public function clearCart()
    {
        $this->items = [];
    }
}
