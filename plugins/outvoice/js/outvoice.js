/**
 * @package OutVoice
 */

/*jshint esversion: 6 */

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function ( $ ) {

			let domain   = 'https://api.outvoice.com';
			let clientID = '08a3b847-025c-40a9-bfc8-4ed891286f99';

			ovInit();

			let nonce = outvoiceData.nonce;

			$( '.ov-login' ).click(
				function () {
					ovLogin();
				}
			);

			$( 'input[name="outvoice-user"], input[name="outvoice-pass"]' ).focus(
				function () {
					$( '#post' ).on(
						'keyup keypress',
						function (e) {
							let keyCode = e.keyCode || e.which;
							if (keyCode === 13) {
								e.preventDefault();
								return false;
							}
						}
					);
				}
			);

			function ovLogin()
			{
				$( '.outvoice-body-wrapper' ).addClass( 'loading' );
				let username = $( 'input[name="outvoice-user"]' ).val();
				let password = $( 'input[name="outvoice-pass"]' ).val();
				getToken( domain, clientID, username, password );
			}

			function ovInit()
			{
				$( '#ov-combobox' ).ovcombobox( {fullMatch: true, showDropDown: false, empty: true} );
				$( '.ovcombobox-display' ).attr( 'placeholder', '-- select contributor --' );
				$( '.outvoice-add-contributor' ).click(
					function () {
						$( '#outvoice-contrib' ).show();
						$( '.outvoice-add-contributor-1' ).show();
						$( this ).hide();
						$( '.outvoice-no-contributor' ).show();
						$( '#publish' ).attr( 'value', 'Publish and Pay' );
					}
				);

				$( '.outvoice-add-contributor-1' ).click(
					function () {
						$( '#ov-combobox-1' ).ovcombobox( {fullMatch: true, showDropDown: false, empty: true} );
						$( '.ovcombobox-display' ).attr( 'placeholder', '-- select contributor --' );
						$( '#outvoice-contrib-1' ).show();
						$( this ).hide();
					}
				);

				$( '.outvoice-no-contributor' ).click(
					function () {
						$( '#outvoice-contrib' ).hide();
						$( '#outvoice-contrib-1' ).hide();
						$( '.outvoice-add-contributor' ).show();
						$( '.outvoice-add-contributor-1' ).hide();
						$( '#publish' ).attr( 'value', 'Publish' );
						$( '#publish' ).off();
						$( this ).hide();
						ovClearFields();
					}
				);
				$( '.ov-amount' ).keydown(
					function (e) {
						// Allow: backspace, delete, tab, escape, enter and period.
						if ($.inArray( e.keyCode, [46, 8, 9, 27, 13, 110, 190] ) !== -1
							// Allow: Ctrl+A, Command+A.
							|| (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true))
							// Allow: home, end, left, right, down, up.
							|| (e.keyCode >= 35 && e.keyCode <= 40)
						) {
							// let it happen, don't do anything.
							return;
						}
						// Ensure that it is a number and stop the keypress.
						if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
							e.preventDefault();
						}
					}
				);
				$( '#publish' ).removeAttr( 'disabled' );
			}

			let bt = $( '#publish' ).attr( 'value' );
			if (bt === 'Publish') {
				$( '#publish' ).attr( 'value', 'Publish and Pay' );
				let status = outvoiceData.status;
				if (status == 1) {
					$( '#publish' ).click(
						function (e) {
							let frlncr = $( 'select[name="outvoice-freelancer"]' ).val();
							if (frlncr !== 'staff') {
								let amt = $( 'input[name="outvoice-amount"]' ).val();
								if (amt < 1) {
									window.alert( 'Please enter an amount larger than $1. To publish without using OutVoice, click the X above the publish and pay button.' );
									e.preventDefault();
								}
							}
						}
					);
				} else {
					$( '#publish' ).attr( 'disabled','disabled' );
					$( '#outvoice-bypass' ).click(
						function (e) {
							e.preventDefault();
							$( '#publish' ).removeAttr( 'disabled' );
							$( '#publish' ).attr( 'value', 'Publish' );
							$( '#outvoice-bypass-text' ).hide();
							$( 'input[name="outvoice-user"]' ).hide();
							$( 'input[name="outvoice-pass"]' ).hide();
							$( '.ov-login, label.ov-form' ).hide();
							$( '.ov-show' ).show();
							$( '.ov-show' ).click(
								function () {
									$( 'input[name="outvoice-user"]' ).show();
									$( 'input[name="outvoice-pass"]' ).show();
									$( '#outvoice-bypass-text' ).show();
									$( '.ov-login, label.ov-form' ).show();
									$( this ).hide();
									$( '#publish' ).attr( 'disabled','disabled' );
									$( '#publish' ).attr( 'value', 'Publish and Pay' );
								}
							);
						}
					);
				}
			}

			function ovClearFields()
			{
				$( '.ovcombobox-value' ).val( '' );
				$( '.ovcombobox-display' ).val( '' );
				$( '.ov-amount' ).val( '' );
			}

			function getToken(domain, clientID, username, password)
			{
				let tokenData   = {
					client_id: clientID,
					grant_type: 'password',
					username: username,
					password: password
				};
				let accessToken = 'Login failed';
				$.ajax(
					{
						url: domain + '/oauth/token',
						type: 'POST',
						data: $.param( tokenData )
					}
				).done(
					function (token) {
						accessToken = token.access_token;
						$( '.outvoice-options-body' ).empty();
						$( ovContent ).appendTo( $( '.outvoice-options-body' ) );
						contributorList( accessToken, domain );
						$( 'input[name="ov-user"]' ).val( username );
						$( 'input[name="ov-pass"]' ).val( password );
					}
				).fail(
					function () {
						$( '.outvoice-body-wrapper' ).removeClass( 'loading' );
						window.alert( 'Login Failed' );
					}
				);
				return accessToken;
			}

			function contributorList(token, domain)
			{
				let output = '';
				$.getJSON(
					{
						url: domain + '/api/v1.0/list-freelancers',
						beforeSend: function (xhr) {
							xhr.setRequestHeader( 'Authorization', 'Bearer ' + token );
						}
					}
				).done(
					function (data) {
						$.each(
							data,
							function (index, item) {
								$.each(
									item,
									function (key, value) {
										$( '#ov-combobox, #ov-combobox-1' ).append(
											$( '<option></option>' )
											.text( value )
											.val( key )
										);
									}
								);
							}
						);
						ovInit();
						$( '.outvoice-body-wrapper' ).removeClass( 'loading' );
					}
				).fail(
					function () {
						window.alert( 'There has been an error. Please log in to Outvoice again.' );
					}
				);
				return output;
			}

			const ovContent = '<div class="outvoice-body-wrapper"></div><div><a class="outvoice-add-contributor">add a contributor</a></div><div id="outvoice-contrib"><div class="outvoice-options-row freelancer"><input type="hidden" id="outvoice_post_nonce" name="outvoice_post_nonce" value="' +
			nonce + '"><select name="outvoice-freelancer" id="ov-combobox"></select></div><div class="outvoice-options-row payment"><select name="outvoice-currency"><option value="USD">USD</option></select><span class="outvoice-currency-symbol">$</span><input class="ov-amount" type="text" size="9" name="outvoice-amount"><input type="hidden" value="" name="ov-paid"></div></div><div><a class="outvoice-add-contributor-1">add contributor</a></div><div id="outvoice-contrib-1"><hr><div class="outvoice-options-row freelancer"><select name="outvoice-freelancer-1" id="ov-combobox-1"></select><div><div class="outvoice-options-row payment"><select name="outvoice-currency-1"><option value="USD">USD</option></select><span class="outvoice-currency-symbol">$</span><input class="ov-amount" type="text" size="9" name="outvoice-amount-1"><div><input type="hidden" value="" name="ov-user"><input type="hidden" value="" name="ov-pass"><div><div><a class="outvoice-no-contributor">X</a><div><div>';

		}
	);

})( jQuery );
