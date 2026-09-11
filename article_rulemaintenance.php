<!-- Article Tournament Maintenance Start -->
<article class="rulemaintenance" id="rulemaintenance">
	<div class="container">
		<h2>大会概要/規定登録</h2>
		<div class="rulemaintenance-content">
			<!-- Form -->
			<form method="post" action="" enctype="multipart/form-data" >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div class="ruledetail">
					<hr style="margin: 12px 0; border: none; border-top: 1px solid #ccc;">
					<table class="table" id="ruleTable">
						<thead>
							<tr>
								<th>ルール</th>
								<th>ファイルアップロード</th>
								<th>ファイル名</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>大会概要</td>
								<td><input type="file" id="tournament_rule" name="tournament_rule" value="" accept=".pdf" ></td>
								<td><?php echo htmlspecialchars( $_SESSION[ 'FORM_TOURNAMENT_RULE_FILENAME' ] ); ?></td>
								<td class="text-center"><button type="submit" class="update_button" name="update_button" value="2">アップロード</button></td>
							</tr>
							<tr>
								<td>大会規定</td>
								<td><input type="file" id="game_rule" name="game_rule" value="" accept=".pdf" ></td>
								<td><?php echo htmlspecialchars( $_SESSION[ 'FORM_GAME_RULE_FILENAME' ] ); ?></td>
								<td class="text-center"><button type="submit" class="update_button" name="update_button" value="3">アップロード</button></td>
							</tr>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</article>

<!-- Article Tournament Maintenance End -->