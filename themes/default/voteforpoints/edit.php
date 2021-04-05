<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('EditVoteHeading')) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php elseif (!empty($successMessage)): ?>
	<p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>
<?php if (is_null($unavailable)): ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form" enctype="multipart/form-data">
	<input type="hidden" name="voteid" value="<?php echo (int) $votesites_res->id ?>" />
	<table class="generic-form-table">
		<tr>
			<th><label for="votename"><?php echo htmlspecialchars(Flux::message('VoteNameLabel')) ?></label></th>
			<td><input type="text" name="votename" id="votename" value="<?php echo ($votesites_res->votename !== "" ? htmlspecialchars($votesites_res->votename) : ($params->get('votename') !== "" ? $params->get('votename') : "")) ?>" /></td>
		</tr>
		<tr>
			<th><label for="voteurl"><?php echo htmlspecialchars(Flux::message('VoteUrlLabel')) ?></label></th>
			<td><input type="text" name="voteurl" id="voteurl" value="<?php echo ($votesites_res->voteurl !== "" ? htmlspecialchars($votesites_res->voteurl) : ($params->get('voteurl') !== "" ? $params->get('voteurl') : "")) ?>" /> <span><?php echo htmlspecialchars(Flux::message('VoteUrlNote')) ?></span></td>
		</tr>
		<tr>
			<th><label for="voteinterval"><?php echo htmlspecialchars(Flux::message('VoteIntervalLabel')) ?></label></th>
			<td><input type="number" name="voteinterval" id="voteinterval" value="<?php echo ($votesites_res->voteurl !== "" ? htmlspecialchars($votesites_res->voteinterval) : ($params->get('voteinterval') !== "" ? $params->get('voteinterval') : Flux::config('DefaultIntervalVoting'))) ?>" /> <span><?php echo htmlspecialchars(Flux::message('VoteIntervalNote')) ?></span></td>
		</tr>
		<tr>
			<th><label for="votepoints">Reward <?php echo htmlspecialchars(Flux::message('VotePointsLabel').'/'.Flux::message('CashPointsLabel')) ?></label></th>
			<td><input type="number" name="votepoints" id="votepoints" value="<?php echo ($votesites_res->votepoints !== "" ? htmlspecialchars($votesites_res->votepoints) : ($params->get('votepoints') !== "" ? $params->get('votepoints') : Flux::config('DefaultVotePoints'))) ?>" /></td>
		</tr>
		<tr>
			<th><label for="imageurl"><?php echo htmlspecialchars(Flux::message('ImageUrlLabel')) ?></label></th>
			<td><input type="text" name="imageurl" placeholder="optional" id="imageurl" value="<?php echo ($votesites_res->imageurl !== "" ? htmlspecialchars($votesites_res->imageurl) : ($params->get('imageurl') !== "" ? $params->get('imageurl') : "")) ?>" /> <span><?php echo htmlspecialchars(Flux::message('ImageUrlNote')) ?></span></td>
		</tr>
		<tr>
			<th><label for="uploadimg"><?php echo htmlspecialchars(Flux::message('UploadImageLabel')) ?></label></th>
			<td><img title='<?php htmlspecialchars($votesites_res->votename) ?>' src="<?php echo (is_null($votesites_res->imgurl) ? $this->themePath('img/').Flux::config('ImageUploadPath').'/'.$votesites_res->imgname : $votesites_res->imgurl) ?>" /> <input type="file" name="uploadimg" id="uploadimg"> <span><?php echo htmlspecialchars(Flux::message('UploadImageNote')) ?></span></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="<?php echo htmlspecialchars(Flux::message('VoteEditButton')) ?>" />
			</td>
		</tr>
	</table>
</form>
<?php else: ?>
<p class='red'><?php htmlspecialchars(Flux::message('EditVoteInvalidID')) ?></p>
<?php endif ?>
