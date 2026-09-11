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

