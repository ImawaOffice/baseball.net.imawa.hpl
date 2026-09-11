<!-- Page Footer Start -->
<footer class="page-footer">
	<nav class="footer-nav">
		<div class="container">
			<ul>
				<?php foreach ( $GLOBALS[ "FOOTER_MENU" ] as $menu ) : ?>
					<li><a href='<?php echo $menu[ "menu_url" ] ?>'><?php echo $menu[ "menu_name" ] ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>
	<div class="copyright">
		<div class="container">
			<span><?php echo isset( $GLOBALS[ "CONFIG" ][ "CopyRight" ] ) ? $GLOBALS[ "CONFIG" ][ "CopyRight" ] : ""; ?></span>
		</div>
	</div>
</footer>
<!-- Page Footer End -->
