<?php

namespace RlcSftp\Connections\FtpConnector;

use \InvalidArgumentException;
use \RuntimeException;
use \RlcSftp\Connections\Connector\ConnectorInterface;

class FtpConnection implements ConnectorInterface
{
    protected $hostname;
    protected $port = 21;
    protected $connection = null;
    protected $timeout = 90;
    protected $errors = array();
    protected $username = null;
    protected $password = null;

    public function __construct($hostname, $port = 21, $timeout = 90)
    {
        $this->hostname = $hostname;
        $this->port     = $port;
        $this->timeout  = $timeout;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function put($localfile, $remotefile, $mode = ConnectorInterface::ASCII)
    {
        if (!file_exists($localfile)) {
            throw new RuntimeException($localfile . " not found on Line:" . __LINE__ . " in class " . __CLASS__);
        }

        if (!$this->list($remotefile)) {
            throw new RuntimeException($localfile . " not found on Line:" . __LINE__ . " in class " . __CLASS__);
        }

        $handle = $this->open($localfile);

        if (!handle) {
            throw new RuntimeException("Could not open file for reading. Line: " . __LINE__ . " in class " . __CLASS__);
        }

        try {
            $this->connect();
            $this->login();
        } catch (Exception $e) {
            $this->_logError($e->getMessage());
        }

        return ftp_fput($this->connection, $remotefile, $handle, $mode);

    }

    public function get($remotefile)
    {

    }

    public function flist($remote)
    {

    }

    public function chdir($chdir)
    {

    }

    public function connect()
    {
        $this->connection = @ftp_connect($this->hostname, $this->port, $this->timeout);
        if ($this->connection) {
            return true;
        }

        $this->connection = null;
        return false;

    }

    public function login()
    {
        $login = @ftp_login($this->connection, $this->username, $this->password);
        return $login;
    }

    public function disconnect()
    {
        if (!$this->connection) {
            return true;
        }

        $disconnect = ftp_close($this->connection);
        $this->connection = null;
        return $disconnect;
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
