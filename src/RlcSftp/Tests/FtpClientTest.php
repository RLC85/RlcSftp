<?php

namespace RlcSftp\Tests;

use RlcSftp\FtpClient;

class FtpClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerConnect
     */
    public function testConnect($host,$port = 21,$timeout = 90)
    {
        $this->ftpClient = new FtpClient();
        $this->assertEquals("boolean",gettype($this->ftpClient->connect($host,$port,$timeout)));
    }

    public function providerConnect()
    {
        return array(
            array(
                "localhost",
                21,
                5
            ),
            array(
                "localhost",
            ),
            array(
                "deadendage.com",
                21,
                5
            )
        );
    }

    /**
     * @depends testConnect
     */
    public function testDisconnect()
    {
        $this->ftpClient = new FtpClient();
        $this->ftpClient->connect("localhost",21,10);
        $this->assertEquals("boolean", gettype($this->ftpClient->disconnect()));
    }

    /**
     * @dataProvider providerAlloc
     */
    public function testAlloc($filesize) {
        $this->ftpClient = new FtpClient();
        $this->ftpClient->connect("localhost");
        $this->assertEquals("boolean", gettype($this->ftpClient->allocate($filesize)));
    }

    /**
     * prodicerAlloc
     * dataProvider for the testAlloc function
     */
    public function providerAlloc()
    {
        return array(
            array(1048),
            array("abc"),
            array(null),
            array(false)
        );
    }

    public function testUpOneDir()
    {

    }

    public function testChdir()
    {

    }

    public function testChmod()
    {

    }

    public function testDelete()
    {

    }

    public function testExec()
    {

    }

    public function testFget()
    {

    }

    public function testFput()
    {

    }

    public function testGetOption()
    {

    }

    public function testGet()
    {

    }

    public function testLogin()
    {

    }

    public function testLastModified()
    {

    }

    public function testMkdir()
    {

    }

    public function testListDir()
    {

    }


    public function testPassive()
    {

    }

    public function testPut()
    {

    }

    public function tesrCurrentDir()
    {

    }

    public function testRename()
    {

    }

    public function testRmDir()
    {

    }

    public function testSetOption()
    {

    }

    public function testSize()
    {

    }

    public function testSslConnect()
    {

    }

    public function testServerType()
    {

    }
    
    /**
     * @depends testConnect
     */
    public function testConnected()
    {
        $this->ftpClient = new FtpClient();
        $this->assertFalse($this->ftpClient->connected());

        $this->ftpClient->connect("localhost");
        $this->assertTrue($this->ftpClient->connected());
    }
}