/**************************
 * sgiman ajax
 * version 1.0
 **************************/
jQuery( function( $ ) {

	//====================================
	// AJAX (узнать заголовок по ID)
	//====================================
	$( '#true_get_id' ).submit( function() {

		var form = $(this);

		$.ajax({
			type : 'POST',
			url : sgiman.ajax_url,
			data : form.serialize(),
			beforeSend : function( xhr ) {
				form.find('button').text( 'Отправляем запрос...' );
			},
			success : function( data ){

				$('#result').html(data);
				form.find('button').text( 'Узнать заголовок' );
			}
		});

		return false;

	});

	//====================================
	// AJAX - "LOAD MORE"
	//====================================
	var button = $( '#loadmore a' ),
			paged = button.data( 'paged' ),
			maxPages = button.data( 'max_pages' );

	//----------------------------------
	// AJAX TEMPLATE FOR SCROLL TOP
	//----------------------------------
	// $( window ).scroll( function() {
	//
	// 	if( $(document).scrollTop() > ( $( document ).height() - 1000 ) {
	//
	// 		// выполняем ajax запрос
	//
	// 	}
	//
	// } );
/****************************************************************************************************
	// бесконечный скрол - ajax
	$( window ).scroll( function() {

		if( $(document).scrollTop() > ( $( document ).height() - 5000 ) ) {

			// выполняем ajax запрос
			 if( ! $( 'body' ).hasClass( 'loading' ) ) // проверка блокировочного текста
			 {
				$.ajax({
					type : 'POST',
					url : sgiman.ajax_url,
					data : {
						paged : paged,
						action : 'loadmore',
						taxonomy : button.data( 'taxonomy' ),
						term_id : button.data( 'term_id' ),
						pagenumlink : button.data( 'pagenumlink' )
					},
					dataType: 'json',
					beforeSend : function( xhr ) {
						button.text( 'Загружаем...' );
						$( 'body' ).addClass( 'loading' );		// блокировать загрузку текстом
					},
					success : function( data ){
						//console.log(data);
						paged++;
						button.parent().before( data.posts );
						$( '#pagination' ).html( data.pagination );
						button.text( 'Загрузить ещё' );

						if( paged == maxPages ) {
							button.remove();
						}

						$( 'body' ).removeClass( 'loading' );	// удалить блокировочный текст
					}
				}); //--- $.ajax ---
			} //--- if ---
 *********************************************************************************************/

	//====================================
	// Кнопка c ajax-пагинацией
	//====================================
	button.click( function( event ) {

		event.preventDefault();

		if( ! $( 'body' ).hasClass( 'loading' ) )
		{
			$.ajax({
				type : 'POST',
				url : sgiman.ajax_url,
				data : {
					paged : paged,
					action : 'loadmore',
					taxonomy : button.data( 'taxonomy' ),
					term_id : button.data( 'term_id' ),
					pagenumlink : button.data( 'pagenumlink' )
				},
				dataType: 'json',

				beforeSend : function( xhr ) {
					button.text( 'Загружаем...' );
					$( 'body' ).addClass( 'loading' );
				},

				success : function( data ){
					//console.log(data);
					paged++;
					button.parent().before( data.posts );
					$( '#pagination' ).html( data.pagination );
					button.text( 'Загрузить ещё' );

					if( paged == maxPages ) {
						button.remove();
					}

					$( 'body' ).removeClass( 'loading' );

				}

			}); // --- $.ajax ---

		} //--- if ---

	} ); //--- button.click ---


	//====================================
	// AJAX - COMMENTS
	//====================================
	// действие при отправки формы комментария
	$( '#commentform' ).submit( function() {

		var commentForm = $(this),
				respond = $( '#respond' ), 			// ответные комменты
				commentList = $( '.comment-list' ); // .comment-list иногда имеет другой класс

		// отправляем запрос
		$.ajax({
			type : 'POST',
			url : sgiman.ajax_url,									// лакализация ajax url
			data : commentForm.serialize() + '&action=sendcomment',	// данные формы

			beforeSend : function( xhr ) {
				// изменяем текст кнопки перед отправкой комментария
				$( '#submit' ).val( 'Отправляем' );
			},

			error: function (request, status, error) {
				// обрабатываем ошибки
				if( status == 500 ){
					alert( 'Ошибка при добавлении комментария' );
				} else if( status=='timeout' ){
					alert( 'Ошибка: Сервер не отвечает, попробуй ещё.' );
				} else {
					// ворпдрессовские ошибочки, не уверен, что это самый оптимальный вариант
					// если знаете способ получше - поделитесь
					var errormsg = request.responseText;
					var string1 = errormsg.split("<p>");
					var string2 = string1[1].split("</p>");
					alert(string2[0]);
				}
			},

			success : function( newComment ) {
				//alert ("test");
				//console.log( newComment );

				if( $( '.comment-list li' ).length > 0 ) { // если есть комментарии

					if( respond.parent().hasClass( 'comment' ) ) { // если дочерний комментарий

						if( respond.parent().children( '.children' ).length > 0 ) { // если дочерние уже есть
							respond.parent().children( '.children' ).append( newComment );
						} else { // если первый дочерний
							respond.after( '<ol class="children">' + newComment + '</ol>' );
						}

					} else { // если обычный комментарий
						commentList.append( newComment );
					}

				} else { // если нет комментариев
					respond.before( '<ol class="comment-list">' + newComment + '</ol>' );
				}

				$( '#cancel-comment-reply-link' ).trigger( "click" );
				$( '#submit' ).val( 'Отправить комментарий' );
				$( '#comment' ).val('');
			}

		} ); // --- $.ajax ---

		return false;

	}); //--- $( '#commentform' ) ---


	//====================================
	// AJAX - SEARCH
	//====================================
	$( 'input[ name="s" ]' ).autocomplete({
		source: function( request, response )
		{
			$.ajax( {
				url : sgiman.ajax_url,			// URL AJAX
				data : {
					action : 'mywebsitesearch',
					term : request.term
				},
				success : function( data ) {	// полученные данные
					console.log( data );
					response( data );
				}
			} );
		},
		select: function( event, ui ) {
			//console.log( ui );
			window.location = ui.item.url;
		}
	});


} ); //--- jQuery ---


//----------------------------------------------------
// БЕЗ AJAX - УЗНАТЬ ЗАГОЛОВОК ПО ID
//----------------------------------------------------
// let form = document.getElementById( 'true_get_id' );
// let result = document.getElementById( 'result' );
//
//
// form.addEventListener( 'submit', ( event ) => {
//
// 	event.preventDefault();
//
// 	form.querySelector('button').textContent = 'Отправляем запрос...';
//
// 	var request = new XMLHttpRequest();
//
// 	request.open('POST', sgiman.ajax_url, true);
//
// 	request.send( new FormData( form ) );
//
// 	request.onload = function() {
//
// 		var resp = this.response;
// 		result.innerHTML = resp;
// 		form.querySelector('button').textContent = 'Узнать заголовок';
//
// 	}
// 	request.send();
//
// } );
