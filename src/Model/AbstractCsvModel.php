<?php
/**
 * @package shideon/data-mover
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Shideon\DataMigrator\Model;

use Shideon\DataMigrator\AbstractModel;

/**
 * Shideon\DataMigrator\Model\AbstractCsvModel
 *
 * @author John Pancoast <johnpancoaster@gmail.com>
 */
abstract class AbstractCsvModel extends AbstractModel
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
     * will need to extend {@see self::endIteration()} and/or {@see self::end()} to define your specific
     * migration functionality.
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
}