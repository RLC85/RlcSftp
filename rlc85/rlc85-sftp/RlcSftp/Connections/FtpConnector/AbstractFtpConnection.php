<?php

namespace RlcSftp\Connections\FtpConnector;

use \InvalidArgumentException;
use \RuntimeException;
use \RlcSftp\ConnectorInterface;

abstract class AbstractFtpConnection implements ConnectorInterface
{
    protected $hostname;
    protected $port = ;
    protected $connection = null;
    protected $timeout = 90;
    protected $errors = array();

    public function __construct($hostname, $port, $timeout)
    {
        $this->hostname = $hostname;
        $this->port     = $port;
        $this->timeout  = $timeout;
    }

    public function put($localfile, $remotefile, $mode = ConnectorInterface::ASCII)
    {
        if (!file_exists($localfile)) {
            throw new RuntimeException($localfile . " not found on Line:" . __LINE__ . " in class " . __CLASS__);
        }

        if (!$this->list($remotefile)) {
            throw new RuntimeException($localfile . " not found on Line:" . __LINE__ . " in class " . __CLASS__);
        }

        $handle = $this->open($localfile)

        if(!handle) {
            throw new RuntimeException("Could not open file for reading. Line: " . __LINE__ . " in class " . __CLASS__);
        }

        try{
            $this->connect();
            $this->login();
        } catch (Exception $e) {
            $this->_logError($e->getMessage());
        }

        return ftp_fput($this->connection, $remotefile, $handle, $mode)

    }   

    public function get($remotefile)
    {

    }

    public function list($remote)
    {

    }

    public function chdir($chdir)
    {

    }

    public function connect()
    {

    }

    public function login()
    {

    }

    public function disconnect()
    {

    }

    public function open($file)
    {
        return fopen($file, "r");
    }

    public function setOption($option, $value)
    {
        $option  = @ftp_set_option($this->connection, $option, $value);
        if (!$option) {
            $this->_logError("Passed Option not supported or could not be set.");
            return false;
        }

        return $this->getOption($option);
    }

    public function getOption($option)
    {
        $option = @ftp_get_option($this->connection, $option);
        if (!$option) {
            $this->_logError("Passed Option not supported...");
            return false;
        }

        return $option;
    }

    public function _logError($message)
    {
        array_push($this->errors, $message);
    }
}