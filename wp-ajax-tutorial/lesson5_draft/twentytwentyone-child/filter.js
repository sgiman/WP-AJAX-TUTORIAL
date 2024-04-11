/**
 * FILTER (AJAX)
 */

jQuery (function ($) {
    //alert("test")
    //alert(true_obj.ajaxurl)

    // --- ajax request ---
    $('#filter').submit ( function () {
        var filter = $(this);               // объект filter
        $.ajax({
            url : true_obj.ajaxurl,         // обработчик
            data : filter.serialize(),      // данные
            type : 'POST',                  // тип запроса
            // изменяем текст кнпоки
            beforeSend : function ( xhr ) {
                filter.find('button').text( 'Загружаю...' );
            },
            // возвращаем текст кнопки
            success : function ( data ) {
                filter.find('button').text( 'Фильтровать...' );
                $('#response').html(data);
            }
        });
        return false;   // приостанвить отпраку данных
    });

});
