<?php
/**
 * File: lib/Utils.php
 * Created Date: 2017-06-02 17:02:12
 */
declare (strict_types = 1);

namespace RedisProto;

const E_PROTOCOL_FAILURE = 0x0001;

class Utils
{
    protected $_buffer = null;
    protected $_dtr;
    protected $_status = self::ST_IDLE;

    public $results = [];

    const ST_IDLE = 0;
    const ST_READING_DATA = 1;

    const CRLF = "\r\n";

    protected function _readUntilCRLF(string $rawData, int $offset = 0)
    {
        $pos = strpos($rawData, self::CRLF, $offset);
        return $pos === false ? null : [substr($rawData, $offset, $pos - $offset), $pos];
    }

    /**
     * Decode the data of redis protocol.
     * @param string $rawText
     * @throws \RedisProto\Exception
     * @return int returns how many item was decoded.
     */
    public function decode(string $rawText): int
    {
        $ret = 0;

        if ($this->_buffer) {

            $rawText = $this->_buffer . $rawText;
            $this->_buffer = null;
        }

        while (1) {

            switch ($this->_status) {
            case self::ST_IDLE:

                switch ($rawText[0]) {
                case ':':

                    list($len, $pos) = $this->_readUntilCRLF($rawText, 1);

                    if ($len === null) {

                        $this->_buffer = $rawText;
                        goto endingLoop;
                    }

                    $this->results[] = (int)$len;

                    $rawText = substr($rawText, $pos + 2);

                    $ret++;

                    break;

                case '+':

                    list($message, $pos) = $this->_readUntilCRLF($rawText, 1);

                    if ($message === null) {

                        $this->_buffer = $rawText;
                        goto endingLoop;
                    }

                    $this->results[] = $message;

                    $rawText = substr($rawText, $pos + 2);

                    $ret++;

                    break;

                case '-':

                    list($message, $pos) = $this->_readUntilCRLF($rawText, 1);

                    if ($message === null) {

                        $this->_buffer = $rawText;
                        goto endingLoop;
                    }

                    throw new Exception($message, E_PROTOCOL_FAILURE);

                    $ret++;

                    break;

                case '$':

                    list($this->_dtr, $pos) = $this->_readUntilCRLF($rawText, 1);

                    if ($this->_dtr === null) {

                        $this->_buffer = $rawText;
                        goto endingLoop;
                    }

                    $this->_dtr = (int)$this->_dtr;

                    $rawText = substr($rawText, $pos + 2);

                    if ($this->_dtr === -1) {

                        $this->results[] = null;
                        $ret++;
                    }
                    elseif (strlen($rawText) < $this->_dtr + 2) {

                        $this->_buffer = $rawText;
                        $this->_status = self::ST_READING_DATA;
                        $rawText = null;
                    }
                    else {

                        $this->results[] = substr($rawText, 0, $this->_dtr);
                        $rawText = substr($rawText, $this->_dtr + 2);
                        $ret++;
                    }

                    break;

                case '*':

                    list($num, $pos) = $this->_readUntilCRLF($rawText, 1);

                    if ($num === null) {

                        $this->_buffer = $rawText;
                        goto endingLoop;
                    }

                    $this->results[] = [(int)$num];

                    $rawText = substr($rawText, $pos + 2);

                    break;

                default:

                    throw new Exception('Unexpected token in Redis protocol.', E_PROTOCOL_FAILURE);
                }

                break;

            case self::ST_READING_DATA:

                if (strlen($rawText) < $this->_dtr + 2) {

                    $this->_buffer = $rawText;
                    goto endingLoop;
                }
                else {

                    $this->results[] = substr($rawText, 0, $this->_dtr);
                    $rawText = substr($rawText, $this->_dtr + 2);
                    $this->_status = self::ST_IDLE;
                    $ret++;
                }

                break;
            }

            if (!$rawText) {

                break;
            }
        }

endingLoop:

        return $ret;
    }

    public function encode(array $args): string
    {
        $argSects = [
            '*' . count($args)
        ];

        foreach ($args as $arg) {

            if (!is_string($arg)) {

                $arg = "{$arg}";
            }

            $argSects[] = '$' . strlen($arg);

            $argSects[] = $arg;
        }

        return join(self::CRLF, $argSects) . self::CRLF;
    }
}