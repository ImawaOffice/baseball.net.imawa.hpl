<?php
foreach( $_SERVER as $key => $value ) {
	echo( "SERVER : " . $key . " = " . var_export( $value, true ) . "<br>\n" );
}
?>
