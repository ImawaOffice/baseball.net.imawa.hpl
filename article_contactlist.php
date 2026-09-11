<!-- Article Contact Start -->
<article class="contact" id="contact">
	<div class="container">
		<h2>お問合せ一覧</h2>
		<div class="contact-content">
			<!-- Form -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">
				<input type="hidden" name="page_current" value="<?php echo $_SESSION[ 'FORM_PAGE_CURRENT' ]; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div>
					<div class="page-navi">
						<?php $page_top_disabled = ""; ?>
						<?php $page_previous_disabled = ""; ?>
						<?php $page_current_disabled = "disabled"; ?>
						<?php $page_next_disabled = ""; ?>
						<?php $page_last_disabled = ""; ?>
						<button type="submit" name="page_top" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_TOP' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_TOP' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_top_disabled; ?>><?php echo "<<"; ?></button>
						<button type="submit" name="page_previous" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_PREVIOUS' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_PREVIOUS' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_previous_disabled; ?>><?php echo "<"; ?></button>
						<button type="button" name="page_current" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_CURRENT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_CURRENT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_current_disabled; ?>><?php echo isset( $_SESSION[ 'FORM_PAGE_CURRENT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_CURRENT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?></button>
						<button type="submit" name="page_next" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_NEXT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_NEXT' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_next_disabled; ?>><?php echo ">"; ?></button>
						<button type="submit" name="page_last" value="<?php echo isset( $_SESSION[ 'FORM_PAGE_LAST' ] ) ? htmlspecialchars( $_SESSION[ 'FORM_PAGE_LAST' ], ENT_QUOTES, 'UTF-8' ) : '0'; ?>" <?php echo $page_last_disabled; ?>><?php echo ">>"; ?></button>
						<label>表示件数</label>
						<select name="page_rows" id="page_rows" onchange="this.form.submit()">
							<option value="10" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 10 ) ? 'selected' : ''; ?>>10</option>
							<option value="25" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 25 ) ? 'selected' : ''; ?>>25</option>
							<option value="50" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 50 ) ? 'selected' : ''; ?>>50</option>
							<option value="100" <?php echo ( isset( $_SESSION[ 'FORM_PAGE_ROWS' ] ) && $_SESSION[ 'FORM_PAGE_ROWS' ] == 100 ) ? 'selected' : ''; ?>>100</option>
						</select>
					</div>
				</div>
				<div>
					<table class="table" id="inquiryTable">
						<thead>
							<tr>
								<th></th>
								<th>問合せ日時</th>
								<th>お名前</th>
								<th>電話番号</th>
								<th>メールアドレス</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( getInquiryList( $_SESSION[ 'FORM_PAGE_CURRENT' ], $_SESSION[ 'FORM_PAGE_ROWS' ] ) as $contact ) : ?>
								<?php
									$inquiryAt = $contact[ 'inquiry_at' ];
									$dt = new DateTime( $inquiryAt );
									// 日本語曜日配列（日曜=0）
									$week = [ '日', '月', '火', '水', '木', '金', '土' ];
									// 曜日番号を取得
									$w = $dt->format( 'w' );
									// フォーマットして表示
									$formatted = $dt->format("Y-m-d ({$week[ $w ]}) H:i");
								?>
								<tr data-id="<?php echo $contact[ 'inquiry_id' ]; ?>"
									data-datetime="<?php echo htmlspecialchars( $formatted ); ?>"
									data-name="<?php echo htmlspecialchars( $contact['name'] ); ?>"
									data-phone="<?php echo htmlspecialchars( $contact['phone'] ); ?>"
									data-email="<?php echo htmlspecialchars( $contact['email'] ); ?>"
									data-message="<?php echo nl2br( htmlspecialchars( $contact['message'] ) ); ?>"
								>
									<td><button type="button" class="detail_button">詳細</button></td>
									<td><?php echo htmlspecialchars( $formatted ); ?></td>
									<td><?php echo htmlspecialchars( $contact['name'] ); ?></td>
									<td><?php echo htmlspecialchars( $contact['phone'] ); ?></td>
									<td><?php echo htmlspecialchars( $contact['email'] ); ?></td>
									<td><button type="submit" class="delete_button" name="delete_button" value="<?php echo $contact['inquiry_id']; ?>"><?php echo "削除"; ?></button>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<hr>
			</form>
		</div>
	</div>

	<dialog id="detailDialog" class="dialog">
		<form method="dialog" style="margin:0">
			<h3 id="dlgTitle" style="margin:0 0 8px"></h3>
			<div id="dlgBody"></div>
			<div style="margin-top:12px; text-align:right;">
				<button value="close">閉じる</button>
			</div>
		</form>
	</dialog>
</article>

<script>
	const dialog = document.getElementById( "detailDialog" );
	const title  = document.getElementById( "dlgTitle" );
	const body   = document.getElementById( "dlgBody" );

	document.getElementById( "inquiryTable" ).addEventListener( "click", ( e ) => {
		const btn = e.target.closest( ".detail_button" );
		if ( !btn ) return;

		const tr = btn.closest("tr");
		const id = tr.dataset.id;
		const datetime = tr.dataset.datetime;
		const name = tr.dataset.name;
		const phone = tr.dataset.phone;
		const email = tr.dataset.email;
		const message = tr.dataset.message;

		title.textContent = `問合せの詳細 #${id}`;
		
		dialogTable = "";
		dialogTable += "<table class='dialogTable'>";
		dialogTable += `<tr><th>問合せ日時</th><td>${datetime}</td></tr>`;
		dialogTable += `<tr><th>お名前</th><td>${name}</td></tr>`;
		dialogTable += `<tr><th>電話番号</th><td>${phone}</td></tr>`;
		dialogTable += `<tr><th>メールアドレス</th><td>${email}</td></tr>`;
		dialogTable += `<tr><th>メッセージ</th><td>${message}</td></tr>`;
		dialogTable += "</table>";

		body.innerHTML = dialogTable;

		dialog.showModal(); // モーダル表示[1](https://developer.mozilla.org/en-US/docs/Web/API/HTMLDialogElement/showModal)
	});
</script>
<!-- Article Contact End -->