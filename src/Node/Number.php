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
 *     This is a work-in-progress.
 *
 *     The \ArrayAccess interface is temporary until the migration is complete.
 * }}
 *
 * @author Anthon Pang <anthon.pang@gmail.com>
 */
class Number extends Node implements \ArrayAccess
{
    /**
     * @var integer
     */
    static public $precision = 5;

    /**
     * @var array
     */
    static protected $unitTable = array(
        'in' => array(
            'in' => 1,
            'pc' => 6,
            'pt' => 72,
            'px' => 96,
            'cm' => 2.54,
            'mm' => 25.4,
            'q'  => 101.6,
        ),
        'turn' => array(
            'deg' => 180,
            'grad' => 200,
            'rad' => M_PI,
            'turn' => 0.5,
        ),
        's' => array(
            's' => 1,
            'ms' => 1000,
        ),
        'Hz' => array(
            'Hz' => 1,
            'kHz' => 0.001,
        ),
        'dpi' => array(
            'dpi' => 1,
            'dpcm' => 2.54,
            'dppx' => 96,
        ),
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
        $this->units = is_array($initialUnit)
            ? $initialUnit
            : ($initialUnit ? array($initialUnit => 1)
                            : array());
    }

    // $number should be normalized
    public function coerce($unit)
    {
        $value = $this->dimension;
        $baseUnit = $this->units;

        if (0 && isset(self::$unitTable[$baseUnit][$unit])) {
            $value = $value * self::$unitTable[$baseUnit][$unit];
        }

        return new Number($value, $unit);
    }

    // just does physical lengths for now
    public function normalize()
    {
        if (0 && isset(self::$unitTable['in'][$this->units])) {
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

        if ($offset === -1
            || $offset === 0
            || $offset === 1
            || $offset === 2
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case -2:
                return $this->sourceParser;

            case -1:
                return $this->sourcePosition;

            case 0:
                return $this->type;

            case 1:
                return $this->dimension;

            case 2:
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
     * @return boolean
     */
    public function unitless()
    {
        return ! count($this->units);
    }

    /**
     * @return string
     */
    public function unitStr()
    {
        $numerators = array();
        $denominators = array();

        foreach ($this->units as $unit => $unitSize) {
            if ($unitSize > 0) {
                $numerators = array_pad($numerators, count($numerators) + $unitSize, $unit);
                continue;
            }

            if ($unitSize < 0) {
                $denominators = array_pad($denominators, count($denominators) + $unitSize, $unit);
                continue;
            }
        }

        return implode('*', $numerators) . (count($denominators) ? '/' . implode('*', $denominators) : '');
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $value = round($this->dimension, self::$precision);

        foreach ($this->units as $unit => $unitSize) {
            if (count($this->units) !== 1 || $unitSize !== 1) {
                $this->throwError('Not a valid CSS value');
            }

            return (string) $value . $unit;
        }

        return (string) $value;
    }
}
