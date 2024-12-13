<?php

abstract class AbstractHandler
{
    /**
     * @var AbstractHandler
     */
    protected $_next;

    /**
     * Abstract method for handling requests
     *
     * @param string $field
     * @param string $value
     */
    abstract public function search($field, $value);

    /**
     * Set the next handler in the chain
     *
     * @param AbstractHandler $next
     */
    public function setNext($next)
    {
        $this->_next = $next;
    }

    /**
     * Get the next handler in the chain
     *
     * @return AbstractHandler|null
     */
    public function getNext()
    {
        return $this->_next;
    }
}

class CountryHandler extends AbstractHandler
{
    private $country;

    public function __construct($country)
    {
        $this->country = $country;
    }

    public function search($field, $value)
    {
        if ($this->country->search($field, $value)) {
            echo "Match found in country: " . $this->country->get('capital') . PHP_EOL;
        } else {
            if ($this->getNext()) {
                $this->getNext()->search($field, $value);
            } else {
                echo "No match found." . PHP_EOL;
            }
        }
    }
}

class Country
{
    public $area;
    public $population;
    public $language;
    private $capital;

    public function __construct($area, $population, $language, $capital)
    {
        $this->area = $area;
        $this->population = $population;
        $this->language = $language;
        $this->capital = $capital;
    }

    public function set($field, $value)
    {
        if (property_exists($this, $field)) {
            $this->$field = $value;
        } elseif ($field === 'capital') {
            $this->setCapital($value);
        } else {
            echo "Field $field does not exist.";
        }
    }

    public function get($field)
    {
        if (property_exists($this, $field)) {
            return $this->$field;
        } elseif ($field === 'capital') {
            return $this->getCapital();
        } else {
            return "Field $field does not exist.";
        }
    }

    public function show()
    {
        echo "<p>Area: " . $this->area . "</p>";
        echo "<p>Population: " . $this->population . "</p>";
        echo "<p>Language: " . $this->language . "</p>";
        echo "<p>Capital: " . $this->getCapital() . "</p>";
    }

    public function search($field, $value)
    {
        if (property_exists($this, $field)) {
            if ($this->$field == $value) {
                return true;
            }
        } elseif ($field === 'capital') {
            return $this->getCapital() === $value;
        }
        return false;
    }

    private function getCapital()
    {
        return $this->capital;
    }

    private function setCapital($capital)
    {
        $this->capital = $capital;
    }

    public static function show_objects($countries)
    {
        foreach ($countries as $country) {
            $country->show();
        }
    }
}

// Create Country objects
$country1 = new Country("45 339 км²", "1,349 мільйона", "Естонська", "Таллінн");
$country2 = new Country("603 628 км²", "41 мільйона", "Українська", "Київ");
$country3 = new Country("357 022 км²", "83 мільйона", "Німецька", "Берлін");

// Create handlers
$handler1 = new CountryHandler($country1);
$handler2 = new CountryHandler($country2);
$handler3 = new CountryHandler($country3);

// Chain handlers
$handler1->setNext($handler2);
$handler2->setNext($handler3);

// Search for a match
$field = "language";
$value = "Українська";
$handler1->search($field, $value);

$field = "capital";
$value = "Берлін";
$handler1->search($field, $value);

$field = "population";
$value = "100 мільйонів";
$handler1->search($field, $value);

?>
