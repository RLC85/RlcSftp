<?php

namespace RlcSftp\Connections\SftpConnector;

use \InvalidArgumentException;
use \RuntimeException;
use RlcSftp\Connections\Connector\ConnectorInterface;

abstract class AbstractSftpConnection implements ConnectorInterface
{
    protected $serverFingerprint = null;
    protected $connection = null;

    public function __construct($hostname, $port = 21)
    {
        $this->hostname = $hostname;
        $this->port     = $port;
    }

    public function setSftp()
    {
        $this->sftp = ssh2_sftp($this->connection);
        return $this->sftp;
    }

    public function getSftp()
    {
        return $this->sftp;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this->connection;
    }

    public function put($localFile, $remoteDir, $filename, $mode = 0644)
    {
        if ($this->connection === null) {
            if (!$this->connect()) {
                throw new RuntimeException("No SFTP Connection Established, did you call " . __CLASS__ ."::connect() before trying to send your file?");
            }
        }

        if (!$this->login()) {
            throw new RuntimeException("Not Logged into the SFTP Sever.");
        }

        if (!$this->flist($remoteDir)) {
            throw new RuntimeException($remoteDir . " is not a directory. Line:" . __LINE__ . " in class " . __CLASS__);
        }
        $remoteFile = $remoteDir . $filename;
        return ssh2_scp_send($this->connection, $localFile, $remotefile, $mode = 0644);
    }

    public function get($localfile, $remoteDir, $filename, $mode)
    {
        if ($this->connection === null) {
            if (!$this->connect()) {
                throw new RuntimeException("No SFTP Connection Established, did you call FtpConnection::connect() before trying to send your file?");
            }
        }

        if (!$this->login()) {
            throw new RuntimeException("Not Logged into the SFTP Sever.");
        }

        if (!$this->flist($remoteDir)) {
            throw new RuntimeException($remoteDir . " is not a directory. Line:" . __LINE__ . " in class " . __CLASS__);
        }

        $handle = @fopen($localfile, "w");
       
        
        $remoteFile = $remoteDir . $filename;

        ssh2_scp_recv($session, $remoteDir, $filename);
    }

    public function flist($remote)
    {
        try {
            $this->connect();
            $this->login();
        } catch (RuntimeException $e) {
            return false;
        }

        $sftp = $this->getSftp();

        $dirhandle = opendir("ssh2.sftp://{$sftp}/{$remote}");
        
        if ($dirhandle) {
            $list = array();
            while (false !== ($entry = readdir($dirhandle))) {
                array_push($list, $entry);
            }
            $this->disconnect();
            return $list;
        }

        return false;
    }

    public function connect()
    {
        $this->connection = @ssh2_connect($this->hostname, $this->port);
        if (!empty($this->connection)) {
            return true;
        }

        $this->connection = null;
        return false;
    }

    abstract public function login();

    public function disconnect()
    {
        $connection = $this->getConnection();
        if (!$connection) {
            return true;
        }

        if($this->exec("ECHO Exiting && exit;") === "Exiting") {
            $this->setConnection(null);
            return true;
        }

        return false;

    }

    public function exec($cmd)
    {
        if (!($stream = ssh2_exec($this->connection, $cmd))) {
            return false;
        }
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream, 4096)) {
            $data .= $buf;
        }
        fclose($stream);
        return trim($data, "\n");
    }
}
