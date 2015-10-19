<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMover\Model;

use Shideon\DataMover\FieldInterface;
use Shideon\DataMover\Input;
use Shideon\DataMover\Iteration;
use Shideon\DataMover\ModelInterface;
use Shideon\DataMover\Output;

/**
 * Shideon\DataMover\Model\AbstractCsvModel
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
abstract class AbstractCsvModel implements ModelInterface
{
    /**
     * @var string Csv field delimiter
     */
    protected $delimiter = ",";

    /**
     * @var string Csv field enclosure
     */
    protected $enclosure = "\"";

    /**
     * @var string Csv field escape character
     */
    protected $escape = "\\";

    /**
     * @var bool
     */
    protected $skipFirstRow = false;

    /**
     * @inheritDoc
     *
     * This is the minimum requirement to make a migration function with our class structure but your extension
     * will most likely need to extend {@see self::endIteration()} and/or {@see self::end()} to define your
     * specific migration functionality.
     */
    abstract public function getFields();

    /**
     * Constructor
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param bool|false $skipFirstRow
     */
    public function __construct($delimiter = ",", $enclosure = "\"", $escape = "\\", $skipFirstRow = false)
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->skipFirstRow = $skipFirstRow;
    }

    /**
     * @inheritDoc
     */
    public function createIterationInput($iterationData)
    {
        return str_getcsv($iterationData, $this->delimiter, $this->enclosure, $this->escape);
    }

    /**
     * @inheritDoc
     * @see ModelInterface::begin()
     */
    public function begin()
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::end()
     */
    public function end()
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::beginIteration()
     */
    public function beginIteration(&$iterationInput, &$iterationOutput)
    {
        // nothing to do by default. override at will.
    }

    /**
     * @inheritDoc
     * @see ModelInterface::endIteration()
     */
    public function endIteration($iterationOutput)
    {
        // nothing to do by default. override at will.
    }
}