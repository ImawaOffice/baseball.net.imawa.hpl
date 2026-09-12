// ハンバーガーメニュー開閉制御
console.log( 'baseball.js' );
document.addEventListener( 'DOMContentLoaded', function() {
	console.log( 'DOMContentLoaded' );
	var navToggle = document.querySelector( '.nav-toggle' );
	var navMenu = document.querySelector( '.nav-menu' );
	if ( navToggle && navMenu ) {
		console.log( 'addEventListener' );
		navToggle.addEventListener( 'click', function() {
			console.log( 'Toggle navigation menu' );
			navMenu.classList.toggle( 'open' );
		});
	}
});

// 権限マスタ管理画面
// 動的に読み込まれた画面でも動作するようイベント委譲を使用する。
document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '#role-list .edit-button' );
	if ( !button ) {
		return;
	}

	var row = button.closest( 'tr' );
	var roleList = document.getElementById( 'role-list' );
	if ( !row || !roleList ) {
		return;
	}

	var action = roleList.querySelector( '#action' );
	var roleId = roleList.querySelector( '#role_id' );
	var roleName = roleList.querySelector( '#role_name' );
	var roleLevel = roleList.querySelector( '#role_level' );
	var isEnabled = roleList.querySelector( '#is_enabled' );
	var saveButton = roleList.querySelector( '#save_button' );

	if ( !action || !roleId || !roleName || !roleLevel || !isEnabled || !saveButton ) {
		return;
	}

	action.value = 'update';
	roleId.value = row.dataset.roleId || '';
	roleName.value = row.dataset.roleName || '';
	roleLevel.value = row.dataset.roleLevel || '';
	roleLevel.readOnly = true;
	isEnabled.checked = row.dataset.isEnabled === '1';
	saveButton.textContent = '更新';
	roleName.focus();
} );

document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '#role-list #cancel_button' );
	if ( !button ) {
		return;
	}

	var roleList = document.getElementById( 'role-list' );
	if ( !roleList ) {
		return;
	}

	var action = roleList.querySelector( '#action' );
	var roleId = roleList.querySelector( '#role_id' );
	var roleName = roleList.querySelector( '#role_name' );
	var roleLevel = roleList.querySelector( '#role_level' );
	var isEnabled = roleList.querySelector( '#is_enabled' );
	var saveButton = roleList.querySelector( '#save_button' );

	if ( !action || !roleId || !roleName || !roleLevel || !isEnabled || !saveButton ) {
		return;
	}

	action.value = 'add';
	roleId.value = '';
	roleName.value = '';
	roleLevel.value = '';
	roleLevel.readOnly = false;
	isEnabled.checked = true;
	saveButton.textContent = '登録';
	roleName.focus();
} );

async function PDFtoImage( $p_PDF, $p_CanvasId, $p_ImageId ){
	const url = $p_PDF;
	console.log( "PDF URL: " + url );

	if ( !url ) {
		console.warn( "PDF URL is empty. Skipping PDF rendering." );
		return;
	}

	pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';

	const pdf = await pdfjsLib.getDocument( url ).promise;

	const container = document.getElementById( $p_ImageId );
	container.innerHTML = "";

	const canvas = document.getElementById( $p_CanvasId );
	const ctx = canvas.getContext( '2d' );

	const page = await pdf.getPage( 1 );
	const scale = 2.0;

	for ( let pageNo = 1; pageNo <= pdf.numPages; pageNo++ ) {
		const page = await pdf.getPage( pageNo );
		const viewport = page.getViewport( { scale } );
		canvas.width = Math.floor( viewport.width );
		canvas.height = Math.floor( viewport.height );

		await page.render( { canvasContext: ctx, viewport } ).promise;

		const img = document.createElement( "img" );
		img.alt = `page-${pageNo}`;
		img.loading = "lazy";
		img.style.display = "block";
		img.style.width = "100%";
		img.src = canvas.toDataURL( "image/png" );
		container.appendChild( img );
	}
}
