<?php if ($likes): ?>

	<table class="mainTable" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?=lang('entry')?></th>
				<th scope="col"><?=lang('member')?></th>
				<th scope="col"><?=lang('date')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($likes AS $row): ?>
				<tr>
					<td><a href="<?=$row['entry_url']?>"><?=$row['title']?></a></td>
					<td><a href="<?=$row['member_url']?>"><?=$row['screen_name']?></a></td>
					<td><?=$row['like_date']?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php else: ?>

	<p><?=lang('no_likes')?></p>

<?php endif; ?>