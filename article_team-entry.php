<!-- Article Team Entry Start -->
<article class="team-entry" id="team-entry">
	<div class="container">
		<h2>大会参加申し込み</h2>
		<div class="team-entry-content">
			<!-- 1. メールアドレス入力・認証コード送信 -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">
				<input type="hidden" name="token" value="<?php echo isset( $_SESSION[ 'RES_TOKEN' ] ) ? htmlspecialchars( $_SESSION[ 'RES_TOKEN' ], ENT_QUOTES, 'UTF-8' ) : ''; ?>">

				<div id="errMsg" class="errMsg"><?php echo $_SESSION[ 'FORM_ERR_MSG' ]; ?></div>

				<div class=form-group>
					<label for="tournament_id">参加する大会</label>
					<select id="tournament_id" name="tournament_id">
						<?php foreach ( getTournamentList() as $tournament ) : ?>
							<option 
								value="<?php echo htmlspecialchars( $tournament[ 'tournament_id' ], ENT_QUOTES, 'UTF-8' ); ?>"
								<?php echo ( isset( $_SESSION[ 'FROM_TOURNAMENT_ID' ] ) && $_SESSION[ 'FROM_TOURNAMENT_ID' ] == $tournament[ 'tournament_id' ] ) ? 'selected' : ''; ?>
							>
								<?php echo htmlspecialchars( $tournament[ 'tournament_name' ], ENT_QUOTES, 'UTF-8' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

<style>

.tab_wrap {
    display: flex;
    flex-wrap: wrap;
}
.tab-label {
    color: White;
    background: LightGray;
    padding: 3px 12px;
    order:-1;
}
.tab-content {
    width: 100%;
    display: none;
	margin-top: 16px;
	padding-left: 16px;
}
.tab-switch:checked+.tab-label {
    background: DeepSkyBlue;
}
.tab-switch:checked+.tab-label+.tab-content {
    display: block;
}
.tab-switch {
    display: none;
}

.tab_wrap input.tab-switch {
  display: none;
}
.tab_wrap .tab_form_group input[type="checkbox"] {
  display: inline-block;
  width: auto;
}
.tab_wrap .tab_form_group input[type="radio"] {
  display: inline-block;
  width: auto;
}
.tab_wrap .tab_form_group input[type="number"] {
	display: inline-block;
	width: 100%;
}
/* カテゴリー */
.tab_wrap #tab_content01,
.tab_wrap #cp_content2,
.tab_wrap #cp_content3,
.tab_wrap #cp_content4 {
  display: none;
  border-top: 1px solid #dddddd;
}
.tab_wrap label.tab_item {
  display: inline-block;
  margin: 0 0 -1px;
  padding: 15px 15px;
  text-align: center;
  color: #bbbbbb;
  border: 1px solid transparent;
}
.tab_wrap label.tab_item:before {
  margin-right: 10px;
}
.tab_wrap label.tab_item[for*='1']:before { content: '\f2bd'; }
.tab_wrap label.tab_item[for*='2']:before { content: '\f15c'; }
.tab_wrap label.tab_item[for*='3']:before { content: '\f0f4'; }
.tab_wrap label.tab_item[for*='4']:before { content: '\f001'; }
.tab_wrap label.tab_item:hover {
  cursor: pointer;
  color: #888888;
}
.tab_wrap input:checked + label {
  color: #555555;
  border: 1px solid #dddddd;
  border-bottom: 1px solid #ffffff;
}
/* --ブロックのバーの色 */
@media screen and (max-width: 650px) {
  .tab_wrap label.tab_item {
    font-size: 0;
  }
  .tab_wrap label.tab_item:before {
    font-size: 18px;
    margin: 0;
  }
}
@media screen and (max-width: 400px) {
  .tab_wrap label.tab_item {
    padding: 15px;
  }
  .tab_wrap label.tab_item:before {
    margin-right: 0px;
  }
}
/* QAブロック */
.tab_wrap #tab_01:checked ~ #tab_content01,
.tab_wrap #cp_conttab2:checked ~ #cp_content2,
.tab_wrap #cp_conttab3:checked ~ #cp_content3,
.tab_wrap #cp_conttab4:checked ~ #cp_content4 {
  display: block;
}
.tab_wrap .cp_qain {
  overflow-x: hidden;
  margin: 0 auto;
  color: #333333;
}
.tab_wrap .cp_qain .cp_actab {
  padding: 20px 0;
  border-bottom: 1px dotted #cccccc;
}
/* 質問 */
.tab_wrap .cp_qain label {
  position: relative;
  display: block;
  width: 100%;
  margin: 0;
  padding: 10px 10px 0 48px;
  cursor: pointer;
}
/* ＋アイコン */
.tab_wrap .cp_qain .cp_plus {
  font-size: 2em;
  line-height: 100%;
  position: absolute;
  z-index: 5;
  margin-top: 3px;
  margin-left: 10px;
  transition: 0.2s ease;
}
/* 答え */
.tab_wrap .cp_qain .cp_actab-content {
  position: relative;
  overflow: hidden;
  height: 0;
  margin: 0 10px 0 48px;
  padding: 14px 0;
  transition: 0.4s ease;
  opacity: 0;
}
/* 質問を開いた時の仕様 */
.tab_wrap .cp_qain .cp_actab input[type=checkbox]:checked ~ .cp_actab-content {
  height: auto;
  opacity: 1;
}
/* 質問をクリックした時の▼アイコンの動き */
.tab_wrap .cp_qain .cp_actab input[type=checkbox]:checked ~ .cp_plus {
  transform: rotate(45deg);
}


form .tab_form_group {
	width: 100%;
	display: flex;
	flex-direction: column;
	gap: 8px;
	margin-bottom: 16px;
}

form .tab_form_group label {
	width: 100%;
	text-align: left;
	padding-right: 0;
}

form .tab_form_group input[ type="text" ],
form .tab_form_group input[ type="email" ],
form .tab_form_group input[ type="tel" ],
form .tab_form_group input[ type="password" ],
form .tab_form_group textarea {
	width: 100%;
}

form .tab_form_group .checkbox-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

form .tab_form_group .number-row label {
	width: 160px;
	flex: 0 0 160px;
	text-align: right;
}

form .tab_form_group .number-row input[type="number"] {
	flex: 1;
}

form .tab_form_group .number-row .number-unit {
	flex: 0 0 auto;
	white-space: nowrap;
}

form .tab_form_group .checkbox-row input[type="checkbox"] {
  margin: 0;
}

form .tab_form_group .checkbox-row label {
  width: auto;
  margin: 0;
}

form .tab_form_group button {
	width: 100%;
	padding: 8px 16px;
	background-color: #007BFF;
	color: #fff;
	border: none;
	border-radius: 4px;
	cursor: pointer;
}
@media ( max-width: 400px ) {	/* iPhone SE 2nd Gen */
	form .form-group{
		flex-direction: column;
		align-items: flex-start;
	}
	form .form-group label {
		text-align: left;
		white-space: nowrap;
	}
	form .form-group input[ type="text" ],
	form .form-group input[ type="email" ],
	form .form-group input[ type="tel" ],
	form .form-group input[ type="password" ],
	form .form-group textarea {
		width: 100%;
	}
	.tab-content {
		padding-left: 0;
	}
}


</style>
				<div>
					<div class="tab_wrap">
						
						<input id="tab_01" class="tab-switch" type="radio" name="tab-switch" checked disabled>
						<label for="tab_01" class="tab-label">１．申込</label>

						<div id="tab_content01" class="tab-content">
							<div class=tab_form_group>
								<label for="tournament_agree">大会概要を確認し承諾しますか？</label>
								<?php $tournament_agree_autofocus = ""; ?>
								<?php $tournament_agree_checked = $_SESSION[ 'FORM1_TOURNAMENT_AGREE' ] == '1' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="checkbox" 
										id="tournament_agree" 
										name="tournament_agree" 
										value="yes" 
										<?php echo $tournament_agree_autofocus; ?> 
										<?php echo $tournament_agree_checked; ?> 
										required>
									<label for="tournament_agree">はい、承諾します</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label for="tournament_lottery_agree">本大会は、応募多数の場合抽選を行います。承諾しますか？</label>
								<?php $tournament_lottery_agree_autofocus = ""; ?>
								<?php $tournament_lottery_agree_checked = $_SESSION[ 'FORM1_TOURNAMENT_LOTTERY_AGREE' ] == '1' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="checkbox" 
										id="tournament_lottery_agree" 
										name="tournament_lottery_agree" 
										value="yes" 
										<?php echo $tournament_lottery_agree_autofocus; ?> 
										<?php echo $tournament_lottery_agree_checked; ?> 
										required>
									<label for="tournament_lottery_agree">はい、承諾します</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label for="waitlist_preference">抽選結果後、出場辞退チームがいる場合キャンセル待ちを希望しますか？</label>
								<?php $waitlist_preference_autofocus = ""; ?>
								<?php $waitlist_preference_yes_checked = $_SESSION[ 'FORM1_WAITLIST_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<?php $waitlist_preference_no_checked = $_SESSION[ 'FORM1_WAITLIST_PREFERENCE' ] == 'no' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="radio" 
										id="waitlist_yes" 
										name="waitlist_preference" 
										value="yes" 
										<?php echo $waitlist_preference_autofocus; ?> 
										<?php echo $waitlist_preference_yes_checked; ?> 
										required>
									<label for="waitlist_yes">はい</label>
									<input type="radio" 
										id="waitlist_no" 
										name="waitlist_preference" 
										value="no" 
										<?php echo $waitlist_preference_autofocus; ?> 
										<?php echo $waitlist_preference_no_checked; ?> 
										required>
									<label for="waitlist_no">いいえ</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label></label>
								<button type="button" 
									name="button_next01" 
									value="<?php echo $_SESSION[ 'FORM_BUTTON_SEND' ]; ?>"
								>
									<?php echo "次へ"; ?>
								</button>
							</div>
						</div>

						<input id="tab_02" class="tab-switch" type="radio" name="tab-switch" disabled>
						<label for="tab_02" class="tab-label">２．滋賀県内or滋賀県外</label>

						<div id="tab_content02" class="tab-content">

							<div class=tab_form_group>
								<label for="shiga_yes">滋賀県内のチームですか？滋賀県外のチームですか？</label>
								<?php $shiga_preference_autofocus = ""; ?>
								<?php $shiga_preference_yes_checked = $_SESSION[ 'FORM2_SHIGA_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<?php $shiga_preference_no_checked = $_SESSION[ 'FORM2_SHIGA_PREFERENCE' ] == 'no' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="radio" 
										id="shiga_yes" 
										name="shiga_preference" 
										value="yes" 
										<?php echo $shiga_preference_autofocus; ?> 
										<?php echo $shiga_preference_yes_checked; ?> 
										required>
									<label for="shiga_yes">滋賀県内</label>
									<input type="radio" 
										id="shiga_no" 
										name="shiga_preference" 
										value="no" 
										<?php echo $shiga_preference_autofocus; ?> 
										<?php echo $shiga_preference_no_checked; ?> 
										required>
									<label for="shiga_no">滋賀県外</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label></label>
								<button type="button" 
									name="button_next02" 
									value="<?php echo $_SESSION[ 'FORM_BUTTON_SEND' ]; ?>"
								>
								<?php echo "次へ"; ?></button>
							</div>

						</div>

						<input id="tab_03" class="tab-switch" type="radio" name="tab-switch" disabled>
						<label for="tab_03" class="tab-label">３．宿泊無or有　(県外チームのみ)</label>

						<div id="tab_content03" class="tab-content">

							<div class=tab_form_group>
								<label for="stay_yes">宿泊を利用しますか？</label>
								<?php $stay_preference_autofocus = ""; ?>
								<?php $stay_preference_yes_checked = $_SESSION[ 'FORM3_STAY_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<?php $stay_preference_no_checked = $_SESSION[ 'FORM3_STAY_PREFERENCE' ] == 'no' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="radio" 
										id="stay_yes" 
										name="stay_preference" 
										value="yes" 
										<?php echo $stay_preference_autofocus; ?> 
										<?php echo $stay_preference_yes_checked; ?> 
										required>
									<label for="stay_yes">はい</label>
									<input type="radio" 
										id="stay_no" 
										name="stay_preference" 
										value="no" 
										<?php echo $stay_preference_autofocus; ?> 
										<?php echo $stay_preference_no_checked; ?>>
									<label for="stay_no">いいえ</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label></label>
								<button type="button" 
									name="button_next03" 
									value="<?php echo $_SESSION[ 'FORM_BUTTON_SEND' ]; ?>"
								>
									<?php echo "次へ"; ?>
								</button>
							</div>

						</div>

						<input id="tab_04" class="tab-switch" type="radio" name="tab-switch" disabled>
						<label for="tab_04" class="tab-label">４．宿泊利用情報</label>

						<div id="tab_content04" class="tab-content">

							<div class=tab_form_group>
								<label for="stay_agree">宿泊は、9/21㈪～9/23㈬の2泊3日・朝食付きです。</label>
								<?php $stay_agree_autofocus = ""; ?>
								<?php $stay_agree_checked = $_SESSION[ 'FORM4_STAY_AGREE' ] == '1' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="checkbox" 
										id="stay_agree" 
										name="stay_agree" 
										value="yes" 
										<?php echo $stay_agree_autofocus; ?> 
										<?php echo $stay_agree_checked; ?> 
										required>
									<label for="stay_agree">はい、承諾します</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label for="dinner_yes">夕食の手配をオプションで付けますか？</label>
								<?php $dinner_preference_autofocus = ""; ?>
								<?php $dinner_preference_yes_checked = $_SESSION[ 'FORM4_DINNER_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<?php $dinner_preference_no_checked = $_SESSION[ 'FORM4_DINNER_PREFERENCE' ] == 'no' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="radio" 
										id="dinner_yes" 
										name="dinner_preference" 
										value="yes" 
										<?php echo $dinner_preference_autofocus; ?> 
										<?php echo $dinner_preference_yes_checked; ?> 
										required>
									<label for="dinner_yes">はい</label>
									<input type="radio" 
										id="dinner_no" 
										name="dinner_preference" 
										value="no" 
										<?php echo $dinner_preference_autofocus; ?> 
										<?php echo $dinner_preference_no_checked; ?>>
									<label for="dinner_no">いいえ</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label for="team_agree2">宿泊利用者人数</label>
								<?php $agree_autofocus = ""; ?>
								<div class="checkbox-row number-row">
									<label for="stay_player_count">選手：</label>
									<select id="stay_player_count" name="stay_player_count" required>
										<?php for ( $i = 1; $i <= 20; $i++ ) : ?>
											<option value="<?php echo $i; ?>" 
												<?php echo ( isset( $_SESSION[ 'FORM4_STAY_PLAYER_COUNT' ] ) && $_SESSION[ 'FORM4_STAY_PLAYER_COUNT' ] == $i ) ? 'selected' : ''; ?>
											>
												<?php echo $i; ?>
											</option>
										<?php endfor; ?>
									</select>
									<span class="number-unit">名</span>
								</div>

								<div class="checkbox-row number-row">
									<label for="stay_coach_count">指導者：</label>
									<select id="stay_coach_count" name="stay_coach_count" required>
										<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
											<option value="<?php echo $i; ?>" 
												<?php echo ( isset( $_SESSION[ 'FORM4_STAY_COACH_COUNT' ] ) && $_SESSION[ 'FORM4_STAY_COACH_COUNT' ] == $i ) ? 'selected' : ''; ?>
											>
												<?php echo $i; ?>
											</option>
										<?php endfor; ?>
									</select>
									<span class="number-unit">名</span>
								</div>

								<div class="checkbox-row number-row">
									<label for="stay_other_count">帯同保護者・その他：</label>
									<select id="stay_other_count" name="stay_other_count" required>
										<?php for ( $i = 1; $i <= 50; $i++ ) : ?>
											<option value="<?php echo $i; ?>" 
												<?php echo ( isset( $_SESSION[ 'FORM4_STAY_OTHER_COUNT' ] ) && $_SESSION[ 'FORM4_STAY_OTHER_COUNT' ] == $i ) ? 'selected' : ''; ?>
											>
												<?php echo $i; ?>
											</option>
										<?php endfor; ?>
									</select>
									<span class="number-unit">名</span>
								</div>
							</div>

							<div class=tab_form_group>
								<label for="stay_car_count">宿泊先駐車場利用台数</label>
								<?php $agree_autofocus = ""; ?>
								<div class="checkbox-row number-row">
									<label for="stay_car_count">車両：</label>
									<input type="number" 
										id="stay_car_count" 
										name="stay_car_count" 
										value="<?php echo isset( $_SESSION[ 'FORM4_STAY_CAR_COUNT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM4_STAY_CAR_COUNT' ] ) : 0; ?>" 
										min="0" 
										max="20" 
										<?php echo $agree_autofocus; ?> 
										required>
									<span class="number-unit">台</span>
								</div>
							</div>

							<div class=tab_form_group>
								<label>食事に関するアレルギー</label>
								<?php $allergy_autofocus = ""; ?>
								<?php $allergy_yes_checked = $_SESSION[ 'FORM4_ALLERGY' ] == 'yes' ? 'checked' : ''; ?>
								<?php $allergy_no_checked = $_SESSION[ 'FORM4_ALLERGY' ] == 'no' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="radio" id="allergy_yes" name="allergy" value="yes" <?php echo $allergy_yes_checked; ?> required onchange="toggleAllergyInfo()">
									<label for="allergy_yes">あり</label>
									<input type="radio" id="allergy_no" name="allergy" value="no" <?php echo $allergy_no_checked; ?> required onchange="toggleAllergyInfo()">
									<label for="allergy_no">なし</label>
								</div>
							</div>

							<div class=tab_form_group>
								<label>アレルギーありの場合具体的に回答（※後日抽選結果後に提出の場合は「後日提出」と記載）</label>
								<?php $allergy_info_autofocus = ""; ?>
								<textarea id="allergy_info" name="allergy_info" required><?php echo isset( $_SESSION[ 'FORM4_ALLERGY_INFO' ] ) ? htmlspecialchars( $_SESSION[ 'FORM4_ALLERGY_INFO' ] ) : ''; ?></textarea>
							</div>

							<div class=tab_form_group>
								<label></label>
								<button type="button" name="button_next04" value="<?php echo $_SESSION[ 'FORM_BUTTON_SEND' ]; ?>"><?php echo "次へ"; ?></button>
							</div>

						</div>

						<input id="tab_05" class="tab-switch" type="radio" name="tab-switch" disabled>
						<label for="tab_05" class="tab-label">５．チーム情報</label>

						<div id="tab_content05" class="tab-content">

							<div class=form-group>
								<label for="team_name">チーム名</label>
								<?php $teamName_autofocus = "autofocus"; ?>
								<input type="text" 
									id="team_name" 
									name="team_name" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TEAM_NAME' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_NAME' ] ) : ''; ?>" 
									<?php echo $teamName_autofocus; ?> 
									required>
							</div>

							<div class=form-group>
								<label for="team_name_kana">チーム名（フリガナ）</label>
								<?php $teamNameKana_autofocus = "autofocus"; ?>
								<input type="text" 
									id="team_name_kana" 
									name="team_name_kana" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TEAM_NAME_KANA' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_NAME_KANA' ] ) : ''; ?>" 
									<?php echo $teamNameKana_autofocus; ?> 
									required>
							</div>

							<div class=form-group>
								<label for="team_manager">チーム代表者名</label>
								<?php $teamManager_autofocus = ""; ?>
								<input type="text" 
									id="team_manager" 
									name="team_manager" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TEAM_MANAGER' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_MANAGER' ] ) : ''; ?>" 
									<?php echo $teamManager_autofocus; ?> 
									required>
							</div>

							<div class=form-group>
								<label for="team_manager_kana">チーム代表者名（フリガナ）</label>
								<?php $teamManagerKana_autofocus = ""; ?>
								<input type="text" 
									id="team_manager_kana" 
									name="team_manager_kana" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TEAM_MANAGER_KANA' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_MANAGER_KANA' ] ) : ''; ?>" 
									<?php echo $teamManagerKana_autofocus; ?> 
									required>
							</div>
											
							<div class=form-group>
								<label for="team_manager_tel">チーム代表者（電話番号）</label>
								<?php $teamManagerTel_autofocus = ""; ?>
								<input type="tel" 
									id="team_manager_tel" 
									name="team_manager_tel" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TEAM_MANAGER_TEL' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_MANAGER_TEL' ] ) : ''; ?>" 
									<?php echo $teamManagerTel_autofocus; ?> 
									required>
							</div>

							<div class=form-group>
								<label for="team_manager_email">チーム代表者（メールアドレス）</label>
								<?php $teamManagerEmail_autofocus = ""; ?>
								<input type="email" 
									id="team_manager_email" 
									name="team_manager_email" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TEAM_MANAGER_EMAIL' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_MANAGER_EMAIL' ] ) : ''; ?>" 
									<?php echo $teamManagerEmail_autofocus; ?> 
									required>
							</div>

							<div class="form-group">
								<label for="tournament_contact">大会連絡担当者</label>
								<?php $tournamentContact_autofocus = ""; ?>
								<input type="text" 
									id="tournament_contact" 
									name="tournament_contact" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT' ] ) : ''; ?>" 
									<?php echo $tournamentContact_autofocus; ?> 
									>
							</div>

							<div class="form-group mt-12">
								<label class="fs08em" for="tournament_contact">※チーム代表と同じ場合は記載なし</label>
								<p></p>
							</div>

							<div class="form-group">
								<label for="tournament_contact_kana">大会連絡担当者（フリガナ）</label>
								<?php $tournamentContactKana_autofocus = ""; ?>
								<input type="text" 
									id="tournament_contact_kana" 
									name="tournament_contact_kana" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_KANA' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_KANA' ] ) : ''; ?>" 
									<?php echo $tournamentContactKana_autofocus; ?> 
									>
							</div>

							<div class="form-group mt-12">
								<label class="fs08em" for="tournament_contact">※チーム代表と同じ場合は記載なし</label>
								<p></p>
							</div>

							<div class=form-group>
								<label for="tournament_contact_tel">大会連絡担当者（電話番号）</label>
								<?php $tournamentContactTel_autofocus = ""; ?>
								<input type="tel" 
									id="tournament_contact_tel" 
									name="tournament_contact_tel" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_TEL' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_TEL' ] ) : ''; ?>" 
									<?php echo $tournamentContactTel_autofocus; ?> 
									>
							</div>

							<div class="form-group mt-12">
								<label class="fs08em" for="tournament_contact">※チーム代表と同じ場合は記載なし</label>
								<p></p>
							</div>

							<div class=form-group>
								<label for="tournament_contact_email">大会連絡担当者（メールアドレス）</label>
								<?php $tournamentContactEmail_autofocus = ""; ?>
								<input type="email" 
									id="tournament_contact_email" 
									name="tournament_contact_email" 
									value="<?php echo isset( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_EMAIL' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TOURNAMENT_CONTACT_EMAIL' ] ) : ''; ?>" 
									<?php echo $tournamentContactEmail_autofocus; ?> 
									>
							</div>

							<div class="form-group mt-12">
								<label class="fs08em" for="tournament_contact">※チーム代表と同じ場合は記載なし</label>
								<p></p>
							</div>

							<div class="form-group">
								<label for="player_count">登録選手</label>
								<div>
									<select id="player_count" name="player_count" required>
										<?php for ( $i = 1; $i <= 20; $i++ ) : ?>
											<option value="<?php echo $i; ?>" 
												<?php echo ( isset( $_SESSION[ 'FORM5_PLAYER_COUNT' ] ) && $_SESSION[ 'FORM5_PLAYER_COUNT' ] == $i ) ? 'selected' : ''; ?>
											>
												<?php echo $i; ?>
											</option>
										<?php endfor; ?>
									</select>
									<span class="number-unit">名</span>
								</div>
							</div>

							<div class="form-group">
								<label for="manager_count">指導者・スタッフ</label>
								<div>
									<select id="manager_count" name="manager_count" required>
										<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
											<option value="<?php echo $i; ?>" 
												<?php echo ( isset( $_SESSION[ 'FORM5_MANAGER_COUNT' ] ) && $_SESSION[ 'FORM5_MANAGER_COUNT' ] == $i ) ? 'selected' : ''; ?>
											>
												<?php echo $i; ?>
											</option>
										<?php endfor; ?>
									</select>
									<span class="number-unit">名</span>
								</div>
							</div>

							<div class="form-group mt-12">
								<label class="fs08em" for="tournament_contact">（監督・コーチ・スコアラー）</label>
								<p></p>
							</div>

							<div class="form-group">
								<label for="parent_count">保護者</label>
								<div>
									<select id="parent_count" name="parent_count" required>
										<option value="0" 
											<?php echo ( !isset( $_SESSION[ 'FORM5_PARENT_COUNT' ] ) || $_SESSION[ 'FORM5_PARENT_COUNT' ] === '' ) ? 'selected' : ''; ?>
										>
											未設定
										</option>
										<?php for ( $i = 1; $i <= 50; $i++ ) : ?>
											<option value="<?php echo $i; ?>" 
												<?php echo ( isset( $_SESSION[ 'FORM5_PARENT_COUNT' ] ) && $_SESSION[ 'FORM5_PARENT_COUNT' ] == $i ) ? 'selected' : ''; ?>
											>
												<?php echo $i; ?>
											</option>
										<?php endfor; ?>
									</select>
									<span class="number-unit">名</span>
								</div>
							</div>

							<div class=form-group>
								<label for="tournament_contact_line">移動手段（主たるものに✓）</label>
								<?php $mycar_preference = $_SESSION[ 'FORM5_MYCAR_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="checkbox" 
										id="mycar_preference" 
										name="mycar_preference" 
										value="yes" 
										<?php echo $mycar_preference; ?>
										>
									<label for="mycar_preference">マイカー・自家用車</label>
									<label for="mycar_count">（台数：</label>
									<input type="number" 
										id="mycar_count" 
										name="mycar_count" 
										value="<?php echo isset( $_SESSION[ 'FORM5_MYCAR_COUNT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_MYCAR_COUNT' ] ) : 0; ?>" 
										min="0" 
										max="50" 
										required>
									<span class="number-unit">台）</span>
								</div>
							</div>

							<div class=form-group mt-12>
								<label for="tournament_contact_line"></label>
								<?php $microbus_preference = $_SESSION[ 'FORM5_MICROBUS_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="checkbox" 
										id="microbus_preference" 
										name="microbus_preference" 
										value="yes" 
										<?php echo $microbus_preference; ?> 
										>
									<label for="microbus_preference">マイクロバス・大型バス</label>
									<label for="microbus_count">（台数：</label>
									<input type="number" 
										id="microbus_count" 
										name="microbus_count" 
										value="<?php echo isset( $_SESSION[ 'FORM5_MICROBUS_COUNT' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_MICROBUS_COUNT' ] ) : 0; ?>" 
										min="0" 
										max="50" 
										required>
									<span class="number-unit">台）</span>
								</div>
							</div>

							<div class=form-group mt-12>
								<label for="tournament_contact_line"></label>
								<?php $public_transport_preference = $_SESSION[ 'FORM5_PUBLIC_TRANSPORT_PREFERENCE' ] == 'yes' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<input type="checkbox" 
										id="public_transport_preference" 
										name="public_transport_preference" 
										value="yes" 
										<?php echo $public_transport_preference; ?>
										>
									<label for="public_transport_preference">公共交通機関（新幹線・電車・路線バス等）</label>
								</div>
							</div>

							<div class="form-group">
								<label for="lunch_yes">昼食利用</label>
								<?php $lunch_day1_lunchbox_checked = $_SESSION[ 'FORM5_LUNCH_DAY1_LUNCHBOX' ] == 'yes' ? 'checked' : ''; ?>
								<?php $lunch_day1_stall_checked = $_SESSION[ 'FORM5_LUNCH_DAY1_STALL' ] == 'yes' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<span class="number-unit">1日目：</span>
									<label for="lunch_day1_lunchbox">お弁当</label>
									<input type="checkbox" 
										id="lunch_day1_lunchbox" 
										name="lunch_day1_lunchbox" 
										value="yes" 
										<?php echo $lunch_day1_lunchbox_checked; ?> 
									>
									<label for="lunch_day1_stall">屋台</label>
									<input type="checkbox" 
										id="lunch_day1_stall" 
										name="lunch_day1_stall" 
										value="yes" 
										<?php echo $lunch_day1_stall_checked; ?> 
									>
								</div>
							</div>

							<div class="form-group mt-12">
								<label for="tournament_contact_line"></label>
								<?php $lunch_day2_stall_checked = $_SESSION[ 'FORM5_LUNCH_DAY2_STALL' ] == 'yes' ? 'checked' : ''; ?>
								<?php $lunch_day2_lunchbox_checked = $_SESSION[ 'FORM5_LUNCH_DAY2_LUNCHBOX' ] == 'yes' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<span class="number-unit">2日目：</span>
									<label for="lunch_day2_lunchbox">お弁当</label>
									<input type="checkbox" 
										id="lunch_day2_lunchbox" 
										name="lunch_day2_lunchbox" 
										value="yes" 
										<?php echo $lunch_day2_lunchbox_checked; ?> 
									>
									<label for="lunch_day2_stall">屋台</label>
									<input type="checkbox" 
										id="lunch_day2_stall" 
										name="lunch_day2_stall" 
										value="yes" 
										<?php echo $lunch_day2_stall_checked; ?> 
									>
								</div>
							</div>

							<div class="form-group mt-12">
								<label for="tournament_contact_line"></label>
								<?php $lunch_day3_stall_checked = $_SESSION[ 'FORM5_LUNCH_DAY3_STALL' ] == 'yes' ? 'checked' : ''; ?>
								<?php $lunch_day3_lunchbox_checked = $_SESSION[ 'FORM5_LUNCH_DAY3_LUNCHBOX' ] == 'yes' ? 'checked' : ''; ?>
								<div class="checkbox-row">
									<span class="number-unit">3日目：</span>
									<label for="lunch_day3_lunchbox">お弁当</label>
									<input type="checkbox" 
										id="lunch_day3_lunchbox" 
										name="lunch_day3_lunchbox" 
										value="yes" 
										<?php echo $lunch_day3_lunchbox_checked; ?> 
									>
									<label for="lunch_day3_stall">屋台</label>
									<input type="checkbox" 
										id="lunch_day3_stall" 
										name="lunch_day3_stall" 
										value="yes" 
										<?php echo $lunch_day3_stall_checked; ?> 
									>
								</div>
							</div>

							<div class="tab_form_group">
								<label>チームの実績（※2026年度のチームの主な実績をご記入ください）</label>
								<?php $team_achievements_autofocus = ""; ?>
								<textarea id="team_achievements" name="team_achievements" required><?php echo isset( $_SESSION[ 'FORM5_TEAM_ACHIEVEMENTS' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_ACHIEVEMENTS' ] ) : ''; ?></textarea>
							</div>

							<div class="tab_form_group">
								<label>チームPR</label>
								<?php $team_pr_autofocus = ""; ?>
								<textarea id="team_pr" name="team_pr" required><?php echo isset( $_SESSION[ 'FORM5_TEAM_PR' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_TEAM_PR' ] ) : ''; ?></textarea>
							</div>

							<div class="tab_form_group">
								<label>どこで大会の情報を知りましたか？</label>
								<?php $information_source_mouth_checked = $_SESSION[ 'FORM5_INFORMATION_SOURCE' ] == 'mouth' ? 'checked' : ''; ?>
								<?php $information_source_sns_checked = $_SESSION[ 'FORM5_INFORMATION_SOURCE' ] == 'sns' ? 'checked' : ''; ?>
								<?php $information_source_homepage_checked = $_SESSION[ 'FORM5_INFORMATION_SOURCE' ] == 'homepage' ? 'checked' : ''; ?>
								<?php $information_source_introduction_checked = $_SESSION[ 'FORM5_INFORMATION_SOURCE' ] == 'introduction' ? 'checked' : ''; ?>
								<?php $information_source_other_checked = $_SESSION[ 'FORM5_INFORMATION_SOURCE' ] == 'other' ? 'checked' : ''; ?>
								<div class="">
									<input type="radio" 
										id="information_source_mouth" 
										name="information_source" 
										value="mouth" 
										<?php echo $information_source_mouth_checked; ?> 
										required>
									<label for="information_source_mouth">クチコミ</label>
									<input type="radio" 
										id="information_source_sns" 
										name="information_source" 
										value="sns" 
										<?php echo $information_source_sns_checked; ?> 
										required>
									<label for="information_source_sns">SNS</label>
									<input type="radio" 
										id="information_source_homepage" 
										name="information_source" 
										value="homepage" 
										<?php echo $information_source_homepage_checked; ?> 
										required>
									<label for="information_source_homepage">ホームページ</label>
									<input type="radio" 
										id="information_source_introduction" 
										name="information_source" 
										value="introduction" 
										<?php echo $information_source_introduction_checked; ?> 
										required>
									<label for="information_source_introduction">知人の紹介</label>
									<input type="radio" 
										id="information_source_other" 
										name="information_source" 
										value="other" 
										<?php echo $information_source_other_checked; ?> 
										required>
									<label for="information_source_other">その他</label>
								</div>
							</div>

							<div class="tab_form_group">
								<label>大会事務局への伝達事項及びご質問</label>
								<?php $notes_autofocus = ""; ?>
								<textarea id="notes" name="notes" ><?php echo isset( $_SESSION[ 'FORM5_NOTES' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_NOTES' ] ) : ''; ?></textarea>
							</div>

							<div class="tab_form_group">
								<label>HPL㈱イベントサポート事業部公式LINEの追加をお願いいたします。登録後、チーム名と名前の送信のご協力をお願い致します</label>
								<?php $notes_autofocus = ""; ?>
								<textarea id="notes" name="notes" ><?php echo isset( $_SESSION[ 'FORM5_NOTES' ] ) ? htmlspecialchars( $_SESSION[ 'FORM5_NOTES' ] ) : ''; ?></textarea>
							</div>

							<div class="tab_form_group">
								<div class="tab_form_group">
									<label></label>
									<button type="button" name="button_next05" value="<?php echo $_SESSION[ 'FORM_BUTTON_SEND' ]; ?>"><?php echo "参加申し込み"; ?></button>
								</div>
							</div>

							<div style="display: none;" aria-hidden="true">
								<label for="agree">同意</label>
								<input type="text" id="agree" name="agree" tabindex="-1" autocomplete="off" value="" >
							</div>

						</div>

<!--
					<input id="tab_99" class="tab-switch" type="radio" name="tab-switch" >
					<label for="tab_99" class="tab-label">６．ダウンロード</label>

					<div id="tab_content99" class="tab-content">

						<div class="form-group">
							<a href="images/ホタルのまちMORIYAMA2026　大会概要.pdf" download>大会概要をダウンロード</a>
						</div>

						<div class="form-group">
							<a href="images/ホタルのまちMORIYAMA2026　大会規定.pdf" download>大会規定をダウンロード</a>
						</div>

						<div class="form-group">
							<a href="images/ホタルのまちMORIYAMA2026　ブロック予選・トーナメント表.pdf" download>ブロック予選・トーナメント表をダウンロード</a>
						</div>

						<div class="form-group">
							<a href="images/ホタルのまちMORIYAMA2026　申込.pdf" download>申込書をダウンロード</a>
						</div>
					</div>
-->
				</div>
			</form>
<script>
function toggleAllergyInfo() {
	const allergyInfo = document.getElementById( 'allergy_info' );
	if ( !allergyInfo ) {
		return;
	}

	const allergyYes = document.getElementById( 'allergy_yes' );
	const isAllergyYes = allergyYes ? allergyYes.checked : false;

	allergyInfo.required = isAllergyYes;
}
function clearValidationOnChange(target, checks) {
    if (!target) return;

    checks.forEach(check => {
        check?.addEventListener('change', () => {
            target.setCustomValidity('');
        });
    });
}
document.addEventListener( 'DOMContentLoaded', function () {
											
const day1Lunchbox = document.getElementById( 'lunch_day1_lunchbox' );
const day1Stall = document.getElementById( 'lunch_day1_stall' );
const day2Lunchbox = document.getElementById( 'lunch_day2_lunchbox' );
const day2Stall = document.getElementById( 'lunch_day2_stall' );
const day3Lunchbox = document.getElementById( 'lunch_day3_lunchbox' );
const day3Stall = document.getElementById( 'lunch_day3_stall' );

clearValidationOnChange(day1Lunchbox, [day1Lunchbox, day1Stall]);
clearValidationOnChange(day2Lunchbox, [day2Lunchbox, day2Stall]);
clearValidationOnChange(day3Lunchbox, [day3Lunchbox, day3Stall]);
	document.querySelectorAll( 'button[name^="button_next"]' ).forEach( function( btn ) {
		btn.addEventListener( 'click', function() {

			const currentTab = this.closest( '.tab-content' );
			const inputs = currentTab.querySelectorAll( 'input, select, textarea' );

			for ( const input of inputs ) {
				if ( !input.checkValidity() ) {
					input.reportValidity();
					return;
				}
			}

			let currentNo = this.name.match(/\d+/)[0];

			if ( currentNo == "02" ) {
				const shiga_preference = document.querySelector( 'input[name="shiga_preference"]:checked' ).value;
				if ( shiga_preference === 'yes' ) {
					// 滋賀県内チームの場合、宿泊利用情報のタブをスキップ
					currentNo = '04';	// 宿泊利用情報のタブをスキップ
				}
			}

			if ( currentNo == "03" ) {
				const stayPreference = document.querySelector( 'input[name="stay_preference"]:checked' ).value;
				if ( stayPreference === 'no' ) {
					// 滋賀県内チームの場合、宿泊利用情報のタブをスキップ
					currentNo = '04';
				}
			}
			const nextNo = String( Number( currentNo ) + 1 ).padStart( 2, '0' );
			const nextTab = document.getElementById( 'tab_' + nextNo );

			if ( nextTab ) {
				// 次のタブへ移動
				nextTab.checked = true;
			}
			else {
				// 次のタブが存在しない場合
				console.log('最後のタブです');

				// 例1: フォーム送信
				const form = this.closest( 'form' );
				if ( form ) {
					day1Lunchbox?.setCustomValidity('');
					day2Lunchbox?.setCustomValidity('');
					day3Lunchbox?.setCustomValidity('');
					if ( day1Lunchbox && !day1Lunchbox.checked && day1Stall && !day1Stall.checked ) {
						day1Lunchbox.setCustomValidity('1日目の昼食利用を選択してください。');
						day1Lunchbox.reportValidity();
						return;
					}
					if ( day2Lunchbox && !day2Lunchbox.checked && day2Stall && !day2Stall.checked ) {
						day2Lunchbox.setCustomValidity('2日目の昼食利用を選択してください。');
						day2Lunchbox.reportValidity();
						return;
					}
					if ( day3Lunchbox && !day3Lunchbox.checked && day3Stall && !day3Stall.checked ) {
						day3Lunchbox.setCustomValidity('3日目の昼食利用を選択してください。');
						day3Lunchbox.reportValidity();
						return;
					}
					form.submit();
				}
			}
		});
	});
});

</script>

		</div>
	</div>
</article>
<!-- Article Team Entry End -->