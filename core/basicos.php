<?php
class basicos
{
	public static function getDaysFromWeek($anio,$semana,$diaExtra = false)
	{
		$dto = new DateTime();
		$dto->setISODate($anio,$semana);
		$ret['week_start'] = $dto->format('Y-m-d');
		$dto->modify('+6 days');
		if($diaExtra)
			$dto->modify('+1 days');
		$ret['week_end'] = $dto->format('Y-m-d');
		return $ret;
	}
	public static function getRealIP()
  {
      if (!empty($_SERVER['HTTP_CLIENT_IP']))
          return $_SERVER['HTTP_CLIENT_IP'];

      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
          return $_SERVER['HTTP_X_FORWARDED_FOR'];

      return $_SERVER['REMOTE_ADDR'];
  }
}
?>
