<?php

$this->loginRequired();

if (!function_exists("isChanged"))
{
	function isChanged($id, $col, $row, $server)
	{
		$vfp_sites = Flux::config('FluxTables.vfp_sites');

		$sql = "SELECT * FROM $server->loginDatabase.$vfp_sites WHERE `$col` = ? AND `id` = ?";
		$sth = $server->connection->getStatement($sql);
		$bind = array($row, (int) $id);
		$sth->execute($bind);

		if ($sth->rowCount())
			return FALSE;
		else
			return TRUE;
	}
}

if (!function_exists("updateValue"))
{
	function updateValue($id, $col, $row, $server)
	{
		$vfp_sites = Flux::config('FluxTables.vfp_sites');

		$sql = "UPDATE $server->loginDatabase.$vfp_sites SET `$col` = ? WHERE `id` = ?";
		$sth = $server->connection->getStatement($sql);
		$bind = array($row, (int) $id);
		$sth->execute($bind);

		if ($sth->rowCount())
			return TRUE;
		else
			return FALSE;
	}
}

if (!function_exists("isVoted"))
{
	function isVoted($id, $server)
	{
		$vote_id 	= (int) $id;
		$vfp_logs 	= Flux::config("FluxTables.vfp_logs");
		$ipaddress 	= $_SERVER['REMOTE_ADDR'];
		$account_id = (int) $server->cart->account->account_id;

		if (Flux::config('EnableIPVoteCheck'))
		{
			$sql = "SELECT `timestamp_expire` FROM $server->loginDatabase.$vfp_logs WHERE `ipaddress` = ? AND `sites_id` = ? AND UNIX_TIMESTAMP(timestamp_expire) > ? LIMIT 1";
			$sth = $server->connection->getStatement($sql);
			$bind = array($ipaddress, $vote_id, time());
			$sth->execute($bind);

			if ($sth->rowCount())
				return $sth->fetch()->timestamp_expire;
//			else
//				return FALSE;
		}

		$sql = "SELECT `timestamp_expire` FROM $server->loginDatabase.$vfp_logs WHERE `account_id` = ? AND `sites_id` = ? AND UNIX_TIMESTAMP(timestamp_expire) > ? LIMIT 1";
		$sth = $server->connection->getStatement($sql);
		$bind = array($account_id, $vote_id, time());
		$sth->execute($bind);

		if ($sth->rowCount())
			return $sth->fetch()->timestamp_expire;
		else
			return FALSE;
	}
}

if (!function_exists("getCashPoints"))
{
	function getCashPoints($account_id, $server)
	{
		$cp_tbl = Flux::config('FluxTables.cashpoints');
		$sql = "SELECT `value` FROM `$cp_tbl` WHERE `account_id` = ? AND `key` = '#CASHPOINTS'";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array((int) $account_id));

		return $sth->rowCount() ? (int) $sth->fetch()->value : 0;
	}
}

if (!function_exists("getTimeLeft"))
{
	function getTimeLeft($ts)
	{
		if (strtotime($ts) < time())
			return FALSE;

		$time = strtotime($ts) - time();

		$temp = $time / 86400;

		// set days
		$days = floor($temp);
		$temp = 24*($temp-$days);

		// set hours
		$hours = floor($temp);
		$temp = 60*($temp-$hours);

		// set minutes
		$minutes = floor($temp);
		$temp = 60*($temp-$minutes);

		// set seconds
		$seconds = floor($temp);

		if ($days > 0)
			return $days.($days > 1 ? " days left" : " day left");
		else
		if ($hours > 0)
			return $hours.($hours > 1 ? " hours left" : " hour left");
		else
		if ($minutes > 0)
			return $minutes.($minutes > 1 ? " minutes left" : " minute left");
		else
			return $seconds.($seconds > 1 ? " seconds left" : " second left");
	}
}

?>
