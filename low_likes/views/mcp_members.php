<?php if ($members): ?>

	<table class="mainTable" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?=lang('member')?></th>
				<th scope="col"><?=lang('num_likes')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($members AS $row): ?>
				<tr>
					<td><a href="<?=$row['member_url']?>"><?=$row['screen_name']?></a></td>
					<td><?=$row['num_likes']?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php else: ?>

	<p><?=lang('no_likes')?></p>

<?php endif; ?>