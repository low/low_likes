<?php if ($likes): ?>

	<table class="mainTable" cellspacing="0">
		<thead>
			<tr>
				<?php if ($view != 'entry'): ?><th scope="col"><?=lang('entry')?></th><?php endif; ?>
				<?php if ($view != 'member'): ?><th scope="col"><?=lang('member')?></th><?php endif; ?>
				<th scope="col"><?=lang('date')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($likes AS $row): ?>
				<tr>
					<?php if ($view != 'entry'): ?><td><a href="<?=$row['entry_url']?>"><?=$row['title']?></a></td><?php endif; ?>
					<?php if ($view != 'member'): ?><td><a href="<?=$row['member_url']?>"><?=$row['screen_name']?></a></td><?php endif; ?>
					<td><?=$row['like_date']?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php else: ?>

	<p><?=lang('no_likes')?></p>

<?php endif; ?>