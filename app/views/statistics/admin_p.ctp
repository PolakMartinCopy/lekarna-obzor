<h2>Provize</h2>
<form action="/admin/statistics/p" method="post">
	od:&nbsp;<?=$form->dateTime('Statistic.from', 'DMY', 24)?><br />
	do:&nbsp;<?=$form->dateTime('Statistic.to', 'DMY', 24)?><br />
	<?=$form->submit('Změnit')?>
</form>

<h3>Prodeje</h3>
<table class="left_heading">
	<tr>
		<th>
			s DPH
		</th>
		<td>
			<?=$provisions['with'] ?> Kč
		</td>
	</tr>
	<tr>
		<th>
			bez DPH
		</th>
		<td>
			<?=$provisions['wout'] ?> Kč
		</td>
	</tr>
	<tr>
		<th>
			3%
		</th>
		<td>
			<?=$provisions['prov'] ?> Kč
		</td>
	</tr>
</table>