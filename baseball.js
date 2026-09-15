// ハンバーガーメニュー開閉制御
console.log( 'baseball.js' );
document.addEventListener( 'DOMContentLoaded', function() {
	console.log( 'DOMContentLoaded' );
	var navToggle = document.querySelector( '.nav-toggle' );
	var navMenu = document.querySelector( '.nav-menu' );
	var dropdownItems = document.querySelectorAll( '.nav-menu .dropdown' );

	function closeAllDropdowns() {
		dropdownItems.forEach( function( item ) {
			item.classList.remove( 'open' );
		} );
	}

	if ( navToggle && navMenu ) {
		console.log( 'addEventListener' );
		navToggle.addEventListener( 'click', function() {
			console.log( 'Toggle navigation menu' );
			var isOpen = navMenu.classList.toggle( 'open' );
			if ( !isOpen ) {
				closeAllDropdowns();
			}
		});
	}

	var dropdownToggles = document.querySelectorAll( '.dropdown-toggle' );
	dropdownToggles.forEach( function( toggle ) {
		toggle.addEventListener( 'click', function( event ) {
			var width = window.innerWidth || document.documentElement.clientWidth;
			if ( width > 900 ) {
				return;
			}
			event.preventDefault();
			event.stopPropagation();
			var parent = toggle.closest( '.dropdown' );
			if ( parent ) {
				var willOpen = !parent.classList.contains( 'open' );
				closeAllDropdowns();
				if ( willOpen ) {
					parent.classList.add( 'open' );
					if ( typeof parent.scrollIntoView === 'function' ) {
						parent.scrollIntoView( { block: 'nearest' } );
					}
				}
			}
		});
	} );

	document.addEventListener( 'click', function( event ) {
		var width = window.innerWidth || document.documentElement.clientWidth;
		if ( width > 900 ) {
			return;
		}

		if ( event.target.closest( '.nav-menu .dropdown' ) ) {
			return;
		}

		closeAllDropdowns();
	} );
});

// 権限マスタ管理画面
// 画面が後からDOMへ追加される場合にも対応するため、documentへイベント委譲する。
function resetRoleFormState() {
	var roleList = document.getElementById( 'role-list' );
	if ( !roleList ) {
		return;
	}

	var form = roleList.querySelector( '#roleForm' );
	if ( !form ) {
		return;
	}

	form.dataset.locked = '0';
	form.classList.remove( 'is-submitting' );

	var saveButton = form.querySelector( '#save_button' );
	if ( saveButton ) {
		saveButton.disabled = false;
		saveButton.textContent = '登録';
	}

	form.querySelectorAll( 'input, button, select' ).forEach( function( element ) {
		element.disabled = false;
	} );

	var dialog = document.getElementById( 'roleDialog' );
	if ( dialog ) {
		dialog.classList.remove( 'is-processing' );
	}
}

function openRoleDialog( mode, row ) {
	var roleList = document.getElementById( 'role-list' );
	var dialog = document.getElementById( 'roleDialog' );
	if ( !roleList || !dialog ) {
		return;
	}

	resetRoleFormState();

	var action = roleList.querySelector( '#action' );
	var roleId = roleList.querySelector( '#role_id' );
	var roleName = roleList.querySelector( '#role_name' );
	var roleLevel = roleList.querySelector( '#role_level' );
	var isEnabled = roleList.querySelector( '#is_enabled' );
	var saveButton = roleList.querySelector( '#save_button' );
	var title = roleList.querySelector( '#roleDialogTitle' );

	if ( !action || !roleId || !roleName || !roleLevel || !isEnabled || !saveButton || !title ) {
		console.error( 'role-list: form elements not found' );
		return;
	}

	if ( mode === 'update' && row ) {
		action.value = 'update';
		roleId.value = row.dataset.roleId || '';
		roleName.value = row.dataset.roleName || '';
		roleLevel.value = row.dataset.roleLevel || '';
		isEnabled.checked = row.dataset.isEnabled === '1';
		saveButton.textContent = '更新';
		title.textContent = '権限編集';
		roleName.focus();
	} else {
		action.value = 'add';
		roleId.value = '';
		roleName.value = '';
		roleLevel.value = '';
		isEnabled.checked = true;
		saveButton.textContent = '登録';
		title.textContent = '権限登録';
		roleName.focus();
	}

	if ( typeof dialog.showModal === 'function' ) {
		dialog.showModal();
	} else {
		dialog.setAttribute( 'open', 'open' );
	}
}

document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '#new_role_button' );
	if ( button ) {
		console.log( 'role-list: new' );
		openRoleDialog( 'add', null );
		return;
	}

	button = event.target.closest( '#role-list .edit-button' );
	if ( !button ) {
		return;
	}

	console.log( 'role-list: edit' );

	var row = button.closest( 'tr' );
	if ( row ) {
		openRoleDialog( 'update', row );
	}
} );

document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '#role-list #cancel_button' );
	if ( !button ) {
		return;
	}

	var dialog = document.getElementById( 'roleDialog' );
	if ( dialog ) {
		if ( typeof dialog.close === 'function' ) {
			dialog.close();
		} else {
			dialog.removeAttribute( 'open' );
		}
	}
} );

document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '.close-dialog' );
	if ( !button ) {
		return;
	}

	var dialog = button.closest( 'dialog' );
	if ( dialog ) {
		if ( typeof dialog.close === 'function' ) {
			dialog.close();
		} else {
			dialog.removeAttribute( 'open' );
		}
	}
} );

function lockRoleForm( form, event ) {
	if ( !form ) {
		return false;
	}

	if ( form.dataset.locked === '1' ) {
		if ( event ) {
			event.preventDefault();
		}
		return false;
	}

	form.dataset.locked = '1';
	form.classList.add( 'is-submitting' );

	var saveButton = form.querySelector( '#save_button' );
	if ( saveButton ) {
		saveButton.disabled = true;
		saveButton.textContent = '処理中...';
	}

	var dialog = document.getElementById( 'roleDialog' );
	if ( dialog ) {
		dialog.classList.add( 'is-processing' );
	}

	return true;
}

document.addEventListener( 'submit', function( event ) {
	var form = event.target.closest( '#roleForm' );
	if ( !form ) {
		return;
	}

	if ( !lockRoleForm( form, event ) ) {
		event.preventDefault();
		return;
	}

	var dialog = document.getElementById( 'roleDialog' );
	if ( dialog ) {
		if ( typeof dialog.close === 'function' ) {
			dialog.close();
		} else {
			dialog.removeAttribute( 'open' );
		}
	}
} );

document.addEventListener( 'click', function( event ) {
	var dialog = document.getElementById( 'roleDialog' );
	if ( !dialog || event.target !== dialog ) {
		return;
	}
	if ( typeof dialog.close === 'function' ) {
		dialog.close();
	} else {
		dialog.removeAttribute( 'open' );
	}
} );

// メニュー管理画面
function resetMenuFormState() {
	var menuList = document.getElementById( 'menu-list' );
	if ( !menuList ) {
		return;
	}

	var form = menuList.querySelector( '#menuForm' );
	if ( !form ) {
		return;
	}

	form.dataset.locked = '0';
	form.classList.remove( 'is-submitting' );

	var saveButton = form.querySelector( '#menu_save_button' );
	if ( saveButton ) {
		saveButton.disabled = false;
		saveButton.textContent = '登録';
	}

	form.querySelectorAll( 'input, button, select' ).forEach( function( element ) {
		element.disabled = false;
	} );

	var dialog = document.getElementById( 'menuDialog' );
	if ( dialog ) {
		dialog.classList.remove( 'is-processing' );
	}
}

function openMenuDialog( mode, row ) {
	var menuList = document.getElementById( 'menu-list' );
	var dialog = document.getElementById( 'menuDialog' );
	if ( !menuList || !dialog ) {
		return;
	}

	resetMenuFormState();

	var action = menuList.querySelector( '#menu_action' );
	var targetMenuId = menuList.querySelector( '#target_menu_id' );
	var menuId = menuList.querySelector( '#menu_id' );
	var parentId = menuList.querySelector( '#menu_parent_id' );
	var displayOrder = menuList.querySelector( '#menu_display_order' );
	var roleMin = menuList.querySelector( '#menu_role_min' );
	var roleMax = menuList.querySelector( '#menu_role_max' );
	var menuName = menuList.querySelector( '#menu_name' );
	var menuUrl = menuList.querySelector( '#menu_url' );
	var isEnabled = menuList.querySelector( '#menu_is_enabled' );
	var saveButton = menuList.querySelector( '#menu_save_button' );
	var title = menuList.querySelector( '#menuDialogTitle' );

	if ( !action || !targetMenuId || !menuId || !parentId || !displayOrder || !roleMin || !roleMax || !menuName || !menuUrl || !isEnabled || !saveButton || !title ) {
		console.error( 'menu-list: form elements not found' );
		return;
	}

	if ( mode === 'update' && row ) {
		action.value = 'update';
		// target_menu_idは更新対象、menu_idは変更後IDとして送る。
		targetMenuId.value = row.dataset.menuId || '';
		menuId.value = row.dataset.menuId || '';
		menuId.readOnly = false;
		parentId.value = row.dataset.parentId || '0';
		displayOrder.value = row.dataset.displayOrder || '0';
		roleMin.value = row.dataset.roleMin || '0';
		roleMax.value = row.dataset.roleMax || '0';
		menuName.value = row.dataset.menuName || '';
		menuUrl.value = row.dataset.menuUrl || '';
		isEnabled.checked = row.dataset.isEnabled === '1';
		saveButton.textContent = '更新';
		title.textContent = 'メニュー編集';
		menuName.focus();
	} else {
		action.value = 'add';
		// 新規時はtarget_menu_idを空にして重複チェック対象を明確化する。
		targetMenuId.value = '';
		menuId.value = '';
		menuId.readOnly = false;
		parentId.value = '0';
		displayOrder.value = '0';
		roleMin.value = '0';
		roleMax.value = '9999';
		menuName.value = '';
		menuUrl.value = '';
		isEnabled.checked = true;
		saveButton.textContent = '登録';
		title.textContent = 'メニュー登録';
		menuId.focus();
	}

	if ( typeof dialog.showModal === 'function' ) {
		dialog.showModal();
	} else {
		dialog.setAttribute( 'open', 'open' );
	}
}

document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '#new_menu_button' );
	if ( button ) {
		openMenuDialog( 'add', null );
		return;
	}

	button = event.target.closest( '#menu-list .edit-button' );
	if ( !button ) {
		return;
	}

	var row = button.closest( 'tr' );
	if ( row ) {
		openMenuDialog( 'update', row );
	}
} );

document.addEventListener( 'click', function( event ) {
	var button = event.target.closest( '#menu-list #menu_cancel_button' );
	if ( !button ) {
		return;
	}

	var dialog = document.getElementById( 'menuDialog' );
	if ( dialog ) {
		if ( typeof dialog.close === 'function' ) {
			dialog.close();
		} else {
			dialog.removeAttribute( 'open' );
		}
	}
} );

function lockMenuForm( form, event ) {
	if ( !form ) {
		return false;
	}

	if ( form.dataset.locked === '1' ) {
		if ( event ) {
			event.preventDefault();
		}
		return false;
	}

	form.dataset.locked = '1';
	form.classList.add( 'is-submitting' );

	var saveButton = form.querySelector( '#menu_save_button' );
	if ( saveButton ) {
		saveButton.disabled = true;
		saveButton.textContent = '処理中...';
	}

	var dialog = document.getElementById( 'menuDialog' );
	if ( dialog ) {
		dialog.classList.add( 'is-processing' );
	}

	return true;
}

document.addEventListener( 'submit', function( event ) {
	var form = event.target.closest( '#menuForm' );
	if ( !form ) {
		return;
	}

	if ( !lockMenuForm( form, event ) ) {
		event.preventDefault();
		return;
	}

	var dialog = document.getElementById( 'menuDialog' );
	if ( dialog ) {
		if ( typeof dialog.close === 'function' ) {
			dialog.close();
		} else {
			dialog.removeAttribute( 'open' );
		}
	}
} );

document.addEventListener( 'click', function( event ) {
	var dialog = document.getElementById( 'menuDialog' );
	if ( !dialog || event.target !== dialog ) {
		return;
	}
	if ( typeof dialog.close === 'function' ) {
		dialog.close();
	} else {
		dialog.removeAttribute( 'open' );
	}
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
