<!-- Page Header Start -->
<header class="page-header">
		<button class="nav-toggle" aria-label="メニューを開く"><span></span></button>
	<div class="container">
		<nav>
			<ul class="nav-menu">
				<?php foreach ( $GLOBALS[ "HEADER_MENU" ] as $menu ) : ?>
					<?php if( isset( $menu[ "children" ] ) && count( $menu[ "children" ] ) > 0 ) : ?>
						<li class="dropdown">
							<a href='<?php echo $menu[ "menu_url" ] ?>' class="dropdown-toggle"><?php echo $menu[ "menu_name" ] ?> ▼</a>
							<ul class="dropdown-menu">
								<?php foreach ( $menu[ "children" ] as $childmenu ) : ?>
									<li><a href='<?php echo $childmenu[ "menu_url" ] ?>'><?php echo $childmenu[ "menu_name" ] ?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php else : ?>
						<li><a href='<?php echo $menu[ "menu_url" ] ?>'><?php echo $menu[ "menu_name" ] ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</nav>
	</div>
</header>
<!-- Page Header End -->
