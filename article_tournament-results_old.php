<!-- Article Tournament Results Start -->
<article class="tournament-results" id="tournament-results">
	<div class="container">
		<h2>試合結果</h2>
		<div class="tournament-results-content">
			<!-- Form -->
			<form method="post" action=""  >
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
		$g_Log->notice( "Team Name : " . $team[ 'team_name' ] , __FUNCTION__, basename( __FILE__ ) );
							echo htmlspecialchars( $team[ 'team_name' ], ENT_QUOTES, 'UTF-8' ) . "\n";
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
				<input type="hidden" id="fontPoint"  value="12" size="2" onchange="shinlineseisei()">
				結果線<input type="button" id="md" value="非表示→表示" onclick="sakusei(1)">
			</div>
			<div id="ska" style="display:none;">　</div>
			<hr style="page-break-before: always;color:silver;">
			<div id="back"  style="display: flex; justify-content: center;" >
<!--			<div id="back"  style="width:800px;height:600px" >	-->
				<canvas id="mainCanvas" width="800" height="600" style="position:absolute;margin:0px;padding:0px"></canvas>
			</div>
			<hr style="page-break-before: always;color:silver;">
			<hr>
			<img id="kekka">
			<br>
			<textarea id="tvew" cols="70" rows="30" style="display:none;">
			</textarea>

		</div>
	</div>
</article>
<!-- Article Tournament Results End -->

<script>
// BODY読み込み時処理
window.addEventListener('load', () => {
	onload_Body();
	create_Tornament();
});

function onload_Body(){
	// CANVAS(mainCanvas)の取得
	cv = document.getElementById( 'mainCanvas' );
	// 2Dコンテキストの取得
	ct = cv.getContext( '2d' );
	// フォント設定
	ct.font = "10pt Arial";
	// 初期表示モード設定
	hmode = 1;
}

// トーナメント表作成
function create_Tornament(){
	// 変数宣言
	teamArray = new Array();
	taiou = new Array();
	liststr = "";
	minashi = 0;
	jitu = 0;
	listline = new Array();
	ruiseki = 0;

	// 入力データの取得
	teamListValue = document.getElementById( "teamList" ).value;
	// データ行ごとに分割(タブを削除してから改行で分割)
	teamArray = teamListValue.replace(/\t/g, "").split( "\n" );

	console.log( "データ件数 : " + teamArray.length );
///let index = 0;
///teamArray.forEach(team => {
///    index++;
///    console.log("データ[" + index + "] : " + team);
///});

	// 回戦数を取得
	kaisen = Math.log( teamArray.length - 1 ) / Math.log( 2 );

	console.log( "回戦数 : " + kaisen );

	// チーム配列をループ
	for( i = 0; i < teamArray.length; i++ ){
		
		console.log( "処理中データ[" + i + "] : " + teamArray[ i ] );

		if( teamArray[ i ] != "" ){	// チーム名が空でない場合
			
			if( i % 2 == 0 ){	// 偶数行の場合
				
				minashi++;

				if( teamArray[ i + 1 ] == "シード" ){	// 次の行がシードの場合
					litmp = "1," + minashi + ",s," + teamArray[ i ] + ",シード," + teamArray[ i ] + ",,,1,,";
				}else{
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

	for( x = 2; x <= kaisen; x++ ){

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

function sakusei(vn){
	if(document.getElementById("md").value=="非表示→表示"){
		hmode = 2;
		document.getElementById("md").value = "表示→非表示";
	}else{
		hmode = 1;
		document.getElementById("md").value = "非表示→表示";
	}
	kksl2()
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
	ska.innerHTML=fmstr+memostr+"</form>"+"<input type='button' onClick='gazouka()' value='画像化'>";
	shinlineseisei()
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

function gazouka(){
	gul = cv.toDataURL("image/jpeg");
	window.document.getElementById('kekka').setAttribute('src', gul);
}

function shinlineseisei(){
	hydai = document.getElementById("tlt").value;
	fkdai = document.getElementById("stm").value;
	hb = document.getElementById("rowHeight").value;
	fzz = document.getElementById("fontPoint").value;
	teamListValue = document.getElementById("teamList").value;
	lt = new Array();
	lt = teamListValue.split("\n");
	kmksa = parseInt(hb);
	kaisen = Math.log(lt.length-1)/Math.log(2);
	endlinechk = 0;
	kijyunX = 200;
	kijyunY = 100;
	mojihaba = 180;
	ct.font = fzz+"pt  Arial";
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
				entriza[menb] = listline[i][3]+","+(kijyunX-mojihaba)+","+((i*kmksa*2+kijyunY-kmksa+(kmksa/2)-1)+(fzz/2));
				sg = ((i)*kmksa*2+kijyunY)+(fzz/2)+150;
			}else{
				ks = parseInt(listline[i][0]);
				newueyoko[i] = kijyunX+","+(i*kmksa*2+kijyunY-kmksa)+",100,2,";
				menb = (i-1)*2+1;
				entriza[menb] = listline[i][3]+","+(kijyunX-mojihaba)+","+((i*kmksa*2+kijyunY-kmksa)+(fzz/2));
				newuetate[i] = (kijyunX+100)+","+(i*kmksa*2+kijyunY-kmksa)+",2,"+(kmksa/2+2)+",";
				newshitatate[i] = (kijyunX+100)+","+(i*kmksa*2+kijyunY-kmksa+(kmksa/2))+",2,"+(kmksa/2+2)+",";
				newshitayoko[i] = kijyunX+","+((i)*kmksa*2+kijyunY)+",100,2,";
				menb = (i-1)*2+2;
				entriza[menb] = listline[i][4]+","+(kijyunX-mojihaba)+","+(((i)*kmksa*2+kijyunY)+(fzz/2));
				sg = ((i)*kmksa*2+kijyunY)+(fzz/2)+150;
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
		cv.width = eg;
	}else{
		cv.width = 800;
	}
	if((sg+80)>600){
		document.getElementById("back").style.height = sg+80;
		cv.height = sg+80;
	}else{
		cv.height = 600;
		document.getElementById("back").style.height = 600;
	}
	fromlst()
}
function fromlst(){

	// 塗りつぶしスタイルを白に設定
	ct.fillStyle = "rgba( 255, 255, 255, 1 )";
	// 矩形の描画(canvas全体を白で塗りつぶす)
	ct.fillRect( 0, 0, cv.width, cv.height );
	// 塗りつぶしスタイルを黒に設定	
	ct.fillStyle = "rgba( 0, 0, 0, 1 )";
	// テキストフォント設定
	ct.font = 30 + "pt Arial";
	// テキストベースラインをトップに設定
	ct.textBaseline = "top";
	// テキストアラインをセンターに設定
	ct.textAlign = "center";
	// テキスト描画( 表題 ) X:canvas幅の中央 Y:30
///	ct.fillText( hydai, ( cv.width / 2 ), 30 );
	// テキストフォント設定
	ct.font = 20 + "pt Arial";
	// テキストベースラインをトップに設定
	ct.textBaseline = "top";
	// テキストアラインを右に設定
	ct.textAlign = "right";
///	ct.fillText(fkdai,(cv.width*0.98),80);
	ct.textBaseline = "bottom";
	ct.textAlign = "left";
	ct.font = fzz+"pt  Arial";
	nomalcolor = "rgba(070,70,70,1)";
	wincolor = "rgba(255,70,70,1)";
	ct.fillStyle = nomalcolor;
	for(i=1;i<listline.length;i++){
		if(newseedline[i]!=""){
			tmp = new Array();
			tmp = newseedline[i].split(",");
			if(hmode==2){
				ct.fillStyle = wincolor;
				ct.fillRect(tmp[0],tmp[1],tmp[2],4);
				ct.fillStyle = nomalcolor;
			}else{
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
		}
		if((hmode==2)&&(listline[i][8]==1)){
			ct.fillStyle = wincolor;
			if(newuetate[i]!=""){
				tmp = new Array();
				tmp = newuetate[i].split(",");
				ct.fillRect(tmp[0],tmp[1],4,tmp[3]);
			}
			if(newueyoko[i]!=""){
				tmp = new Array();
				tmp = newueyoko[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],4);
			}
			ct.fillStyle = nomalcolor;
		}else if((hmode==2)&&(listline[i][0]!=1)&&(listline[i][3]!="-")){
			if(newuetate[i]!=""){
				tmp = new Array();
				tmp = newuetate[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newueyoko[i]!=""){
				tmp = new Array();
				tmp = newueyoko[i].split(",");
				ct.fillStyle = wincolor;
				ct.fillRect(tmp[0],tmp[1],tmp[2],4);
				ct.fillStyle = nomalcolor;
			}
		}else{
			if(newuetate[i]!=""){
				tmp = new Array();
				tmp = newuetate[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newueyoko[i]!=""){
				tmp = new Array();
				tmp = newueyoko[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
		}
		if((hmode==2)&&(listline[i][8]==2)){
			if(newshitatate[i]!=""){
				tmp = new Array();
				tmp = newshitatate[i].split(",");
				ct.fillStyle = wincolor;
				ct.fillRect(tmp[0],tmp[1],4,tmp[3]);
				ct.fillStyle = nomalcolor;
			}
			if(newshitayoko[i]!=""){
				tmp = new Array();
				tmp = newshitayoko[i].split(",");
				ct.fillStyle = wincolor;
				ct.fillRect(tmp[0],tmp[1],tmp[2],4);
				ct.fillStyle = nomalcolor;
			}
		}else if((hmode==2)&&(listline[i][0]!=1)&&(listline[i][4]!="-")){
			if(newshitatate[i]!=""){
				tmp = new Array();
				tmp = newshitatate[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newshitayoko[i]!=""){
				tmp = new Array();
				tmp = newshitayoko[i].split(",");
				ct.fillStyle = wincolor;
				ct.fillRect(tmp[0],tmp[1],tmp[2],4);
				ct.fillStyle = nomalcolor;
			}
		}else{
			if(newshitatate[i]!=""){
				tmp = new Array();
				tmp = newshitatate[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
			if(newshitayoko[i]!=""){
				tmp = new Array();
				tmp = newshitayoko[i].split(",");
				ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
			}
		}
	}
	ksk = listline.length-1
	if(endlinestr !=""){
		tmp = new Array();
		tmp = endlinestr.split(",");
		if((hmode==2)&&(listline[ksk][5]!="-")){
			ct.fillStyle = wincolor;
			ct.fillRect(tmp[0],tmp[1],tmp[2],4);
			ct.fillStyle = nomalcolor;
			ct.fillText(listline[ksk][5],(0+parseInt(tmp[0])+120),(0+parseInt(tmp[1])+(fzz/2)));
		}else{
			ct.fillRect(tmp[0],tmp[1],tmp[2],tmp[3]);
		}
	}
	ct.textBaseline = "middle";
	ct.textAlign = "left";
	for(i=1;i<newgameno.length;i++){
		if(newgameno[i]!=""){
			tmp = new Array();
			tmp = newgameno[i].split(",");
			ct.fillText(i,tmp[0],tmp[1]);
		}
	}

	
	ct.textAlign = "right";
	ct.font = (fzz/2)+"pt  Arial";
	for(i=1;i<newmemo.length;i++){
		if(newmemo[i]!=""){
			tmp = new Array();
			tmp = newmemo[i].split(",");
			ct.fillText(tmp[2],tmp[0],tmp[1]);
		}
	}

	ct.textBaseline = "bottom";
	ct.textAlign = "left";
	ct.font = fzz+"pt  Arial";

	for(i=1;i<entriza.length;i++){
		if(entriza[i]!=""){
			tmp = new Array();
			tmp = entriza[i].split(",");
			ct.fillText(tmp[0],tmp[1],tmp[2]);
		}
	}
}
function sampledl(){
	document.getElementById("tlt").value="";
	document.getElementById("stm").value="";
}
</script>
