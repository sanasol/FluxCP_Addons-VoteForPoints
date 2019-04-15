<?php if (!defined('FLUX_ROOT')) exit;
return array(
	'DefaultThemeName'		=> 'default',
	'PointsType'			=> "cash", // credit, vote, cash
	'DefaultIntervalVoting' => 12,
	'DefaultVotePoints' 	=> 0,
	'VoteNameMax'			=> 15,
	'VoteNameMin' 			=> 6,
	'VotePointsMin'			=> 0,
	'VotePointsMax'			=> 999,
	'VoteIntervalMin'		=> 1,
	'VoteIntervalMax'		=> 24,
	'AllowedImgType'		=> array('jpg', 'jpeg', 'png', 'gif'),
	'MaxFileSize'			=> 500, // KB,
	'ImageMaxWidth'			=> 150,
	'ImageMaxHeight'		=> 150,
	'ImageUploadPath'		=> "votes", // /themes/default/img/votes/
	'EnableIPVoteCheck'		=> true,
	'AlphaNumSpaceRegex'	=> "/^[A-Za-z0-9_\s]+$/",

	'MenuItems'	=> array(
		'Other'	=> array(
			'Vote for Points' => array(
				'module' => 'voteforpoints'
			)
		)
	),

	'SubMenuItems'	=> array(
		'voteforpoints'	=> array(
			'index' => 'Vote',
			'add' => 'Add Voting Site',
			'list' => 'List Voting Sites',
			'log' => 'Voters Log',
		)
	),

	'FluxTables'	=> array(
		'vfp_logs' => 'cp_vfp_logs',
		'vfp_sites' => 'cp_vfp_sites',
		'cashpoints' => 'acc_reg_num'
	)
)
?>
