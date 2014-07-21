<?php

namespace RlcSftp\Tests\Connections\FtpConnector;

use \PHPUnit_Framework_TestCase;
use RlcSftp\Connections\FtpConnector\FtpConnection;

class FtpConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerFtp
     */
    public function testConnect($hostname, $port = 21, $timeout = 90)
    {
        $ftp = new FtpConnection($hostname, $port, $timeout);
        if ($hostname === "localhost") {
            $this->assertFalse($ftp->connect());
        }

        if ($hostname === "ec2-50-19-37-183.compute-1.amazonaws.com") {
            if ($port === 21) {
                $this->assertTrue($ftp->connect());
            } else {
                $this->assertFalse($ftp->connect());
            }
        }
    }

    public function providerFtp()
    {
        return array(
            array(
                "localhost"
            ),
            array(
                "ec2-50-19-37-183.compute-1.amazonaws.com",
                21,
                90
            ),
            array(
                "ec2-50-19-37-183.compute-1.amazonaws.com",
                25677
            )
        );
    }

    /**
     * @dataProvider providerFtp
     * @depends testConnect
     */
    public function testDisconnect($hostname, $port = 21, $timeout = 90)
    {
        $ftp = new FtpConnection($hostname, $port, $timeout);
        $ftp->connect();
        if ($hostname === "localhost") {
            $this->assertTrue($ftp->disconnect());
        }

        if ($hostname === "ec2-50-19-37-183.compute-1.amazonaws.com") {
            if ($port === 21) {
                $this->assertTrue($ftp->disconnect());
            } else {
                $this->assertTrue($ftp->disconnect());
            }
        }
    }

    /**
     * @dataProvider providerLogin
     * @depends testConnect
     * @expectedException RuntimeException
     */
    public function testLogin($username, $password, $connect = true)
    {
        $ftp = new FtpConnection("ec2-50-19-37-183");
       
        if ($username === "rich" && $password === "P@ssWord") {
            $ftp->connect();
            $this->assertTrue($ftp->login());
            $ftp->disconnect();
        }

        if($username !== "rich") {
            $ftp->connect();
            $this->assertFalse($ftp->login());
            $this->disconnect();
        }

        if (!$connect) {
           $this->assertFalse($ftp->login());
        }
    }

    public function providerLogin()
    {
        return array(
            array(
            ),
            array(
            ),
        );
    }
}
