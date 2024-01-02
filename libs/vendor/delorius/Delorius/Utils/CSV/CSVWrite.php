<?php
namespace Delorius\Utils\CSV;


class CSVWrite
{
    /** @var string */
    public $input_encoding = 'ISO-8859-1';
    /** @var string */
    public $output_encoding = 'ISO-8859-1';
    /** @var bool */
    public $convert_encoding = false;
    /** @var string */
    public $linefeed = "\n";
    /** @var string */
    public $delimiter = ';';
    /** @var string */
    public $enclosure = '"';
    /** @var bool */
    public $enclose_all = false;
    /** @var string */
    public $output_filename = 'data.csv';
    /** @var array */
    public $fields = array();
    /** @var bool */
    public $heading = true;

    /** @var array */
    protected $data = array();


    /**
     * @param $row
     */
    public function addRow($row)
    {
        $this->data[] = $row;
    }


    /**
     * @param $rows
     */
    public function addRows($rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    /**
     * Encoding
     * Convert character encoding
     *
     * @param  string $input Input character encoding, uses default if left blank
     * @param  string $output Output character encoding, uses default if left blank
     */
    public function encoding($input = null, $output = null)
    {
        $this->convert_encoding = true;
        if (!is_null($input)) {
            $this->input_encoding = $input;
        }

        if (!is_null($output)) {
            $this->output_encoding = $output;
        }
    }

    /**
     * @param string $filename
     * @param array $data
     * @param array $fields
     * @param null $delimiter
     * @return bool
     */
    public function save($filename = '', $data = array(), $fields = array(), $delimiter = null)
    {
        if (empty($filename)) {
            $filename = $this->output_filename;
        }

        if ($delimiter === null) {
            $delimiter = $this->delimiter;
        }

        $flat_string = $this->unparse($data, $fields, $delimiter);
        return $this->_wfile($filename, $flat_string);
    }

    /**
     * @param null $filename
     * @param array $data
     * @param array $fields
     * @param null $delimiter
     * @return string
     */
    public function output($filename = null, $data = array(), $fields = array(), $delimiter = null)
    {
        if (empty($filename)) {
            $filename = $this->output_filename;
        }
        if ($delimiter === null) {
            $delimiter = $this->delimiter;
        }
        $flat_string = $this->unparse($data, $fields, $delimiter);
        if (!is_null($filename)) {
            header('Content-type: application/csv');
            header('Content-Length: ' . strlen($flat_string));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Disposition: attachment; filename="' . $filename . '"; modification-date="' . date('r') . '";');
            echo $flat_string;
        }
        return $flat_string;
    }

    /**
     * @param array $data
     * @param array $fields
     * @param null $delimiter
     * @return string
     */
    protected function unparse($data = array(), $fields = array(), $delimiter = null)
    {
        if (!is_array($data) || empty($data)) {
            $data = &$this->data;
        } else {
            $this->data = $data;
        }

        if (!is_array($fields) || empty($fields)) {
            $fields = &$this->fields;
        } else {
            $this->fields = $fields;
        }

        if ($delimiter == null) {
            $delimiter = $this->delimiter;
        }

        $string = chr(0xEF) . chr(0xBB) . chr(0xBF);
        // create heading
        /** @noinspection ReferenceMismatchInspection */
        $fieldOrder = $this->_validate_fields_for_unparse($fields);
        if (!$fieldOrder && !empty($data)) {
            $column_count = count($data[0]);
            $columns = range(0, $column_count - 1, 1);
            $fieldOrder = array_combine($columns, $columns);
        }

        if ($this->heading && !empty($fields)) {
            foreach ($fieldOrder as $column_name) {
                $entry[] = $this->_enclose_value($column_name, $delimiter);
            }

            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = array();
        }

        $_keys = array_keys($fieldOrder);
        foreach ($data as $key => $row) {
            foreach ($_keys as $index) {
                $cell_value = $row[$index];
                $entry[] = $this->_enclose_value($cell_value, $delimiter);
            }

            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = array();
        }

        if ($this->convert_encoding) {
            $string = iconv($this->input_encoding, $this->output_encoding, $string);
        }

        return $string;
    }

    /**
     * Enclose values if needed
     *  - only used by unparse()
     *
     * @param string $value Cell value to process
     * @param string $delimiter Character to put between cells on the same row
     *
     * @return string Processed value
     */
    private function _enclose_value($value = null, $delimiter)
    {
        if ($value !== null && $value != '') {
            $delimiter_quoted = $delimiter ?
                preg_quote($delimiter, '/') . "|"
                : '';
            $enclosure_quoted = preg_quote($this->enclosure, '/');
            $pattern = "/" . $delimiter_quoted . $enclosure_quoted . "|\n|\r/i";
            if ($this->enclose_all || preg_match($pattern, $value) || ($value{0} == ' ' || substr($value, -1) == ' ')) {
                $value = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value);
                $value = $this->enclosure . $value . $this->enclosure;
            }
        }

        return $value;
    }

    /**
     * @param $fields
     * @return array
     */
    private function _validate_fields_for_unparse($fields)
    {
        if (empty($fields)) {
            return array();
        }

        if (count($this->fields)) {
            return array_combine($this->fields, $this->fields);
        }

        // this is needed because sometime titles property is overwritten instead of using fields parameter!
        $titlesOnParse = !empty($this->data) ? array_keys($this->data[0]) : array();

        // both are identical, also in ordering
        if (array_values($fields) === array_values($titlesOnParse)) {
            return array_combine($fields, $fields);
        }

        // if renaming given by: $oldName => $newName (maybe with reorder and / or subset):
        // todo: this will only work if titles are unique
        $fieldOrder = array_intersect(array_flip($fields), $titlesOnParse);
        if (!empty($fieldOrder)) {
            return array_flip($fieldOrder);
        }

        $fieldOrder = array_intersect($fields, $titlesOnParse);
        if (!empty($fieldOrder)) {
            return array_combine($fieldOrder, $fieldOrder);
        }

        // original titles are not given in fields. that is okay if count is okay.
        if (count($fields) != count($titlesOnParse)) {
            throw new \UnexpectedValueException(
                "The specified fields do not match any titles and do not match column count.\n" .
                "\$fields was " . print_r($fields, true) .
                "\$titlesOnParse was " . print_r($titlesOnParse, true));
        }

        return array_combine($titlesOnParse, $fields);
    }

    /**
     * @param $file
     * @param string $content
     * @param string $mode
     * @param int $lock
     * @return bool
     */
    private function _wfile($file, $content = '', $mode = 'wb', $lock = LOCK_EX)
    {
        if ($fp = fopen($file, $mode)) {
            flock($fp, $lock);
            $re = fwrite($fp, $content);
            $re2 = fclose($fp);

            if ($re !== false && $re2 !== false) {
                return true;
            }
        }

        return false;
    }
}
