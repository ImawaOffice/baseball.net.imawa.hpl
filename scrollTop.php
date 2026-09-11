<!-- Scroll to Top Button Start -->
 <style type="text/css">
#scrollTopBtn {
	position: fixed;
	bottom: 20px;
	right: 20px;
	width: 48px;
	height: 48px;
	background: #333;
	color: #fff;
	border: none;
	border-radius: 50%;
	cursor: pointer;
	display: none; /* 初期は非表示 */
	box-shadow: 0 2px 8px rgba(0,0,0,0.15);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 28px;
	transition: background 0.2s, box-shadow 0.2s;
	z-index: 1000;
}
#scrollTopBtn::before {
	content: '\25B2'; /* 上向き三角 */
	font-size: 28px;
	display: block;
	line-height: 1;
}
#scrollTopBtn:hover {
	background-color: #555;
	box-shadow: 0 4px 16px rgba(0,0,0,0.25);
}
</style>

<button id="scrollTopBtn" aria-label="ページのトップへ戻る"></button>

<script>
// トップへ戻るボタン
const scrollBtn = document.getElementById( 'scrollTopBtn' );

// スクロール位置によってボタン表示
window.addEventListener('scroll', () => {
	if ( window.scrollY > 200 ) {
		scrollBtn.style.display = 'block';
	} else {
		scrollBtn.style.display = 'none';
	}
});

// クリックでトップへスムーズスクロール
scrollBtn.addEventListener( 'click', () => {
	window.scrollTo({
		top: 0,
		behavior: 'smooth'
	});
});
</script>
<!-- Scroll to Top Button End -->
 