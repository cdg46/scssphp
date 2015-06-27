<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2015 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/gpl-license GPL-3.0
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.net/scssphp
 */

namespace Leafo\ScssPhp\Node;

use Leafo\ScssPhp\Node;

/**
 * SCSS dimension + optional units
 *
 * {@internal
 * }}
 *
 * @author Anthon Pang <anthon.pang@gmail.com>
 */
class Number extends Node implements \ArrayAccess
{
    static protected $unitTable = array(
        'in' => array(
            'in' => 1,
            'pc' => 6,
            'pt' => 72,
            'px' => 96,
            'cm' => 2.54,
            'mm' => 25.4,
            'q'  => 101.6,
        )
    );

    /**
     * @var integer|float
     */
    public $dimension;

    /**
     * @var array
     */
    public $units;

    /**
     * Initialize number
     *
     * @param mixed  $dimension
     * @param string $unit
     */
    public function __construct($dimension, $initialUnit)
    {
        $this->type = Node::T_NUMBER;
        $this->dimension = $dimension;
        $this->units = $initialUnit;
    }

    /**
     * Are units compatible?
     *
     * @param \Leafo\ScssPhp\Node\Number $number
     *
     * @return boolean
     */
    public function isCompatible($number)
    {
    }

    public function modulo($right)
    {
        if ($opName == 'mod' && $right[2] != '') {
            $this->throwError("Cannot modulo by a number with units: $right[1]$right[2].");
        }
    }

    // $number should be normalized
    public function coerce($unit)
    {
        $value = $this->dimension;
        $baseUnit = $this->units;

        if (isset(self::$unitTable[$baseUnit][$unit])) {
            $value = $value * self::$unitTable[$baseUnit][$unit];
        }

        return new Number($value, $unit);
    }

    // just does physical lengths for now
    public function normalize()
    {
        if (isset(self::$unitTable['in'][$this->units])) {
            $conv = self::$unitTable['in'][$this->units];

            return new Number($this->dimension / $conv, 'in');
        }

        return new Number($this->dimension, $this->units);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if ($offset === -2) {
            return $sourceParser !== null;
        }

        if ($offset === -1) {
            return true;
        }

        if ($offset === 0) {
            return true;
        }

        if ($offset === 1) {
            return true;
        }

        if ($offset === 2) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ($offset === -2) {
            return $this->sourceParser;
        }

        if ($offset === -1) {
            return $this->sourcePosition;
        }

        if ($offset === 0) {
            return $this->type;
        }

        if ($offset === 1) {
            return $this->dimension;
        }

        if ($offset === 2) {
            return $this->units;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === 1) {
            $this->dimension = $value;
        } elseif ($offset === 2) {
            $this->units = $value;
        } elseif ($offset == -1) {
            $this->sourcePosition = $value;
        } elseif ($offset == -2) {
            $this->sourceParser = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ($offset === 1) {
            $this->dimension = null;
        } elseif ($offset === 2) {
            $this->units = null;
        } elseif ($offset === -1) {
            $this->sourcePosition = null;
        } elseif ($offset === -2) {
            $this->sourceParser = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->dimension . $this->units;
    }
}
