<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(sprintf(Flux::message('VoteHeading'), $server->serverName)) ?></h2>
<p class='message'><?php echo htmlspecialchars(Flux::message("VoteNotice")) ?></p>
<?php if (!empty($errorMessage)): ?>
	<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php elseif (!empty($successMessage)): ?>
	<p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<?php if (Flux::config('PointsType') == 'cash'): ?>
	<p><?php echo sprintf(Flux::message('CurrentCashPoints'), number_format(getCashPoints($session->account->account_id, $server))) ?></p>
<?php endif ?>

<?php if (count($votesites_res) !== 0): ?>
    <table class="horizontal-table vote-table">
        <tr>
            <th>Voting Site</td>
            <th>Points</th>
            <th>Vote Time Interval</th>
            <th>Time Left</th>
        </tr>
        <?php foreach ($votesites_res as $row): ?>
            <tr>
                <td style="text-align:center">
                    <a
                            <?php
                            if (isVoted($row->id, $server) === FALSE) {
                                $url = $this->urlWithQs;
                                if(strpos($url, '?') !== false) {
                                    $url .= '&id='.$row->id;
                                } else {
                                    $url .= '?id='.$row->id;
                                }
                                echo 'target="_blank" href="'. $url .'"';
                            }
                            ?>
                            class="vote-button"
                            style="<?php echo (isVoted($row->id, $server) !== FALSE ? "cursor:not-allowed;": "cursor:pointer;") ?>">
                        <img <?php echo (isVoted($row->id, $server) !== FALSE ? "style='opacity:0.3;filter:alpha(opacity=30)' ": "") ?>title='<?php echo htmlspecialchars($row->votename) ?>' src="<?php echo (is_null($row->imgurl) ? $this->themePath('img/').Flux::config('ImageUploadPath').'/'.$row->imgname : $row->imgurl) ?>" />
                    </a>
                </td>
                <td style="text-align:center"><?php echo number_format($row->votepoints) ?></td>
                <td style="text-align:center"><?php echo $row->voteinterval." ".((int) $row->voteinterval > 1 ? "Hours" : "Hour") ?></td>
                <td style="text-align:center"><?php echo (isVoted($row->id, $server) !== FALSE ? getTimeLeft(isVoted($row->id, $server)) : Flux::message('VoteNow')) ?></td>
            </tr>
        <?php endforeach ?>
    </table>

<script type="text/javascript">
    function handler() {
        setTimeout(function () {
            window.location.reload();
        }, 2000);
    }
    document.addEventListener('click', function(e) {
        for (var target = e.target; target && target != this; target = target.parentNode) {
            if (target.matches('.vote-button')) {
                handler.call(target, e);
                break;
            }
        }
    }, false);
</script>
<?php else: ?>
	<p class='red'><?php echo htmlspecialchars(Flux::message("NoVotingSiteYet2")) ?></p>
<?php endif ?>
