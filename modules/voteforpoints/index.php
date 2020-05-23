<?php if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

require_once("function.php");
$vfp_sites		= Flux::config('FluxTables.vfp_sites');
$vfp_logs		= Flux::config('FluxTables.vfp_logs');
$cp_tbl			= Flux::config('FluxTables.cashpoints');
$errorMessage	= NULL;

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

if (isset($_REQUEST['id']))
{
    $id 		= (int) $params->get('id');
    $ip 		= getUserIP();
    $account_id = (int) $session->account->account_id;

    $sql = "SELECT * FROM $server->loginDatabase.$vfp_sites WHERE id = ?";
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($id));
    $res = $sth->fetch();

    // voting site doesn't exists
    if ( ! $sth->rowCount())
    {
        $errorMessage = Flux::message("VoteDontExists");
    } else

        // validate for ip address
        if (Flux::config('EnableIPVoteCheck'))
        {
            $sql = "SELECT timestamp_expire FROM $server->loginDatabase.$vfp_logs WHERE ipaddress = ? AND sites_id = ? AND UNIX_TIMESTAMP(timestamp_expire) > ? LIMIT 1";
            $sth = $server->connection->getStatement($sql);
            $bind = array($ip, $id, time());
            $sth->execute($bind);

            if ($sth->rowCount())
                $errorMessage = Flux::message("AlreadyVoted");
        }

        // validate for account_id
        if (is_null($errorMessage))
        {
            $sql = "SELECT timestamp_expire FROM $server->loginDatabase.$vfp_logs WHERE account_id = ? AND sites_id = ? AND UNIX_TIMESTAMP(timestamp_expire) > ? LIMIT 1";
            $sth = $server->connection->getStatement($sql);
            $bind = array($account_id, $id, time());
            $sth->execute($bind);

            if ($sth->rowCount())
            {
                $errorMessage = Flux::message("AlreadyVoted");
            } else {
                // update the existing row
                $sql = "UPDATE $server->loginDatabase.$vfp_logs SET timestamp_expire = ?, timestamp_voted = ?, ipaddress = ? WHERE account_id = ? AND sites_id = ?";
                $sth = $server->connection->getStatement($sql);
                $bind = array(
                    date('Y-m-d H:i:s', strtotime("+".$res->voteinterval." hours")),
                    date('Y-m-d H:i:s'),
                    $ip,
                    $account_id,
                    $id
                );
                $sth->execute($bind);

                if ( ! $sth->rowCount())
                {
                    // insert new row
                    $sql = "INSERT INTO $server->loginDatabase.$vfp_logs VALUES (NULL, ?, ?, ?, ?, ?)";
                    $sth = $server->connection->getStatement($sql);
                    $bind = array(
                        $id,
                        date('Y-m-d H:i:s', strtotime("+".$res->voteinterval." hours")),
                        date('Y-m-d H:i:s'),
                        $ip,
                        $account_id
                    );
                    $sth->execute($bind);

                    if ( ! $sth->rowCount())
                    {
                        $errorMessage = sprintf(Flux::message("UnableToVote"), 2);
                    } else {

                        switch (Flux::config('PointsType'))
                        {
                            case "vote":
                                // update votepoints
                                $sql = "UPDATE $server->loginDatabase.cp_createlog SET votepoints = votepoints + ? WHERE account_id = ?";
                                $sth = $server->connection->getStatement($sql);
                                $sth->execute(array((int) $res->votepoints, $account_id));
                                break;

                            case "cash":
                                // insert or update cashpoints
                                $cashpoints_var = "#CASHPOINTS";
                                $sql = "select value from $cp_tbl WHERE `key` = ? AND account_id = ?";
                                $sth = $server->connection->getStatement($sql);
                                $sth->execute(array($cashpoints_var, $account_id));

                                // account doesn't have a record for cashpoints
                                // so we will add a row
                                if (!$sth->rowCount())
                                {
                                    $sql = "INSERT INTO $cp_tbl (`account_id`, `key`, `index`, `value`) VALUES (?, ?, 0, ?)";
                                    $sth = $server->connection->getStatement($sql);
                                    $bind = array($account_id, $cashpoints_var, $res->votepoints);
                                    $sth->execute($bind);

                                    if ( ! $sth->rowCount())
                                        $errorMessage = sprintf(Flux::message("UnableToVote"), 4);
                                } else {
                                    $sql = "UPDATE $cp_tbl SET `value` = `value` + ? WHERE `key` = ? AND account_id = ?";
                                    $sth = $server->connection->getStatement($sql);
                                    $sth->execute(array((int) $res->votepoints, $cashpoints_var, $account_id));
                                }
                                break;

                            default:
                                // update credits row
                                $sql = "select balance from $server->loginDatabase.cp_credits WHERE account_id = ?";
                                $sth = $server->connection->getStatement($sql);
                                $sth->execute(array($account_id));
                                if (!$sth->rowCount())
                                {
                                    // insert new credits row
                                    $sql = "INSERT INTO $server->loginDatabase.cp_credits VALUES (?, ?, NULL, NULL)";
                                    $sth = $server->connection->getStatement($sql);
                                    $sth->execute(array($account, $res->votepoints));

                                    if ( ! $sth->rowCount())
                                        $errorMessage = sprintf(Flux::message("UnableToVote"), 6);
                                } else {
                                    $sql = "UPDATE $server->loginDatabase.cp_credits SET balance = balance + ? WHERE account_id = ?";
                                    $sth = $server->connection->getStatement($sql);
                                    $sth->execute(array((int) $res->votepoints, $account_id));
                                }
                                break;
                        }

                        header('Location: '.$res->voteurl);
                        exit();
                    }
                }
            }
        }
}

// fetch all voting sites
$sql = "SELECT * FROM $server->loginDatabase.$vfp_sites";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$votesites_res = $sth->fetchAll();

?>
