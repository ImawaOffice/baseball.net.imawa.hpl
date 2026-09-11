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

	// “画像として耐える”ように、最初は少し高めに描画
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
