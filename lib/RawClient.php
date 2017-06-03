<?php
/**
 * File: lib/RawClient.php
 * Created Date: 2017-06-02 16:07:23
 */
declare (strict_types = 1);

namespace RedisProto;

class RawClient
{
    protected $_socket;

    protected $_encoder;

    protected $_config;

    protected function __construct()
    {
        $this->_encoder = new Utils();
    }

    public function isPersisted(): bool
    {
        return $this->_config['persist'];
    }

    /**
     * @return static
     */
    public static function connect(array $config)
    {
        $ret = new static();

        $config['persist'] = $config['persist'] ?? false;

        $ret->_config = $config;

        $ret->_doConnect();

        return $ret;
    }

    protected function _doConnect()
    {
        $errNo = 0;

        if ($this->_config['persist'] ?? false) {

            $this->_socket = pfsockopen(
                $this->_config['host'],
                $this->_config['port'],
                $errNo,
                $error,
                $this->_config['timeout'] ?? 3
            );
        }
        else {

            $this->_socket = fsockopen(
                $this->_config['host'],
                $this->_config['port'],
                $errNo,
                $error,
                $this->_config['timeout'] ?? 3
            );
        }

        if ($errNo) {

            throw new \Exception("Failed to connect to server: {$error}", $errNo);
        }
    }

    public function sendCommand(...$args)
    {
        if (!$this->_socket) {

            $this->_doConnect();
        }

        try {

            fwrite($this->_socket, $this->_encoder->encode($args));

            while ($ret = fread($this->_socket, 1024)) {

                if ($this->_encoder->decode($ret)) {

                    break;
                }
            }
        }
        catch (Exception $e) {

            $this->close();

            throw $e;
        }

        $ret = array_shift($this->_encoder->results);

        if (is_array($ret)) {

            return array_splice($this->_encoder->results, 0, $ret[0]);
        }

        return $ret;
    }

    protected function _close()
    {
        fclose($this->_socket);

        $this->_socket = null;
    }

    public function close()
    {
        if ($this->_config['persist'] || !$this->_socket) {

            return;
        }

        $this->sendCommand('quit');

        $this->_close();
    }

    public function __destruct()
    {
        $this->_socket && $this->close();
    }
}
