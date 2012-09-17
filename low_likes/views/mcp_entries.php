<?php if ($entries): ?>

	<table class="mainTable" cellspacing="0">
		<thead>
			<tr>
				<th scope="col"><?=lang('entry')?></th>
				<th scope="col"><?=lang('num_likes')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($entries AS $row): ?>
				<tr>
					<td><a href="<?=$row['entry_url']?>"><?=$row['title']?></a></td>
					<td><?=$row['num_likes']?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php else: ?>

	<p><?=lang('no_likes')?></p>

<?php endif; ?>