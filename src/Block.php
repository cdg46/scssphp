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

namespace Leafo\ScssPhp;

/**
 * SCSS block
 *
 * {@internal
 *     The conversion of blocks from \stdClass to Block is a work-in-progress.
 * }}
 *
 * @author Anthon Pang <anthon.pang@gmail.com>
 */
class Block
{
    /**
     * Not yet implemented
     */
    const T_MEDIA = 'media';
    const T_MIXIN = 'mixin';
    const T_INCLUDE = 'include';
    const T_FUNCTION = 'function';
    const T_EACH = 'each';
    const T_WHILE = 'while';
    const T_FOR = 'for';
    const T_IF = 'if';
    const T_ELSE = 'else';
    const T_ELSEIF = 'elseif';
    const T_DIRECTIVE = 'directive';
    const T_NESTED_PROPERTY = 'nestedprop';
    const T_BLOCK = 'block';
    const T_ROOT = 'root';
    const T_NULL = null;
    const T_COMMENT = 'comment';

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Leafo\ScssPhp\Block
     */
    public $parent;

    /**
     * @var integer
     */
    public $sourcePosition;

    /**
     * @var \Leafo\ScssPhp\Parser
     */
    public $sourceParser;

    /**
     * @var array
     */
    public $selectors;

    /**
     * @var array
     */
    public $comments;

    /**
     * @var array
     */
    public $children;

    /**
     * @var array
     */
    public $store;

    /**
     * @var array
     */
    public $lines;

    /**
     * @var integer
     */
    public $depth;
}
