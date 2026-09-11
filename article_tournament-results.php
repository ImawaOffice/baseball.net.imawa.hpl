<?php
# ==========================================================
# 大会登録内容追加処理
# @param int $p_tournamentId 更新対象の大会ID
# @return boolean 更新成功すればtrue、失敗すればfalse
# ==========================================================
function getAttachmentFile( $p_attachmentType = 2 ){

	global $g_Log;
	$g_Log->notice( "大会規定ファイル取得処理", __FUNCTION__, basename( __FILE__ ) );

	// SQL文作成
	// アップロードしたファイルの書込み
	$SQL = "";
	$SQL .= " SELECT ";
	$SQL .= "   attachment_id ";
	$SQL .= " , attachment_type ";
	$SQL .= " , physical_file_name ";
	$SQL .= " , file_name ";
	$SQL .= " , file_path ";
	$SQL .= " , file_size ";
	$SQL .= " , mime_type ";
	$SQL .= " , file_description ";
	$SQL .= " , CONCAT( file_path, physical_file_name ) AS file_full_path ";	// file_descriptionを文字列として取得
	$SQL .= "   FROM baseball_attachment ";
	$SQL .= "  WHERE is_enabled = 1 ";
	$SQL .= "    AND attachment_type = :attachment_type ";	// attachment_typeが指定されたレコードを大会規定ファイルとして扱う
	$SQL .= "  ORDER BY attachment_id DESC ";	// 取得したレコードのうち、attachment_idが最大のレコードを大会規定ファイルとして扱う
	$SQL .= "  LIMIT 1 ";	// 取得件数を1件に限定
	
	$SQL_Parameters = array(
		'attachment_type' => $p_attachmentType
	);

	$url = "";

	try {
		global $g_DB;
		$dataTable = $g_DB->select( $SQL, $SQL_Parameters );

		foreach( $dataTable as $row ){
			$url = $row[ 'file_full_path' ];
		}
		$g_Log->notice( "URL : {$url} セットしました", __FUNCTION__, basename( __FILE__ ) );

	} catch ( Exception $e ) {
		$_SESSION[ 'FORM_ERR_MSG' ] = '大会規定ファイルの取得に失敗しました';
		$g_Log->error( $_SESSION[ 'FORM_ERR_MSG' ] . " : " . htmlspecialchars( $e->getMessage() ), __FUNCTION__, basename( __FILE__ ) );
		return false;
	}

	return $url;
}
?>
<!-- Article Tournament Results Start -->
<article class="tournament-results" id="tournament-results">
	<div class="container">
		<h2>試合予定/結果</h2>
		<div class="tournament-results-content">
			<!-- Form -->
			<form method="post" action=""  >
				<input type="hidden" name="view_state" value="<?php echo $_SESSION[ 'FROM_VIEW_STATE' ]; ?>">
				<select id="tournament_id" name="tournament_id" onchange="this.form.submit()">
					<?php foreach ( getTournamentList() as $tournament ) : ?>
						<option 
							value="<?php echo htmlspecialchars( $tournament[ 'tournament_id' ], ENT_QUOTES, 'UTF-8' ); ?>"
							<?php echo ( isset( $_SESSION[ 'FROM_TOURNAMENT_ID' ] ) && $_SESSION[ 'FROM_TOURNAMENT_ID' ] == $tournament[ 'tournament_id' ] ) ? 'selected' : ''; ?>
						>
							<?php echo htmlspecialchars( $tournament[ 'tournament_title' ] . ' ' . $tournament[ 'tournament_text' ], ENT_QUOTES, 'UTF-8' ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</form>

			<div id="pdf-container1" style="width:100%;">
				<div id="pdf-images1" style="max-width:100%; height:auto; display:block;"></div>
				<canvas id="pdf-canvas1" style="display:none;"></canvas>
			</div>

			<div id="pdf-container2" style="width:100%;">
				<div id="pdf-images2" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas2" style="display:none;"></canvas>
			</div>

			<div id="pdf-container3" style="width:100%;">
				<div id="pdf-images3" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas3" style="display:none;"></canvas>
			</div>

			<div id="pdf-container4" style="width:100%;">
				<div id="pdf-images4" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas4" style="display:none;"></canvas>
			</div>

			<div id="pdf-container5" style="width:100%;">
				<div id="pdf-images5" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas5" style="display:none;"></canvas>
			</div>

			<div id="pdf-container6" style="width:100%;">
				<div id="pdf-images6" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas6" style="display:none;"></canvas>
			</div>

			<div id="pdf-container7" style="width:100%;">
				<div id="pdf-images7" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas7" style="display:none;"></canvas>
			</div>

			<div id="pdf-container8" style="width:100%;">
				<div id="pdf-images8" style="max-width:100%; height:auto; display:none;"></div>
				<canvas id="pdf-canvas8" style="display:none;"></canvas>
			</div>



		</div>
	</div>
</article>

			<div style="display:none;">
				<p>試合結果</p>
				<img id="canvas1Image">
			</div>

			<div style="display:none;">
				<p>裏大会試合結果</p>
				<img id="canvas2Image">
			</div>
<style>

#ska{
padding:2px;
background-color:white;
margin:2px;
}
#dtn{
padding:2px;
background-color:white;
margin:2px;
}


#tp{
padding:2px;
background-color:white;
margin:2px;
}

#cnpn{
padding:2px;
background-color:white;
margin:2px;
}

#tvew{
/* visibility: hidden; */
}

#stb{

font-size:25px;
}

</style>


			<div id="tp"><h1 style="display:none;">トーナメント図作成WEBアプリ</h1></div>
			<textarea id="tvew" cols="70" rows="30" style="display:none;">
			</textarea>
			<div id="dtn"  style="display:none;">
				<hr>
				表題:<input type="text" size="20" id="tlt" value="動物王者決定大会(サンプルデータ)">
				<hr>
				解説:<input type="text" size="20" id="stm" value="このままクリックしてみてください。">
				<hr>
				<input type="button" onClick="create_Tornament()" value="トーナメント図の作成" id="stb">
				<hr>
				出場:<br>
				<textarea id="teamList" cols="50" rows="16">
					<?php 
						foreach ( getTeamList( $_SESSION[ 'FROM_TOURNAMENT_ID' ], 1, 100 ) as $team ){
							echo htmlspecialchars( $team[ 'team_name' ], ENT_QUOTES, 'UTF-8' ) . "\n";
						}
					 ?>
				</textarea>
				<textarea id="game1List" cols="50" rows="16">
					<?php 
						foreach ( getGameResult( $_SESSION[ 'FROM_TOURNAMENT_ID' ], 0 ) as $game ){
							echo htmlspecialchars( $game[ 'game_block' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'game_count' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'game_count' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team1_name' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team2_name' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'winner_name' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team1_last_game_id' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team2_last_game_id' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'winner_slot' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'score' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo "" . "\n";
						}
					 ?>
				</textarea>
				<textarea id="game2List" cols="50" rows="16">
					<?php 
						foreach ( getGameResult( $_SESSION[ 'FROM_TOURNAMENT_ID' ], 1 ) as $game ){
							echo htmlspecialchars( $game[ 'game_block' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'game_count' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'game_count' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team1_name' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team2_name' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'winner_name' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team1_last_game_id' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'team2_last_game_id' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'winner_slot' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo htmlspecialchars( $game[ 'score' ], ENT_QUOTES, 'UTF-8' ) . ",";
							echo "" . "\n";
						}
					 ?>
				</textarea>
				<br>
				偶数行にシードと入力すればシード対応します。(1次のみ)。<br>
				シード活用で2の累乗にしてください。<br>
				<input type="button" onClick="sampledl()" value="サンプルデータの消去">
			</div>
			<div id="cnpn" style="display:none;">
				<input type="hidden" id="rowHeight" value="32" size="2" onchange="shinlineseisei()">
				<input type="hidden" id="fontPoint"  value="10" size="2" onchange="shinlineseisei()">
				結果線<input type="button" id="md" value="非表示→表示" onclick="sakusei(1)">
			</div>
			<div id="ska" style="display:none;">　</div>

            <!-- トーナメント表をtableで表示するためのdiv -->
            <div id="tournament-table" style="margin:20px 0; text-align:center; display:none;"></div>

			<hr style="page-break-before: always;color:silver;">
			<div id="class0CanvasWrapper"  style="width: 100%; overflow: auto; display: none;" >
<!--			<div id="class0CanvasWrapper"  style="width:800px;height:600px" >	-->
				<canvas id="class0Canvas" width="800" height="600" style="margin:0px;padding:0px"></canvas>
			</div>
			<div id="class1CanvasWrapper"  style="width: 100%; overflow: auto; display: none;" >
<!--			<div id="class1CanvasWrapper"  style="width:800px;height:600px" >	-->
				<canvas id="class1Canvas" width="800" height="600" style="margin:0px;padding:0px"></canvas>
			</div>
			<div id="class2CanvasWrapper"  style="width: 100%; overflow: auto; display: none;" >
<!--			<div id="class2CanvasWrapper"  style="width:800px;height:600px" >	-->
				<canvas id="class2Canvas" width="800" height="600" style="margin:0px;padding:0px"></canvas>
			</div>
			<hr style="page-break-before: always;color:silver;">
			<hr>
				<img id="canvas0Image" style="display:none;">
			<br>

		</div>
	</div>
</article>
<!-- Article Tournament Results End -->


<script defer>
// BODY読み込み時処理
window.addEventListener( 'load', () => {
//	onload_Body();
//	create_Tornament();
//	renderTournamentTable();
	PDFtoImage( '<?php echo getAttachmentFile( 11 ); ?>', 'pdf-canvas1', 'pdf-images1' );
//	PDFtoImage( '<?php echo getAttachmentFile( 12 ); ?>', 'pdf-canvas2', 'pdf-images2' );
//	PDFtoImage( '<?php echo getAttachmentFile( 21 ); ?>', 'pdf-canvas3', 'pdf-images3' );
//	PDFtoImage( '<?php echo getAttachmentFile( 22 ); ?>', 'pdf-canvas4', 'pdf-images4' );
//	PDFtoImage( '<?php echo getAttachmentFile( 31 ); ?>', 'pdf-canvas5', 'pdf-images5' );
//	PDFtoImage( '<?php echo getAttachmentFile( 32 ); ?>', 'pdf-canvas6', 'pdf-images6' );
//	PDFtoImage( '<?php echo getAttachmentFile( 41 ); ?>', 'pdf-canvas7', 'pdf-images7' );
//	PDFtoImage( '<?php echo getAttachmentFile( 42 ); ?>', 'pdf-canvas8', 'pdf-images8' );
});

function onload_Body(){
	// CANVAS(class1Canvas)の取得
	class0Canvas = document.getElementById( 'class0Canvas' );
	class1Canvas = document.getElementById( 'class1Canvas' );
	class2Canvas = document.getElementById( 'class2Canvas' );
	// 2Dコンテキストの取得
	canvas0Context = class0Canvas.getContext( '2d' );
	canvas1Context = class1Canvas.getContext( '2d' );
	canvas2Context = class2Canvas.getContext( '2d' );
	// フォント設定
	canvas0Context.font = "10pt Arial";
	canvas1Context.font = "10pt Arial";
	canvas2Context.font = "10pt Arial";
	// 初期表示モード設定(1:通常表示 2:勝者強調表示)
	displayMode = 2;

	createCanvas( "class1CanvasWrapper", "class1Canvas", "canvas1Image", "game1List", 800, 600 );
	createCanvas( "class2CanvasWrapper", "class2Canvas", "canvas2Image", "game2List", 800, 600 );
}

// トーナメント表作成
function create_Tornament(){
	// 変数宣言
	taiou = new Array();
	liststr = "";
	minashi = 0;
	jitu = 0;
	listline = new Array();
	ruiseki = 0;

	// 入力データの取得
	teamListValue = document.getElementById( "teamList" ).value;
	// データ行ごとに分割(タブを削除してから改行で分割)
	teamArray = new Array();
	teamArray = teamListValue.replace(/\t/g, "").split( "\n" );

	console.log( "データ件数 : " + teamArray.length );
///let index = 0;
///teamArray.forEach(team => {
///    index++;
///    console.log("データ[" + index + "] : " + team);
///});

	// 回戦数を取得
	gameRound = Math.log( teamArray.length - 1 ) / Math.log( 2 );

	console.log( "回戦数 : " + gameRound );

	// 第1回戦のチーム配列をループ
	for( i = 0; i < teamArray.length; i++ ){
		
		console.log( "処理中データ[" + i + "] : " + teamArray[ i ] );

		if( teamArray[ i ] != "" ){	// チーム名が空でない場合
			
			if( i % 2 == 0 ){	// 偶数行の場合
				
				minashi++;

				if( teamArray[ i + 1 ] == "シード" ){	// 次の行がシードの場合
					litmp = "1," + minashi + ",s," + teamArray[ i ] + ",シード," + teamArray[ i ] + ",,,1,,";
				}
				else{
					jitu++;
					taiou[ jitu ] = minashi;
					litmp = "1," + minashi + "," + jitu + "," + teamArray[ i ] + "," + teamArray[ i + 1 ] + ",-,,,0,,";
				}
				
				console.log( "追加データ : " + litmp );

				liststr += litmp + "\n";

				listline[ minashi ] = new Array();
				listline[ minashi ] = litmp.split( "," );
			}
			ruiseki++;
		}
	}

	gendo = teamArray.length / 4;
	kj = 1;

	for( x = 2; x <= gameRound; x++ ){

		for( y = 1; y <= gendo; y++ ){
			
			minashi++;
			jitu++;
			litmp = x + "," + minashi + "," + jitu + ",-,-,-," + kj + "," + (kj + 1) + ",0,,";
			liststr += litmp + "\n";

			if( litmp != "" ){
				taiou[ jitu ] = minashi;
				listline[ minashi ] = new Array();
				listline[ minashi ] = litmp.split( "," );
			}
			kj = kj + 2;
		}
		gendo = gendo / 2;
	}

	// textareaに出力
	document.getElementById( "tvew" ).value = liststr;

	zens = jitu;
	skarray = new Array();

	for( yyy = 1; yyy <= zens; yyy++ ){
		skarray[ yyy ] = 0;
	}

	listhenkoub()
}

function listhenkoub(){

	liststr = "";

	for( yx = 1; yx < listline.length; yx++ ){
		
		if( listline[ yx ][ 0 ] >= 2 ){
			tagetg = listline[ yx ][ 6 ];
			listline[ yx ][ 3 ] = listline[ tagetg ][ 5 ];
			tagetg = listline[ yx ][ 7 ];
			listline[ yx ][ 4 ] = listline[ tagetg ][ 5 ];
		}
		liststr += listline[ yx ] + "\n";
	}
	document.getElementById( "tvew" ).value = liststr;
	kksl2()
}

function getGameList( $gameListId = "game1List" ){

	// 入力データの取得
	let gameListValue = document.getElementById( $gameListId ).value;
	// 入力データを改行で分割し配列化
	let t_gameList = gameListValue.replace(/\t/g, "").split( "\n" );
	// 第1回戦の試合数
	let round1Count = 0;
	// 入力された配列から有効データを取得
	let newGameList = new Array();
///	newGameList[ 0 ] = ",,,-,-,-,,,,0,,";
	console.log( "getGameList : " + $gameListId + " Data : " + t_gameList );
	let newLine = 0;
	for (let i = 0; i < t_gameList.length; i++) {
		// 入力行をカンマで分割し配列化
		let gameRow = t_gameList[ i ].split( "," );
		// 1カラム目（回戦数）が未定義または空欄は対象外とする
		if ( gameRow[ 0 ] == undefined || gameRow[ 0 ] == "" ) {
			continue;
		}
		// 1カラム目（回戦数）が 1 の場合は第1回戦の試合数をカウントアップする
		if ( gameRow[ 0 ] == 1 ) {
			round1Count++;
		}
		// 1カラム目（回戦数）が 1 以上の場合は新しい配列に代入
		if( gameRow[ 0 ] >= 1 ){
			newLine++;
			newGameList[ newLine ] = t_gameList[ i ];
		}
	}
	console.log( "getGameList: round1Count : " + round1Count );

	// 回戦数を取得
	const gameRound = Math.log( round1Count - 1 ) / Math.log( 2 );
	const gameTotalRound = Math.ceil( gameRound ) + 1;

	console.log( "getGameList: gameRound : " + gameRound + " gameTotalRound : " + gameTotalRound );

	// 回戦ごとの試合数を計算し配列に格納
	const roundGameArray = [];

	for ( let i = gameTotalRound; i > 0; i-- ) {
		const matches = Math.pow( 2, gameTotalRound - i );
		console.log( `getGameList: gameTotalRound=${gameTotalRound}, round=${i}, matches=${matches}` );

		roundGameArray[ i ] = matches;
	}

	// ゲームリストの不足分を補填
	const ROW_ROUND = 0;
	const ROW_GAMENO = 1;
	const ROW_TEAM1 = 3;
	const ROW_TEAM2 = 4;
	const ROW_MEMO = 9;
	let lastGameIndex = 1;
	
	newGameArray = new Array();
	for( let r = 1; r <= gameTotalRound; r++ ){
		console.log( `getGameList: round=${r}, matches=${roundGameArray[ r ]}` );
		// ラウンドの試合数の最小と最大を求める
		let gameStartIndex = 0;
		let gameEndIndex = 0;
		for( let i = 1; i < r + 1; i++ ){
			gameStartIndex += roundGameArray[ i - 1 ] ? roundGameArray[ i - 1 ] : 0;
			gameEndIndex += roundGameArray[ i ] ? roundGameArray[ i ] : 0;
			console.log( `getGameList: ${i} gameStartIndex=${gameStartIndex}, gameEndIndex=${gameEndIndex}` );
		}
		gameStartIndex += 1;
		console.log( `getGameList: round=${r}, gameStartIndex=${gameStartIndex}, gameEndIndex=${gameEndIndex}` );

		// 試合の不足分を補填
		for( let g = gameStartIndex; g <= gameEndIndex; g++ ){
			gameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
			console.log( `getGameList: round=${r}, match=${g}, gameRow=${gameRow}, r=${gameRow[0]}` );
///			console.log( `getGameList: ${newGameList}` );
			if( gameRow[ ROW_ROUND ] != undefined || gameRow[ ROW_ROUND ] != "" ){
				console.log( `round defined` );
				if( gameRow[ ROW_ROUND ] == r ){
					console.log( `round = ${r}` );
					if( gameRow[ ROW_GAMENO ] == g ){
						console.log( `match = ${g}` );
						newGameArray[ g ] = gameRow;
						continue;
					}
					else{
						console.log( `match != ${g}` );
					}
					console.log( `getGameList: round=${r}=${gameRow[0]}, match=${g}, gameData=${newGameList[ g ]}` );
					if( r == 1 ){
					console.log( `round = ${r} = 1` );
						newGameList[ g ] = r + "," + g + "," + g + ",-,-,-,,,,0,,";
						newGameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
						newGameArray[ g ] = newGameRow;
						console.log( `getGameList: round=${r}, match=${g}, gameData=${newGameList[ g ]}` );
					}
					else{
					console.log( `round = ${r} != 1` );
						newGameList[ g ] = r + "," + g + "," + g + ",-,-,-," + lastGameIndex + "," + ( lastGameIndex + 1 ) + ",0,,";
						newGameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
						newGameArray[ g ] = newGameRow;
						console.log( `getGameList: round=${r}, match=${g}, gameData=${newGameList[ g ]}` );
						lastGameIndex = lastGameIndex + 2;
					}
				}
				else{
					console.log( `round != ${r}` );
					console.log( `getGameList: round=${r}=${gameRow[ ROW_ROUND ]}, match=${g}, gameData=${newGameList[ g ]}` );
					if( r == 1 ){
						newGameList[ g ] = r + "," + g + "," + g + ",-,-,-,,,,0,,";
						newGameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
						newGameArray[ g ] = newGameRow;
						console.log( `getGameList: round=${r}, match=${g}, gameData=${newGameList[ g ]}` );
					}
					else{
						newGameList[ g ] = r + "," + g + "," + g + ",-,-,-," + lastGameIndex + "," + ( lastGameIndex + 1 ) + ",0,,";
						newGameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
						newGameArray[ g ] = newGameRow;
						console.log( `getGameList: round=${r}, match=${g}, gameData=${newGameList[ g ]}` );
						lastGameIndex = lastGameIndex + 2;
					}
				}
			}
			else{
				console.log( `undefined` );
				console.log( `getGameList: round=${r}, match=${g}, gameData=undefined` );
					console.log( `getGameList: round=${r}=${gameRow[0]}, match=${g}, gameData=${newGameList[ g ]}` );
					if( r == 1 ){
						newGameList[ g ] = r + "," + g + "," + g + ",-,-,-,,,,0,,";
						newGameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
						newGameArray[ g ] = newGameRow;
						console.log( `getGameList: round=${r}, match=${g}, gameData=${newGameList[ g ]}` );
					}
					else{
						newGameList[ g ] = r + "," + g + "," + g + ",-,-,-," + lastGameIndex + "," + ( lastGameIndex + 1 ) + ",0,,";
						newGameRow = newGameList[ g ] ? newGameList[ g ].split( "," ) : [];
						newGameArray[ g ] = newGameRow;
						console.log( `getGameList: round=${r}, match=${g}, gameData=${newGameList[ g ]}` );
						lastGameIndex = lastGameIndex + 2;
					}
			}
		}
	}
	console.log( newGameList );
	document.getElementById( $gameListId ).value = newGameList.join( "\n" );
	return newGameArray;
}

function kksl2(){
	ikaihan = 1;
	fmstr = "結果の入力<form name='cnp'>";
	memostr = "<hr>会場・時間・得点等<br>";
	for(yx=1;yx<taiou.length;yx++){
		yxt = taiou[yx];
		if(listline[yxt][3]!=""){
			sn = "shiai"+yx;
			yxstr = yx;
			if(yx<10)yxstr = "0"+yx;
			fmstr += yxstr+"<select name='"+sn+"' onChange='kekkatouroku2("+yx+")'>";
			memostr += " "+yxstr+"<input type='text' id='mm"+yx+"' onChange='memoyomi()' value='"+listline[yxt][9]+"'>";
			if(yx%4==0)memostr += "<br>"
			fmstr += "<option value='0'>-</option>";
			if((listline[yxt][3]==listline[yxt][5])&&(listline[yxt][3]!="-")){
				fmstr += "<option value='1' selected>"+listline[yxt][3]+"</option>";
			}else{
				fmstr += "<option value='1'>"+listline[yxt][3]+"</option>";
			}
			if((listline[yxt][4]==listline[yxt][5])&&(listline[yxt][4]!="-")){
				fmstr += "<option value='2' selected>"+listline[yxt][4]+"</option>";
			}else{
				fmstr += "<option value='2'>"+listline[yxt][4]+"</option>";
			}
			fmstr += "</select>";
		}
	}
	ska.innerHTML=fmstr+memostr+"</form>"+"<input type='button' onClick='canvasToImage()' value='画像化'>";
	shinlineseisei()
}


function shinlineseisei(){
	hydai = document.getElementById("tlt").value;
	fkdai = document.getElementById("stm").value;
	rowHeight = document.getElementById("rowHeight").value;
	fontPoint = document.getElementById("fontPoint").value;
	teamListValue = document.getElementById("teamList").value;
	lt = new Array();
	lt = teamListValue.split("\n");
	kmksa = parseInt(rowHeight);
	gameRound = Math.log(lt.length-1)/Math.log(2);
	endlinechk = 0;
	kijyunX = 200;
	kijyunY = 100;
	mojihaba = 180;
	canvas0Context.font = fontPoint+"pt  Arial";
	eg = 800;
	sg = 600;
	newseedline = new Array();
	newuetate = new Array();
	newueyoko = new Array();
	newshitatate = new Array();
	newshitayoko = new Array();
	newgameno = new Array();
	newmemo = new Array();
	entymemza = new Array();
	entriza = new Array();
	console.log("listline.length : " + listline.length );
	console.log("listline[0] : " + listline[0] );
	for(i=1;i<listline.length;i++){
		newgameno[i] = "";
		newseedline[i] = "";
		newuetate[i] =  "";
		newueyoko[i] =  "";
		newshitatate[i] =  "";
		newshitayoko[i] =  "";	
		entriza[i] = "";
		newmemo[i] = ""; 
	}
	endlinestr = "";
	tand = 0;
	for(i=1;i<listline.length;i++){
		tand++;
		if(listline[i][0]==1){
			if(listline[i][4]=="シード"){
				soe = listline[i][1];
				newseedline[soe] = kijyunX+","+(i*kmksa*2+kijyunY-kmksa+(kmksa/2)-1)+",100,2,";
				menb = (i-1)*2+1;
				entriza[menb] = listline[i][3]+","+(kijyunX-mojihaba)+","+((i*kmksa*2+kijyunY-kmksa+(kmksa/2)-1)+(fontPoint/2));
				sg = ((i)*kmksa*2+kijyunY)+(fontPoint/2)+150;
			}else{
				ks = parseInt(listline[i][0]);
				newueyoko[i] = kijyunX+","+(i*kmksa*2+kijyunY-kmksa)+",100,2,";
				menb = (i-1)*2+1;
				entriza[menb] = listline[i][3]+","+(kijyunX-mojihaba)+","+((i*kmksa*2+kijyunY-kmksa)+(fontPoint/2));
				newuetate[i] = (kijyunX+100)+","+(i*kmksa*2+kijyunY-kmksa)+",2,"+(kmksa/2+2)+",";
				newshitatate[i] = (kijyunX+100)+","+(i*kmksa*2+kijyunY-kmksa+(kmksa/2))+",2,"+(kmksa/2+2)+",";
				newshitayoko[i] = kijyunX+","+((i)*kmksa*2+kijyunY)+",100,2,";
				menb = (i-1)*2+2;
				entriza[menb] = listline[i][4]+","+(kijyunX-mojihaba)+","+(((i)*kmksa*2+kijyunY)+(fontPoint/2));
				sg = ((i)*kmksa*2+kijyunY)+(fontPoint/2)+150;
				gm = listline[i][2];
				if(listline[i][9]==""){
					newgameno[gm] = (kijyunX+100-55)+","+(i*kmksa*2+kijyunY-kmksa+(kmksa/2));
				}else{
					newgameno[gm] = -1000+","+(-1000);
				}
				newmemo[gm] = (kijyunX+90)+","+(i*kmksa*2+kijyunY-kmksa+(kmksa/2)+","+listline[i][9]);
			}
			if(listline.length==2){
				endlinechk = 1;
				endlinestr = (kijyunX+100)+","+(i*kmksa*2+kijyunY-kmksa+(kmksa/2))+",100,2";
			}
		}else{
			ks = parseInt(listline[i][0]);
			mt = (Math.pow(2,ks-1)-1);
			mt2 = (Math.pow(2,ks-2)-1);
			tng = Math.pow(2,ks-2);
			kss = ks-1;
			kk = Math.pow(2,ks-1)*2;
			kijyunten = (kmksa*Math.pow(2,ks-2)-1)+(kmksa/2);
			newueyoko[i] = (kijyunX+(kss*100))+","+(((tand-1)*(kmksa*kk))+kijyunY+kijyunten)+",100,2,";
			newshitayoko[i] = (kijyunX+(kss*100))+","+(((tand-0.5)*(kmksa*kk))+kijyunY+kijyunten)+",100,2";
			newuetate[i] = (kijyunX+(kss*100)+100)+","+(((tand-1)*(kmksa*kk))+kijyunY+kijyunten)+",2,"+(kmksa*(tng)+2);
			newshitatate[i] = (kijyunX+(kss*100)+100)+","+(((tand-1)*(kmksa*kk))+kijyunY+kijyunten+(kmksa*(tng)))+",2,"+(kmksa*(tng)+2);
			gm = listline[i][2];
			if(listline[i][9]==""){
				newgameno[gm] = (kijyunX+(kss*100)+55)+","+((((tand-1)*(kmksa*kk))+kijyunY+kijyunten+(kmksa*(tng))));
			}else{
					newgameno[gm] = -1000+","+(-1000);
				}

			newmemo[gm]  = (kijyunX+(kss*100)+90)+","+((((tand-1)*(kmksa*kk))+kijyunY+kijyunten+(kmksa*(tng)))+","+listline[i][9]);
		}
		if(i!=listline.length-1){
			if(listline[i][0]!=listline[i+1][0]){
				tand = 0;
			}
		}else{
			if(endlinechk==0){
				endlinestr = (kijyunX+(kss*100)+100)+","+(((tand-1)*(kmksa*kk))+kijyunY+kijyunten+(kmksa*(tng)))+",100,2";
				eg = (kijyunX+(kss*100)+100)+300;
			}
		}
	}
	if(eg>800){
		class0Canvas.width = eg;
	}else{
		class0Canvas.width = 800;
	}
	if((sg+80)>600){
		document.getElementById("class0CanvasWrapper").style.height = sg+80;
		class0Canvas.height = sg+80;
	}else{
		class0Canvas.height = 600;
		document.getElementById("class0CanvasWrapper").style.height = 600;
	}
	fromlst();
	canvasToImage( "class0Canvas", "canvas0Image" );

}

function createCanvas( $canvasWrapperId, $canvasId, $canvasImageId, $gameListId, $width, $height ){

	// 対象CANVASのラッパー要素を取得
	let targetCanvasWrapper = document.getElementById( $canvasWrapperId );
	// 対象CANVASの要素を取得
	let targetCanvas = document.getElementById( $canvasId );
	// 対象CANVASの2Dコンテキストを取得
	let targetCanvasContext = targetCanvas.getContext( '2d' );
	// CANVASのフォント設定
	targetCanvasContext.font = "10pt Arial";
	// 初期表示モード設定(1:通常表示 2:勝者強調表示)
	displayMode = 2;
	// 入力データの取得
	let gameListValue = document.getElementById( $gameListId ).value;
	// 入力データの配列設定
	let gameListArray = new Array();
	gameListArray = getGameList( $gameListId );
	console.log( `createCanvas: ${$gameListId} : gameListArray = ${gameListArray}` );
	// 行の高さ取得
	let rowHeight = document.getElementById( "rowHeight" ).value;
	let intRowHeight = parseInt( rowHeight );
	// フォントサイズ取得
	let fontPoint = document.getElementById( "fontPoint" ).value;
	targetCanvasContext.font = fontPoint + "pt Arial";
	// 回戦数を取得
	let gameRound1Count = 0;
	for( let i = 1; i < gameListArray.length; i++ ){
		if( gameListArray[ i ][ 0 ] == 1 ){
			gameRound1Count++;
		}
	}
	let gameRound = Math.log( gameRound1Count - 1 ) / Math.log( 2 );
	//
	let newGameNo = new Array();
	let newSeedLine = new Array();
	let newuetate = new Array();
	let newueyoko = new Array();
	let newshitatate = new Array();
	let newshitayoko = new Array();
	let newmemo = new Array();
	let entymemza = new Array();
	entriza = new Array();
	for( let i = 1; i < gameListArray.length; i++ ){
		newGameNo[ i ] = "";
		newSeedLine[ i ] = "";
		newuetate[ i ] =  "";
		newueyoko[ i ] =  "";
		newshitatate[ i ] =  "";
		newshitayoko[ i ] =  "";	
		entriza[ i ] = "";
		newmemo[ i ] = ""; 
	}

	let hydai = document.getElementById("tlt").value;
	let fkdai = document.getElementById("stm").value;
	let endlinechk = 0;
	let kijyunX = 200;
	let kijyunY = 100;
	let mojihaba = 180;
	let eg = 800;
	let sg = 600;
	endlinestr = "";
	let tand = 0;
	const ROW_ROUND = 0;
	const ROW_GAMENO = 1;
	const ROW_TEAM1 = 3;
	const ROW_TEAM2 = 4;
	const ROW_MEMO = 9;
	for( let i = 1; i < gameListArray.length; i++ ){
		tand++;
		console.log( `createCanvas: ${$gameListId} : processing game index=${gameListArray[ i ]}` );
		console.log( `createCanvas: ${$gameListId} : i=${i}, round=${gameListArray[ i ][ ROW_ROUND ]}, game=${gameListArray[ i ][ ROW_GAMENO ]}, team1=${gameListArray[ i ][ ROW_TEAM1 ]}, team2=${gameListArray[ i ][ ROW_TEAM2 ]}` );
		if( gameListArray[ i ][ ROW_ROUND ] == 1 ){
			if( gameListArray[ i ][ ROW_TEAM2 ] == "シード" ){
				let soe = gameListArray[ i ][ ROW_GAMENO ];
				newSeedLine[ soe ] = kijyunX + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight + ( intRowHeight / 2 ) - 1 ) + ",100,2,";
				let menb = ( i - 1 ) * 2 + 1;
				entriza[ menb ] = gameListArray[ i ][ ROW_TEAM1 ] + "," + ( kijyunX - mojihaba ) + "," + ( ( i * intRowHeight * 2 + kijyunY - intRowHeight + ( intRowHeight / 2 ) - 1 ) + ( fontPoint / 2 ) );
				sg = ( ( i ) * intRowHeight * 2 + kijyunY ) + ( fontPoint / 2 ) + 150;
			}
			else{
				let ks = parseInt( gameListArray[ i ][ ROW_ROUND ] );
				newueyoko[ i ] = kijyunX + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight ) + ",100,2,";
				let menb = ( i - 1 ) * 2 + 1;
				entriza[ menb ] = gameListArray[ i ][ ROW_TEAM1 ] + "," + ( kijyunX - mojihaba ) + "," + ( ( i * intRowHeight * 2 + kijyunY - intRowHeight ) + ( fontPoint / 2 ) );
				newuetate[ i ] = ( kijyunX + 100 ) + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight ) + ",2," + ( intRowHeight / 2 + 2 ) + ",";
				newshitatate[ i ] = ( kijyunX + 100 ) + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight + ( intRowHeight / 2 ) ) + ",2," + ( intRowHeight / 2 + 2 ) + ",";
				newshitayoko[ i ] = kijyunX + "," + ( ( i ) * intRowHeight * 2 + kijyunY ) + ",100,2,";
				menb = ( i - 1 ) * 2 + 2;
				entriza[ menb ] = gameListArray[ i ][ ROW_TEAM2 ] + "," + ( kijyunX - mojihaba ) + "," + ( ( i * intRowHeight * 2 + kijyunY ) + ( fontPoint / 2 ) );
				sg = ( ( i ) * intRowHeight * 2 + kijyunY ) + ( fontPoint / 2 ) + 150;
				let gm = gameListArray[ i ][ ROW_GAMENO ];
				if( gameListArray[ i ][ ROW_MEMO ] == "" ){
					newGameNo[ gm ] = ( kijyunX + 100 - 55 ) + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight + ( intRowHeight / 2 ) );
				}else{
					newGameNo[ gm ] = -1000 + "," + ( -1000 );
				}
				newmemo[ gm ] = ( kijyunX + 90 ) + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight + ( intRowHeight / 2 ) ) + "," + gameListArray[ i ][ ROW_MEMO ];
			}
			if( gameListArray.length == 2 ){
				endlinechk = 1;
				endlinestr = ( kijyunX + 100 ) + "," + ( i * intRowHeight * 2 + kijyunY - intRowHeight + ( intRowHeight / 2 ) ) + ",100,2";
			}
		}else{
			let ks = parseInt( gameListArray[ i ][ ROW_ROUND ] );
			let mt = ( Math.pow( 2, ks - 1 ) - 1 );
			let mt2 = ( Math.pow( 2, ks - 2 ) - 1 );
			tng = Math.pow( 2, ks - 2 );
			kss = ks - 1;
			kk = Math.pow( 2, ks - 1 ) * 2;
			kijyunten = ( intRowHeight * Math.pow( 2, ks - 2 ) - 1 ) + ( intRowHeight / 2 );
			newueyoko[ i ] = ( kijyunX + ( kss * 100 ) ) + "," + ( ( ( tand - 1 ) * ( intRowHeight * kk ) ) + kijyunY + kijyunten ) + ",100,2,";
			newshitayoko[ i ] = ( kijyunX + ( kss * 100 ) ) + "," + ( ( ( tand - 0.5 ) * ( intRowHeight * kk ) ) + kijyunY + kijyunten ) + ",100,2";
			newuetate[ i ] = ( kijyunX + ( kss * 100 ) + 100 ) + "," + ( ( ( tand - 1 ) * (intRowHeight * kk ) ) + kijyunY + kijyunten ) + ",2," + ( intRowHeight * ( tng ) + 2 );
			newshitatate[ i ] = ( kijyunX + ( kss * 100 ) + 100 ) + "," + ( ( ( tand - 1 ) * ( intRowHeight * kk ) ) + kijyunY + kijyunten + ( intRowHeight * ( tng ) ) ) + ",2," + ( intRowHeight * ( tng ) + 2 );
			let gm = gameListArray[ i ][ 2 ];
			if( gameListArray[ i ][ ROW_MEMO ] == "" ){
				newGameNo[ gm ] = ( kijyunX + ( kss * 100 ) + 55 ) + "," + ( ( ( tand - 1 ) * ( intRowHeight * kk ) ) + kijyunY + kijyunten + ( intRowHeight * ( tng ) ) );
			}
			else{
				newGameNo[ gm ] = -1000 + "," + ( -1000 );
			}
			newmemo[ gm ]  = ( kijyunX + ( kss * 100 ) + 90 ) + "," + ( ( ( tand - 1 ) * ( intRowHeight * kk ) ) + kijyunY + kijyunten + ( intRowHeight * ( tng ) ) ) + "," + gameListArray[ i ][ ROW_MEMO ];
		}
		if( i != gameListArray.length - 1 ){
			if( gameListArray[ i ][ 0 ] != gameListArray[ i + 1 ][ 0 ] ){
				tand = 0;
			}
		}
		else{
			if( endlinechk == 0 ){
				endlinestr = ( kijyunX + ( kss * 100 ) + 100 ) + "," + ( ( ( tand - 1 ) * ( intRowHeight * kk ) ) + kijyunY + kijyunten + ( intRowHeight * ( tng ) ) ) + ",100,2";
				eg = ( kijyunX + ( kss * 100 ) + 100 ) + 300;
			}
		}
	}
	if( eg > 800 ){
		targetCanvas.width = eg;
	}else{
		targetCanvas.width = 800;
	}
	if((sg+80)>600){
		targetCanvasWrapper.style.height = sg + 80;
		targetCanvas.height = sg + 80;
	}else{
		targetCanvas.height = 600;
		targetCanvasWrapper.style.height = 600;
	}
	setCanvasForm( $canvasId, $gameListId, newSeedLine, newuetate, newueyoko, newshitatate, newshitayoko, newGameNo, newmemo );
	canvasToImage( $canvasId, $canvasImageId );

}
function fromlst(){

	// 塗りつぶしスタイルを白に設定
	canvas0Context.fillStyle = "rgba( 255, 255, 255, 1 )";
	// 矩形の描画(canvas全体を白で塗りつぶす)
	canvas0Context.fillRect( 0, 0, class0Canvas.width, class0Canvas.height );
	// 塗りつぶしスタイルを黒に設定	
	canvas0Context.fillStyle = "rgba( 0, 0, 0, 1 )";
	// テキストフォント設定
	canvas0Context.font = 30 + "pt Arial";
	// テキストベースラインをトップに設定
	canvas0Context.textBaseline = "top";
	// テキストアラインをセンターに設定
	canvas0Context.textAlign = "center";
	// テキスト描画( 表題 ) X:canvas幅の中央 Y:30
///	canvas0Context.fillText( hydai, ( class0Canvas.width / 2 ), 30 );
	// テキストフォント設定
	canvas0Context.font = 20 + "pt Arial";
	// テキストベースラインをトップに設定
	canvas0Context.textBaseline = "top";
	// テキストアラインを右に設定
	canvas0Context.textAlign = "right";
///	canvas0Context.fillText(fkdai,(class0Canvas.width*0.98),80);
	canvas0Context.textBaseline = "bottom";
	canvas0Context.textAlign = "left";
	canvas0Context.font = fontPoint+"pt  Arial";
	nomalcolor = "rgba(070,70,70,1)";
	wincolor = "rgba(255,70,70,1)";
	canvas0Context.fillStyle = nomalcolor;

	for(i=1;i<listline.length;i++){
		console.log( "listline[" + i + "/" + (listline.length-1) + "] : " + listline[ i ] );
		console.log( "newseedline[" + i + "] : " + newseedline[ i ] );
		if(newseedline[i]!=""){
			tmp = new Array();
			tmp = newseedline[i].split(",");
			if(displayMode==2){
				canvas0Context.fillStyle = wincolor;
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],4);
				canvas0Context.fillStyle = nomalcolor;
			}else{
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
		}
		if((displayMode==2)&&(listline[i][8]==1)){
			canvas0Context.fillStyle = wincolor;
			if(newuetate[i]!=""){
				tmp = new Array();
				tmp = newuetate[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],4,tmp[3]);
			}
			if(newueyoko[i]!=""){
				tmp = new Array();
				tmp = newueyoko[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],4);
			}
			canvas0Context.fillStyle = nomalcolor;
		}else if((displayMode==2)&&(listline[i][0]!=1)&&(listline[i][3]!="-")){
			if(newuetate[i]!=""){
				tmp = new Array();
				tmp = newuetate[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newueyoko[i]!=""){
				tmp = new Array();
				tmp = newueyoko[i].split(",");
				canvas0Context.fillStyle = wincolor;
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],4);
				canvas0Context.fillStyle = nomalcolor;
			}
		}else{
			if(newuetate[i]!=""){
				tmp = new Array();
				tmp = newuetate[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newueyoko[i]!=""){
				tmp = new Array();
				tmp = newueyoko[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
		}
		if((displayMode==2)&&(listline[i][8]==2)){
			if(newshitatate[i]!=""){
				tmp = new Array();
				tmp = newshitatate[i].split(",");
				canvas0Context.fillStyle = wincolor;
				canvas0Context.fillRect(tmp[0],tmp[1],4,tmp[3]);
				canvas0Context.fillStyle = nomalcolor;
			}
			if(newshitayoko[i]!=""){
				tmp = new Array();
				tmp = newshitayoko[i].split(",");
				canvas0Context.fillStyle = wincolor;
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],4);
				canvas0Context.fillStyle = nomalcolor;
			}
		}else if((displayMode==2)&&(listline[i][0]!=1)&&(listline[i][4]!="-")){
			if(newshitatate[i]!=""){
				tmp = new Array();
				tmp = newshitatate[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newshitayoko[i]!=""){
				tmp = new Array();
				tmp = newshitayoko[i].split(",");
				canvas0Context.fillStyle = wincolor;
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],4);
				canvas0Context.fillStyle = nomalcolor;
			}
		}else{
			if(newshitatate[i]!=""){
				tmp = new Array();
				tmp = newshitatate[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newshitayoko[i]!=""){
				tmp = new Array();
				tmp = newshitayoko[i].split(",");
				canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
		}
	}
	ksk = listline.length-1
	if(endlinestr !=""){
		tmp = new Array();
		tmp = endlinestr.split(",");
		if((displayMode==2)&&(listline[ksk][5]!="-")){
			canvas0Context.fillStyle = wincolor;
			canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],4);
			canvas0Context.fillStyle = nomalcolor;
			canvas0Context.fillText(listline[ksk][5],(0+parseInt(tmp[0])+120),(0+parseInt(tmp[1])+(fontPoint/2)));
		}else{
			canvas0Context.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
		}
	}
	canvas0Context.textBaseline = "middle";
	canvas0Context.textAlign = "left";
	for(i=1;i<newgameno.length;i++){
		if(newgameno[i]!=""){
			tmp = new Array();
			tmp = newgameno[i].split(",");
			canvas0Context.fillText(i,tmp[0],tmp[1]);
		}
	}

	
	canvas0Context.textAlign = "right";
	canvas0Context.font = (fontPoint/2)+"pt  Arial";
	for(i=1;i<newmemo.length;i++){
		if(newmemo[i]!=""){
			tmp = new Array();
			tmp = newmemo[i].split(",");
			canvas0Context.fillText(tmp[2],tmp[0],tmp[1]);
		}
	}

	canvas0Context.textBaseline = "bottom";
	canvas0Context.textAlign = "left";
	canvas0Context.font = fontPoint+"pt  Arial";

	for(i=1;i<entriza.length;i++){
		if(entriza[i]!=""){
			tmp = new Array();
			tmp = entriza[i].split(",");
			canvas0Context.fillText(tmp[0],tmp[1],tmp[2]);
		}
	}
	renderTournamentTable();
}
function setCanvasForm( $canvasId, $gameListId, $newSeedLine, $newuetate, $newueyoko, $newshitatate, $newshitayoko, $newgameno, $newmemo ){
	
// CANVAS(class1Canvas)の取得
	targetCanvas = document.getElementById( $canvasId );
	// 2Dコンテキストの取得
	canvasContext = targetCanvas.getContext( '2d' );
	// 初期表示モード設定(1:通常表示 2:勝者強調表示)
	displayMode = 2;
	// 入力データの取得
	gameListValue = document.getElementById( $gameListId ).value;
	gameListArray = new Array();
	gameListArray = getGameList( $gameListId );

	// 塗りつぶしスタイルを白に設定
	canvasContext.fillStyle = "rgba( 255, 255, 255, 1 )";
	// 矩形の描画(canvas全体を白で塗りつぶす)
	canvasContext.fillRect( 0, 0, targetCanvas.width, targetCanvas.height );
	// 塗りつぶしスタイルを黒に設定	
	canvasContext.fillStyle = "rgba( 0, 0, 0, 1 )";
	// テキストフォント設定
	canvasContext.font = 10 + "pt Arial";
	// テキストベースラインをトップに設定
	canvasContext.textBaseline = "top";
	// テキストアラインをセンターに設定
	canvasContext.textAlign = "center";
	// テキスト描画( 表題 ) X:canvas幅の中央 Y:30
///	canvasContext.fillText( hydai, ( targetCanvas.width / 2 ), 30 );
	// テキストフォント設定
	canvasContext.font = 10 + "pt Arial";
	// テキストベースラインをトップに設定
	canvasContext.textBaseline = "top";
	// テキストアラインを右に設定
	canvasContext.textAlign = "right";
///	canvasContext.fillText(fkdai,(targetCanvas.width*0.98),80);
	canvasContext.textBaseline = "bottom";
	canvasContext.textAlign = "left";
	canvasContext.font = fontPoint+"pt  Arial";
	nomalcolor = "rgba(070,70,70,1)";
	wincolor = "rgba(255,70,70,1)";
	canvasContext.fillStyle = nomalcolor;

	const CANVAS_X = 0;
	const CANVAS_Y = 1;
	const CANVAS_Width = 2;
	const CANVAS_Height = 3;

	for( i = 1; i < gameListArray.length; i++ ){
		console.log( "canvasId: " + $canvasId + " listline[" + i + "/" + (gameListArray.length-1) + "] : " + gameListArray[ i ] );
		console.log( "newseedline[" + i + "] : " + $newSeedLine[ i ] );
		if( $newSeedLine[ i ] != "" ){
			tmp = new Array();
			tmp = $newSeedLine[ i ].split( "," );
			if( displayMode == 2 ){
				canvasContext.fillStyle = wincolor;
				canvasContext.fillRect( tmp[ CANVAS_X ], tmp[ CANVAS_Y ], tmp[ CANVAS_Width ], 4 );
				canvasContext.fillStyle = nomalcolor;
			}else{
				canvasContext.fillRect( tmp[ CANVAS_X ], tmp[ CANVAS_Y ], tmp[ CANVAS_Width ], tmp[ CANVAS_Height ]);
			}
		}
		if( ( displayMode == 2 ) && ( gameListArray[ i ][ 8 ] == 1 ) ){
			canvasContext.fillStyle = wincolor;
			if( $newuetate[ i ] != "" ){
				tmp = new Array();
				tmp = $newuetate[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], 4, tmp[ 3 ] );
			}
			if( $newueyoko[ i ] != "" ){
				tmp = new Array();
				tmp = $newueyoko[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], 4 );
			}
			canvasContext.fillStyle = nomalcolor;
		}
		else if( ( displayMode == 2 ) && ( gameListArray[ i ][ 0 ] != 1 ) && ( gameListArray[ i ][ 3 ] != "-" ) ){
			if( $newuetate[ i ] != "" ){
				tmp = new Array();
				tmp = $newuetate[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
			}
			if( $newueyoko[ i ] != "" ){
				tmp = new Array();
				tmp = $newueyoko[ i ].split( "," );
				canvasContext.fillStyle = wincolor;
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], 4 );
				canvasContext.fillStyle = nomalcolor;
			}
		}
		else{
			if( $newuetate[ i ] != "" ){
				tmp = new Array();
				tmp = $newuetate[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
			}
			if( $newueyoko[ i ] != "" ){
				tmp = new Array();
				tmp = $newueyoko[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
			}
		}
		if( ( displayMode == 2 ) && ( gameListArray[ i ][ 8 ] == 2 ) ){
			if( $newshitatate[ i ] != "" ){
				tmp = new Array();
				tmp = $newshitatate[ i ].split( "," );
				canvasContext.fillStyle = wincolor;
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], 4, tmp[ 3 ] );
				canvasContext.fillStyle = nomalcolor;
			}
			if( $newshitayoko[ i ] != "" ){
				tmp = new Array();
				tmp = $newshitayoko[ i ].split( "," );
				canvasContext.fillStyle = wincolor;
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], 4 );
				canvasContext.fillStyle = nomalcolor;
			}
		}
		else if( ( displayMode == 2 ) && ( gameListArray[ i ][ 0 ] != 1 ) && ( gameListArray[ i ][ 4 ] != "-" ) ){
			if( $newshitatate[ i ] != "" ){
				tmp = new Array();
				tmp = $newshitatate[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
			}
			if( $newshitayoko[ i ] != "" ){
				tmp = new Array();
				tmp = $newshitayoko[ i ].split( "," );
				canvasContext.fillStyle = wincolor;
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], 4 );
				canvasContext.fillStyle = nomalcolor;
			}
		}
		else{
			if( $newshitatate[ i ] != "" ){
				tmp = new Array();
				tmp = $newshitatate[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
			}
			if( $newshitayoko[ i ] != "" ){
				tmp = new Array();
				tmp = $newshitayoko[ i ].split( "," );
				canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
			}
		}
	}
	ksk = gameListArray.length - 1
	if( endlinestr != "" ){
		tmp = new Array();
		tmp = endlinestr.split( "," );
		if( ( displayMode == 2 ) && ( gameListArray[ ksk ][ 5 ] != "-" ) ){
			canvasContext.fillStyle = wincolor;
			canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], 4 );
			canvasContext.fillStyle = nomalcolor;
			canvasContext.fillText( gameListArray[ ksk ][ 5 ], ( 0 + parseInt( tmp[ 0 ] ) + 120 ), ( 0 + parseInt( tmp[ 1 ] ) + ( fontPoint / 2 ) ) );
		}
		else{
			canvasContext.fillRect( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ], tmp[ 3 ] );
		}
	}
	canvasContext.textBaseline = "middle";
	canvasContext.textAlign = "left";
	for( i = 1; i < $newgameno.length; i++ ){
		if( $newgameno[ i ] != "" ){
			tmp = new Array();
			tmp = $newgameno[ i ].split( "," );
			canvasContext.fillText( i, tmp[ 0 ], tmp[ 1 ] );
		}
	}

	canvasContext.textAlign = "right";
	canvasContext.font = ( fontPoint / 2 ) + "pt Arial";
	for( i = 1; i < $newmemo.length; i++ ){
		if( $newmemo[ i ] != "" ){
			tmp = new Array();
			tmp = $newmemo[ i ].split( "," );
			canvasContext.fillText( tmp[ 2 ], tmp[ 0 ], tmp[ 1 ] );
		}
	}

	canvasContext.textBaseline = "bottom";
	canvasContext.textAlign = "left";
	canvasContext.font = fontPoint+"pt  Arial";

	for( i = 1; i < entriza.length; i++ ){
		if( entriza[ i ] != "" ){
			tmp = new Array();
			tmp = entriza[ i ].split( "," );
			canvasContext.fillText( tmp[ 0 ], tmp[ 1 ], tmp[ 2 ] );
		}
	}
}

// listline配列からトーナメント表をtableで表示する関数（未呼び出し）
function renderTournamentTable() {
	if (typeof listline === 'undefined' || !Array.isArray(listline) || listline.length < 2) {
		document.getElementById('tournament-table').innerHTML = '<div style="color:red;">データがありません</div>';
		return;
	}
	let html = '<table border="1" style="margin:auto; border-collapse:collapse; min-width:500px;">';
	html += '<tr style="background:#eee;"><th>ラウンド</th><th>番号</th><th>チーム1</th><th>チーム2</th><th>勝者</th></tr>';
	for (let i = 1; i < listline.length; i++) {
		let row = listline[i];
		if (!row || row.length < 11) continue;
		html += '<tr>';
		html += `<td>${row[0]}</td>`; // ラウンド
		html += `<td>${row[1]}</td>`; // 番号
		html += `<td>${row[3]}</td>`; // チーム1
		html += `<td>${row[4]}</td>`; // チーム2
		html += `<td>${row[5]}</td>`; // 勝者
		html += `<td>${row[6]}</td>`; // 元チーム1
		html += `<td>${row[7]}</td>`; // 元チーム2
		html += `<td>${row[8]}</td>`; // 勝者
		html += `<td>${row[9]}</td>`; // 勝者
		html += `<td>${row[10]}</td>`; // 勝者
		html += '</tr>';
	}
	html += '</table>';
	document.getElementById('tournament-table').innerHTML = html;
}

function sakusei(vn){
	if(document.getElementById("md").value=="非表示→表示"){
		displayMode = 2;
		document.getElementById("md").value = "表示→非表示";
	}else{
		displayMode = 1;
		document.getElementById("md").value = "非表示→表示";
	}
	kksl2()
}
function memoyomi(){
	for(yx=1;yx<taiou.length;yx++){
		tstr = document.getElementById('mm'+yx).value;
		yxt = taiou[yx];
		listline[yxt][9]=tstr;
	}
	listhenkoub()
}
function kekkatouroku2(ban){
	for(yyy=1;yyy<=zens;yyy++){
		skarray[yyy] = document.cnp.elements[yyy-1].options[document.cnp.elements[yyy-1].selectedIndex].value;
		if(yyy == ban){
			skz = parseInt(skarray[yyy]);
			asa = 0+skz+2;
			yxt = taiou[yyy];
			listline[yxt][5]=listline[yxt][asa];
			listline[yxt][8]=skz;
		}
	}
	listhenkoub()
}

function canvasToImage( $canvasId, $canvasImageId ){
	canvasImageUrl = document.getElementById( $canvasId ).toDataURL( "image/jpeg" );
	window.document.getElementById( $canvasImageId ).setAttribute( 'src', canvasImageUrl );
	document.getElementById( $canvasId ).style.display = "none";

}
function sampledl(){
	document.getElementById("tlt").value="";
	document.getElementById("stm").value="";
}
</script>
