<?php if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if (isset($_POST['votename']))
{
	$errorMessage	= NULL;
	$votename		= $params->get('votename');
	$voteurl		= str_replace("www.", "", $params->get('voteurl'));
	$voteinterval 	= (int) $params->get('voteinterval');
	$votepoints		= (int) $params->get('votepoints');
	$imageurl		= str_replace("www.", "", $params->get('imageurl'));
	$uploadimg		= @$_FILES['uploadimg'];
	$imgtypes		= Flux::config('AllowedImgType')->toArray();
	$vfp_sites		= Flux::config('FluxTables.vfp_sites');
	$filename		= NULL;

	//
	$from = "SELECT id FROM $server->loginDatabase.$vfp_sites ";
	$sql = $from."WHERE votename = ?";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($votename));
	$votenameExists = $sth->rowCount();

	//
	$sql = $from."WHERE voteurl = ?";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($voteurl));
	$voteurlExists = $sth->rowCount();

	if ($votenameExists)
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
	// voteurl is already exists
	if ($voteurlExists)
		$errorMessage = Flux::message("VoteUrlExists");
	else
	// voteurl is not a valid url
	if (!filter_var($voteurl, FILTER_VALIDATE_URL))
		$errorMessage = sprintf(Flux::message("InvalidURL"), 'Vote URL');
	else
	// voteinterval is invalid
	if ($voteinterval < Flux::config('VoteIntervalMin') && $voteinterval > Flux::config('VoteIntervalMax'))
		$errorMessage = sprintf(Flux::message("InvalidVoteInterval"), Flux::config('VoteIntervalMin'), Flux::config('VoteIntervalMax'));
	else
	// votepoints is invalid
	if ($votepoints < Flux::config('VotePointsMin') && $votepoints > Flux::config('votePointsMax'))
		$errorMessage = sprintf(Flux::message("InvalidVotePoints"), Flux::config('VotePointsMin'), Flux::config('VotePointsMax'));
	else
	// imageurl is not a valid url
	if ($imageurl !== "" && !filter_var($imageurl, FILTER_VALIDATE_URL))
		$errorMessage = sprintf(Flux::message("InvalidURL"), 'Image URL');
	else
	// uploadimg has an error
	if ($uploadimg['error'] > 0 && $imageurl === "")
		$errorMessage = Flux::message("UploadImageOrImageURL");
	else
	// voteurl is not given. fetch the image instead
	if ($imageurl === "")
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
			if (!move_uploaded_file($uploadimg['tmp_name'], $filepath.$filename))
				$errorMessage = Flux::message("FailedToUpload");
		}
	}

	if (is_null($errorMessage))
	{
		$sql = "INSERT INTO $server->loginDatabase.$vfp_sites VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
		$sth = $server->connection->getStatement($sql);

		if ($imageurl === "")
			$imageurl = NULL;

		if ($uploadimg['error'] > 0)
			$uploadimg = NULL;

		$bind = array($votename, $voteurl, $voteinterval, $votepoints, $filename, $imageurl, date('Y-m-d H:i:s'));

		if ($sth->execute($bind))
			$this->redirect($this->url('voteforpoints', 'list'));
		else
			$errorMessage = Flux::message("FailedToAdd");
	}

}

?>
