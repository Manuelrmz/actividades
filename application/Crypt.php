<?php
class crypt
{
	private $clsKey = "";
	private $_s = array();
	public function __construct($key = "")
	{
		$this->setKey($key);
	}
	/**
	 * [Crypt]
	 * @param [string] $text
	 * 
	 * @return  [string] [Encrypted Text]
	 */
	private function Crypt($text)
	{
		$T = 0;
		$i = 0;
		$j = 0;
		$cadena = "";
		$this->createKeyArray();
		for($C = 0; $C < strlen($text); $C++)
		{
			$i = ($i + 1) & 255;
			$j = ($j + $this->_s[$i]) & 255;
			$T = $this->_s[$i];
			$this->_s[$i] = $this->_s[$j];
			$this->_s[$j] = $T;
			$T = ($this->_s[$i] + $this->_s[$j]) & 255;
			$cadena = $cadena . chr(ord(substr($text,$C,1)) ^ $this->_s[$T]);
		}
		return $cadena;
	}
	/**
	 * [Decrypt]
	 * @param [string] $text
	 * 
	 * @return  [string] [Decrypted Text]
	 */
	private function Decrypt($text)
	{
		$T = 0;
		$i = 0;
		$j = 0;
		$cadena = "";
		$this->createKeyArray();
		for($C = 0; $C < strlen($text); $C++)
		{
			$i = ($i + 1) & 255;
			$j = ($j + $this->_s[$i]) & 255;
			$T = $this->_s[$i];
			$this->_s[$i] = $this->_s[$j];
			$this->_s[$j] = $T;
			$T = ($this->_s[$i] + $this->_s[$j]) & 255;
			$cadena = $cadena . chr(ord(substr($text,$C,1)) ^ $this->_s[$T]);
		}
		return $cadena;
	}
	/**
	 * [createdKeyArray]
	 * 
	 * @return nothing
	 */
	private function createKeyArray()
	{
		$KeyLenght = 0;
		$T = 0;
		$i = 0;
		$j = 0;
		if($this->getKey() != "" && strlen(trim($this->getKey())) > 0)
		{
			$KeyLenght = strlen($this->getKey());
			for($i = 0; $i < 256; $i++)
			{
				$this->_s[$i] = $i;
			}
			for($i = 0; $i < 256; $i++)
			{
				$j = ($j + $this->_s[$i] + ord(substr($this->getKey(),($i % $KeyLenght),1)) & 255);
				$T = $this->_s[$i];
				$this->_s[$i] = $this->_s[$j];
				$this->_s[$j] = $T;
			}
		}
		else
			throw new Exception("Key didn't found", 1);
	}
	/**
	 * [ConvToInt]
	 * @param [string] $hexValue
	 *
	 * @return [integer] $temp
	 */
	private function ConvToInt($hexValue)
	{	
		$x1 = "";
		$x2 = "";
		$temp = 0;
		$x1 = substr($hexValue,0,1);
		$x2 = substr($hexValue,1,1);
		if(is_numeric ($x1))
			$temp = 16 * $x1;
		else
			$temp = (ord($x1) - 55) * 16;
		if(is_numeric ($x2))
			$temp = $temp + $x2;
		else
			$temp = $temp + (ord($x2) - 55);
		return $temp;
	}
	/**
	 * [ConvToHex]
	 * @param [integer] $intvalue
	 *
	 * @return [string] $currentValue
	 */
	private function ConvToHex($intvalue)
	{
		$currentValue = "";
		if($intvalue > 9)
			$currentValue = chr($intvalue + 55);
		else
			$currentValue .= $intvalue;
		return $currentValue;
	}
	/**
	 * [encriptar]
	 * @param [string] $text
	 *
	 * @return [string] $temp
	 */
	private function encriptar($text)
	{
		$x = 0;
		$temp = "";
		$tempNum = "";
		$tempChar = "";
		$tempChar2 = "";
		for($x; $x < strlen($text); $x++)
		{
			$tempChar2 = substr($text,$x,1);
			$tempNum = (integer)(ord($tempChar2) / 16);
			if(($tempNum * 16) < ord($tempChar2))
			{	
				$tempChar = $this->ConvToHex(ord($tempChar2) - ($tempNum * 16));
				$temp .= $this->ConvToHex($tempNum) . $tempChar;
			}
			else
				$temp .= $this->ConvToHex($tempNum) . '0';
		}
		return $temp;
	}
	/**
	 * [desencriptar]
	 * @param [string] $text
	 *
	 * @return [string] $temp
	 */
	private function desencriptar($text)
	{
		$temp = "";
		$HexByte = "";
		for($i = 0; $i < strlen($text); $i+=2)
		{
			$HexByte = substr($text,$i,2);
			$temp = $temp . chr($this->ConvToInt($HexByte));
		}
		return $temp;
	}
	/**
	 * [DecryptString]
	 * 
	 * @param [string] $cadena
	 * @param [string] $key
	 *
	 * @return [string] $decryptString
	 */
	public function DecryptString($cadena,$key)
	{
		$this->setKey($key);
		$AuxDec = $this->desencriptar($cadena);
		$decryptString = $this->Decrypt($AuxDec);
		return $decryptString;
	}
	/**
	 * [CryptString]
	 * 
	 * @param [string] $cadena
	 * @param [string] $key
	 *
	 * @return [string] $encryptedString
	 */
	public function CryptString($cadena,$key)
	{
		$encryptedString = "";
		$this->setKey($key);
		if(trim($cadena) !== "")
		{
			$encryptedString  = $this->Crypt($cadena);
		}
		$encryptedString = $this->encriptar($encryptedString);
		return $encryptedString;
	}
	/**
	 * @return [string] $clsKey
	 */
	public function getKey()
	{
		return $this->clsKey;
	}
	/**
	 * @param [string] $key
	 * @return  [string] [Key Value as string]
	 */
	public function setKey($key)
	{
		$this->clsKey = (string)$key;
	}
}
?>