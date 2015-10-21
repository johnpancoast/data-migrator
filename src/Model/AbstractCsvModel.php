<?php
/**
 * @package johnpancoast/data-migrator
 * @copyright (c) 2015 John Pancoast
 * @author John Pancoast <johnpancoaster@gmail.com>
 * @license MIT
 */

namespace Pancoast\DataMigrator\Model;

use Pancoast\DataMigrator\AbstractModel;

/**
 * A CSV migration model
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
    public function createIterationInput($iterationCount, $iterationData)
    {
        return str_getcsv($iterationData, $this->delimiter, $this->enclosure, $this->escape);
    }
}