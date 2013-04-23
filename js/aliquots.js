
function create_aliquots(id) {

    $(document.createElement('div'))
    .addClass('aliquot_titlebar')
    .appendTo("#"+id);

    $(document.createElement('span'))
    .addClass('aliquot_titlebar_value')
    .text('Aliquots')
    .addClass('h2')
    .appendTo(".aliquot_titlebar");

    $(document.createElement('span'))
    .text('+')
    .addClass('aliquot_addition')
    .appendTo(".aliquot_titlebar");

    $(document.createElement('div'))
    .addClass('aliquot_details')
    .addClass('table')
    .attr('table','aliquots')
    .appendTo("#"+id);

    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_aliquot_details.php",
        dataType: 'json',
        data: { aq_rid: id}
    })
    .done(function(data){
        $.each(data.aliquots, function(key, val) {

            $(document.createElement('div'))
            .addClass('aliquot_detail')
            .addClass('table_id')
            .attr('rid',val.aq_aqid)
            .attr('table','aliquot')
            .appendTo('.aliquot_details');

        bind_edit_functions(
            $(document.createElement('input'))
            .attr('type','text')
            .addClass('aliquot_amount')
            .addClass('width_adjust')
            .attr('field','aq_amount')
            .val(val.aq_amount)
            .appendTo('.aliquot_detail[rid='+val.aq_aqid+']')
            );

        bind_edit_functions(
            $(document.createElement('input'))
            .attr('type','text')
            .addClass('aliquot_conc')
            .addClass('width_adjust')
            .attr('field','aq_conc')
            .val(val.aq_conc)
            .appendTo('.aliquot_detail[rid='+val.aq_aqid+']')
            );

        bind_edit_functions(
            $(document.createElement('input'))
            .attr('type','text')
            .addClass('aliquot_lot')
            .addClass('width_adjust')
            .attr('field','aq_lot')
            .val(val.aq_lot)
            .appendTo('.aliquot_detail[rid='+val.aq_aqid+']')
            );

            $(document.createElement('span'))
            .addClass('container_temp')
            .text(val.c_temp)
            .appendTo('.aliquot_detail[rid='+val.aq_aqid+']');

            $(document.createElement('span'))
            .addClass('aliquot_container')
            .text(val.c_cname)
            .appendTo('.aliquot_detail[rid='+val.aq_aqid+']');
            // TODO: add tooltip to show container location

            if($('.aliquot_amount').val().length === 0 ){
                $('.aliquot_amount').val('(amount)').addClass('default_value');
            }
            if($('.aliquot_conc').val().length === 0 ){
                $('.aliquot_conc').val('(concentration)').addClass('default_value');
            }
            if($('.aliquot_lot').val().length === 0 ){
                $('.aliquot_lot').val('(lot/date)').addClass('default_value');
            }

            $('.width_adjust').each(function(){
                width_adjust(this);
            })
            .keypress(function(){
                width_adjust(this);
            });


        });
    })
    .fail(function(){
        $(document.createElement('div'))
        .text("there was a problem loading aliquot details")
        .appendTo(".aliquot_details");
    });
}

function width_adjust(this_item){
    var input_width = $(this_item).val().length;
    // alert(input_width);
    input_width = (input_width+1 )*8;
    $(this_item).css('width',input_width);

    //TODO: add mechanism to balance column widths for all items at once
}


