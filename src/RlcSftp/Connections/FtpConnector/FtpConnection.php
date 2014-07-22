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
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this->password;
    }

    public function put($localfile, $remoteDir, $filename, $mode = ConnectorInterface::ASCII)
    {
        if ($this->connection === null) {
            if (!$this->connect()) {
                throw new RuntimeException("No FTP Connection Established, did you call FtpConnection::connect() before trying to send your file?");
            }
        }

        if (!$this->login()) {
            throw new RuntimeException("Not Logged into the FTP Sever.");
        }

        if (!$this->flist($remoteDir)) {
            throw new RuntimeException($remoteDir . " is not a directory. Line:" . __LINE__ . " in class " . __CLASS__);
        }

        $handle = @fopen($localfile, "r");

        if (!$handle) {
            throw new RuntimeException("Could not open file for reading. Line: " . __LINE__ . " in class " . __CLASS__);
        }

       
        
        $remoteFile = $remoteDir . $filename;
        return ftp_fput($this->connection, $remoteFile, $handle, $mode);
    
    }

    public function get($localfile, $remoteDir, $filename, $mode = ConnectorInterface::ASCII)
    {
        if ($this->connection === null) {
            if (!$this->connect()) {
                throw new RuntimeException("No FTP Connection Established, did you call FtpConnection::connect() before trying to send your file?");
            }
        }

        if (!$this->login()) {
            throw new RuntimeException("Not Logged into the FTP Sever.");
        }

        if (!$this->flist($remoteDir)) {
            throw new RuntimeException($remoteDir . " is not a directory. Line:" . __LINE__ . " in class " . __CLASS__);
        }

        $handle = @fopen($localfile, "w");

        if (!$handle) {
            throw new RuntimeException("Could not open file for reading. Line: " . __LINE__ . " in class " . __CLASS__);
        }

       
        
        $remoteFile = $remoteDir . $filename;
        return ftp_fget($this->connection, $handle, $remoteFile, $mode);

    }

    public function flist($remote)
    {
        if ($this->connection === null) {
            throw new RuntimeException("No FTP Connection Established, did you call FtpConnection::connect() before trying to send your file?");
        }
        if (!$this->login()) {
            throw new RuntimeException("Not Logged into the FTP Sever, Log in before listing a directory.");
        }
        $list = ftp_nlist($this->connection, $remote);
        return ftp_nlist($this->connection, $remote);
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

    public function login($username = null, $password = null)
    {
        if ($this->connection === null) {
            throw new RuntimeException("No FTP Connection Established, did you call FtpConnection::connect() before trying to send your file?");
        }
        $u = $this->username;
        $p = $this->password;

        if (!empty($username)) {
            $u = $username;
        }

        if (!empty($password)) {
            $p = $password;
        }

        $login = @ftp_login($this->connection, $u, $p);

        if (!$login) {
            return false;
        }

        return true;
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
}
