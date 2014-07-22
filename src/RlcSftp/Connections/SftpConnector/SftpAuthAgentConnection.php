<?php

namespace RlcSftp\Connections\SftpConnector;

use \InvalidArgumentException;
use \RuntimeException;

class SftpAuthAgentConnection extends AbstractSftpConnection
{
    public function login($username = null)
    {
        if (empty($this->connection)) {
            $this->connect();
        }
        
        $u = $this->username;

        if (!empty($username)) {
            $u = $username;
        }
        
        ssh2_auth_agent($this->connection, $u);
        $this->setSftp();
    }
}
