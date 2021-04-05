<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(sprintf(Flux::message('ListVoteHeading'), $server->serverName)) ?></h2>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php elseif (!empty($successMessage)): ?>
	<p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>
<?php if (count($votesites_res) === 0): ?>
	<p class='message'><?php echo Flux::message('NoVotingSiteYet') ?></p>
<?php else: ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<table class="horizontal-table vote-table">
		<tr>
			<th>Voting Site</td>
			<th>Points</th>
			<th>Vote Time Interval</th>
			<th>Site Added</th>
			<th>Actions</th>
		</tr>
		<?php foreach ($votesites_res as $row): ?>
		<tr>
			<td style="text-align:center">
				<img title='<?php echo htmlspecialchars($row->votename) ?>' src="<?php echo (is_null($row->imgurl) ? '/'.FLUX_DATA_DIR.'/'.Flux::config('ImageUploadPath').'/'.$row->imgname : $row->imgurl) ?>" />
			</td>
			<td style="text-align:center"><?php echo number_format($row->votepoints) ?></td>
			<td style="text-align:center"><?php echo $row->voteinterval." ".((int) $row->voteinterval > 1 ? "Hours" : "Hour") ?></td>
			<td style="text-align:center"><?php echo date(Flux::config("DateFormat"), strtotime($row->datetime_created)) ?></td>
			<td style="text-align:center"><button type="submit" name="id" value="<?php echo (int) $row->id ?>" onclick="if(!confirm('Are you sure about this?')) return false;">Delete</button> | <button type='button' onclick="window.open('<?php echo $this->url('voteforpoints', 'edit').(Flux::config('UseCleanUrls') ? "?id=".$row->id : "&id=".$row->id) ?>');">Edit</button></td>
		</tr>
		<?php endforeach ?>
	</table>
</form>
<script type="text/javascript">
	$(function() {
		$('.vote-button').click(function() {
			var id = $(this).val();
			var vote_sites = new Array();

			<?php foreach ($votesites_res as $row): ?>
				vote_sites[<?php echo $row->id ?>] = "<?php echo htmlspecialchars($row->voteurl) ?>";
			<?php endforeach ?>

			window.open(vote_sites[id]);
		});
	});
</script>
<?php endif ?>
