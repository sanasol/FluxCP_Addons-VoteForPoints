<?php if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

require_once("function.php");
$unavailable 	= NULL;
$errorMessage	= NULL;
$vfp_sites		= Flux::config('FluxTables.vfp_sites');
$vfp_logs		= Flux::config('FluxTables.vfp_logs');
$id 			= (int) $params->get('id');

if (isset($_POST['votename']))
{
	$voteid			= $params->get('voteid');
	$votename		= $params->get('votename');
	$voteurl		= str_replace("www.", "", $params->get('voteurl'));
	$voteinterval 	= (int) $params->get('voteinterval');
	$votepoints		= (int) $params->get('votepoints');
	$imageurl		= str_replace("www.", "", $params->get('imageurl'));
	$uploadimg		= @$_FILES['uploadimg'];
	$imgtypes		= Flux::config('AllowedImgType')->toArray();
	$filename		= NULL;
	$hasUpdate		= NULL;

	// votename value has changed
	if (isChanged($voteid, "votename", $votename, $server))
	{
		$sql = "SELECT id FROM $server->loginDatabase.$vfp_sites WHERE votename = ? LIMIT 1";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($votename));

		// votename is already exists
		if ($sth->rowCount())
			$errorMessage = Flux::message("VoteNameExists");
		else
		// look for alphanumeric, underscore and white space characters
		if (!preg_match(Flux::config('AlphaNumSpaceRegex'), $votename))
			$errorMessage = Flux::message("InvalidVoteNameFormat");
		else
		// votename has an invalid length
		if (strlen($votename) > Flux::config('VoteNameMax') && strlen($votename) < Flux::config('VoteNameMin'))
			$errorMessage = sprintf(Flux::message("InvalidVoteNameLength"), Flux::config('VoteNameMin'), Flux::config('VoteNameMax'));
		else
		// failed to update the value
		if (!updateValue($voteid, "votename", $votename, $server))
			$errorMessage = sprintf(Flux::message("FailedToUpdate"), "Vote Name");
		else
			$hasUpdate = TRUE;
	}

	// voteurl value has changed
	if (is_null($errorMessage) && isChanged($voteid, "voteurl", $voteurl, $server))
	{
		$sql = "SELECT id FROM $server->loginDatabase.$vfp_sites WHERE voteurl = ? LIMIT 1";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($voteurl));

		// voteurl is already exists
		if ($sth->rowCount())
			$errorMessage = Flux::message("VoteUrlExists");
		else
		// voteurl is not a valid url
		if (!filter_var($voteurl, FILTER_VALIDATE_URL))
			$errorMessage = sprintf(Flux::message("InvalidURL"), 'Vote URL');
		else
		// failed to update the value
		if (!updateValue($voteid, "voteurl", $voteurl, $server))
			$errorMessage = sprintf(Flux::message("FailedToUpdate"), "Vote URL");
		else
			$hasUpdate = TRUE;
	}

	// voteinterval value has changed
	if (is_null($errorMessage) && isChanged($voteid, "voteinterval", $voteinterval, $server))
	{
		// voteinterval is invalid
		if ($voteinterval < Flux::config('VoteIntervalMin') && $voteinterval > Flux::config('VoteIntervalMax'))
			$errorMessage = sprintf(Flux::message("InvalidVoteInterval"), Flux::config('VoteIntervalMin'), Flux::config('VoteIntervalMax'));
		else
		// failed to update the value
		if (!updateValue($voteid, "voteinterval", $voteinterval, $server))
			$errorMessage = sprintf(Flux::message("FailedToUpdate"), "Voting Interval");
		else
			$hasUpdate = TRUE;
	}

	// votepoints value has changed
	if (is_null($errorMessage) && isChanged($voteid, "votepoints", $votepoints, $server))
	{
		// votepoints is invalid
		if ($votepoints < Flux::config('VotePointsMin') && $votepoints > Flux::config('votePointsMax'))
			$errorMessage = sprintf(Flux::message("InvalidVotePoints"), Flux::config('VotePointsMin'), Flux::config('VotePointsMax'));
		else
		// failed to update the value
		if (!updateValue($voteid, "votepoints", $votepoints, $server))
			$errorMessage = sprintf(Flux::message("FailedToUpdate"), "Vote Points");
		else
			$hasUpdate = TRUE;
	}

	// imageurl value has changed
	if (is_null($errorMessage) && isChanged($voteid, "imgurl", $imageurl, $server) && $imageurl !== "")
	{
		// imageurl is not a valid url
		if (!filter_var($imageurl, FILTER_VALIDATE_URL))
			$errorMessage = sprintf(Flux::message("InvalidURL"), 'Image URL');
		else
		// failed to update the value
		if (!updateValue($voteid, "imgurl", $imageurl, $server))
			$errorMessage = sprintf(Flux::message("FailedToUpdate"), "Image URL");
		else
			$hasUpdate = TRUE;
	}

	// updating imagename
	if (is_null($errorMessage) && $imageurl === "" && $uploadimg['error'] === 0)
	{
		$ext = explode(".", $uploadimg['name']);
		$ext = end($ext);

		// invalid image type
		if (!preg_match("/image\//", $uploadimg['type']) &&
			!in_array(str_replace("image/", "", $uploadimg['type']), $imgtypes) &&
			!in_array($ext, $imgtypes))
			$errorMessage = Flux::message("InvalidImageType");
		else
		// invalid file size
		if ($uploadimg['size'] > Flux::config('MaxFileSize')*1024)
			$errorMessage = sprintf(Flux::message("InvalidFileSize"), Flux::config('MaxFileSize'));
		else
		// invalid image
		if (!$size = getimagesize($uploadimg['tmp_name']))
			$errorMessage = Flux::message("InvalidImageType");
		else
		// invalid image size
		if ($size[0] > Flux::config('ImageMaxWidth') || $size[1] > Flux::config('ImageMaxHeight'))
			$errorMessage = sprintf(Flux::message("InvalidImageSize"), Flux::config('ImageMaxWidth'), Flux::config('ImageMaxHeight'));
		else
		{
			$filename = time()."_".md5(time().$server->serverName).".".$ext;
			$filepath = FLUX_DATA_DIR.'/'.Flux::config('ImageUploadPath').'/';
            if (!is_dir($filepath)) {
                mkdir($filepath);
            }

			// failed to upload the image
			if (is_dir($filepath) && !move_uploaded_file($uploadimg['tmp_name'], $filepath.$filename))
				$errorMessage = Flux::message("FailedToUpload");
			else {
				if (!updateValue($voteid, "imgname", $filename, $server))
					$errorMessage = sprintf(Flux::message("FailedToUpdate"), "Image URL");
				else
					$hasUpdate = TRUE;
			}
		}
	}

	if ($hasUpdate === TRUE)
		$successMessage = Flux::message("VotingSiteUpdated");
	else
	if (is_null($errorMessage))
		$errorMessage = Flux::message("NoChangesMade");
}


if (isset($_GET['id']))
{
	$sql = "SELECT * FROM $server->loginDatabase.$vfp_sites WHERE id = ? LIMIT 1";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($id));

	if ($sth->rowCount())
		$votesites_res = $sth->fetch();
	else
		$unavailable = TRUE;

} else {
	$unavailable = TRUE;
}
?>
