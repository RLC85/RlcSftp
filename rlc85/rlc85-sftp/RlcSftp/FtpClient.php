<?php

namespace RlcSftp;

class FtpClient implements FtpInterface
{
	public $errors     = array();
	public $connection = null;
	const ASCII        = FTP_ASCII;
	const BINARY       = FTP_BINARY;
	const AUTOSEEK     = FTP_AUTOSEEK;
	const TIMEOUT_SEC  = FTP_TIMEOUT_SEC;

	public function connect($host, $port = 21, $timeout = 90)
	{
		$this->connection = @ftp_connect($host, $port, $timeout);

		if($this->connection === false) {
			throw new RuntimeException("Unable to connect to server with provided information.");
		}

		return true;
	}

	public function disconnect()
	{
		if(!$this->connected)
		{
			return true;
		}

		if(ftp_close($this->connection)) {
			return true;
		}

		array_push($this->errors, "Unable to disconnect from server");
		return false;
	}

	public function allocate($filesize)
	{
		if (! is_int($filesize)) {
			throw new InvalidArgumentException("Allocate requires integer parameter filesize. " . gettype($filesize) . " given. Line: " . __LINE__ . " in file " . __CLASS_ );
		}
		$result = "";
		$allocation = @ftp_alloc($this->connection);
		return true;
	}

	public function upOneDir()
	{

	}

	public function chdir($directory)
	{

	}

	public function chmod($mode, $filename)
	{

	}

	public function delete($path)
	{

	}

	public function exec($command)
	{

	}

	public function fget($handle, $remote_file, $mode = FtpClient::ASCII, $resumepos = 0)
	{

	}

	public function fput($remote_file, $handle, $mode = FtpClient::ASCII, $startpos = 0)
	{

	}

	public function getOption($option)
	{

	}

	public function get()
	{

	}

	public function login()
	{

	}

	public function lastModified()
	{

	}

	public function mkdir()
	{

	}

	public function listDir()
	{

	}

	public function passive()
	{

	}

	public function put()
	{

	}

	public function currentDir()
	{

	}

	public function rename()
	{

	}

	public function rmDir()
	{

	}

	public function setOption()
	{

	}

	public function size()
	{

	}

	public function sslConnect()
	{

	}

	public function getServerType()
	{

	}

	public function connected()
	{
		if(!empty($this->connection) && $this->connection !== false)
		{
			return true;
		}

		return false;
	}
}