
function create_aliquots(rid) {

    $('div .aliquot_titlebar').remove();
    $('div .aliquot_details').remove();

    $(document.createElement('div'))
    .addClass('aliquot_titlebar')
    .appendTo('[rid='+rid+']');


    $(document.createElement('span'))
    .addClass('aliquot_titlebar_value')
    .text('Aliquots')
    .addClass('h2')
    .appendTo(".aliquot_titlebar");

    $(document.createElement('a'))
    .text(' (+add)')
    .click(function(){
        var rid = $('#side .reagent_div').attr('rid');
        new_aliquot(rid);
    })
    .appendTo(".aliquot_titlebar");

    $(document.createElement('div'))
    .addClass('aliquot_details')
    .addClass('table')
    .attr('table','aliquots')
    .appendTo('[rid='+rid+']');

    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_aliquot_details.php",
        dataType: 'json',
        data: { aq_rid: rid}
    })
    .done(function(data){
        $.each(data.aliquots, function(key, val) {

            //create aliquot detail container
            $(document.createElement('div'))
            .addClass('aliquot_detail')
            .addClass('table_id')
            .attr('aqid',val.aq_aqid)
            .attr('table','aliquot')
            .appendTo('.aliquot_details');

            //create container details and button
            $(document.createElement('a'))
                .addClass('button')
                .addClass('container')
                .addClass('table_id')
                .attr('cid',val.c_cid)
                .addClass('aliquot_container')
                .text(val.c_cname)
                .appendTo('.aliquot_detail[aqid='+val.aq_aqid+']');

                $(document.createElement('span'))
                .addClass('container_temp')
                .text("  ("+val.c_temp+")")
                .appendTo('[aqid='+val.aq_aqid+'] a');

                // TODO: add tooltip to show container location

            //create aliquot details
                $(document.createElement('input'))
                .attr('type','text')
                .addClass('aliquot_amount')
                .addClass('width_adjust')
                .addClass('editable')
                .attr('field','aq_amount')
                .attr('default_value','(qty)')
                .val(val.aq_amount)
                .appendTo('.aliquot_detail[aqid='+val.aq_aqid+']');

                $(document.createElement('input'))
                .attr('type','text')
                .addClass('aliquot_conc')
                .addClass('width_adjust')
                .addClass('editable')
                .attr('field','aq_conc')
                .attr('default_value','(conc)')
                .val(val.aq_conc)
                .appendTo('.aliquot_detail[aqid='+val.aq_aqid+']');

                $(document.createElement('input'))
                .attr('type','text')
                .addClass('aliquot_lot')
                .addClass('width_adjust')
                .addClass('editable')
                .attr('field','aq_lot')
                .attr('default_value','(lot/date)')
                .val(val.aq_lot)
                .appendTo('.aliquot_detail[aqid='+val.aq_aqid+']');

                $(document.createElement('select'))
                    .addClass('action_options')
                    .appendTo('.aliquot_detail[aqid='+val.aq_aqid+']');

                    $(document.createElement('option'))
                    .text('options')
                    .appendTo('.aliquot_detail[aqid='+val.aq_aqid+'] select');

                    $(document.createElement('option'))
                    .val('move')
                    .text('move')
                    .appendTo('.aliquot_detail[aqid='+val.aq_aqid+'] select');

                    $(document.createElement('option'))
                    .val('delete')
                    .text('delete')
                    .appendTo('.aliquot_detail[aqid='+val.aq_aqid+'] select');

                    $(document.createElement('option'))
                    .val('duplicate')
                    .text('duplicate')
                    .appendTo('.aliquot_detail[aqid='+val.aq_aqid+'] select');
        });

        //now that you have all the aliquot information, insert default values and bind edit functions
            $('.editable').each(function(){
                bind_edit_functions($(this));

                if($(this).val().length === 0){
                    $(this).val(  $(this).attr("default_value") );
                }
            });


            $('.width_adjust').each(function(){
                width_adjust(this);
            })
            .keypress(function(){
                width_adjust(this);
            });

            bind_aliquot_action();
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
    input_width = (input_width+1 )*6;
    $(this_item).css('width',input_width);

    //TODO: add mechanism to balance column widths for all items at once
}

//bind functions to aliquot select pulldown-manu
function bind_aliquot_action(){
$('.action_options').change(function(){
    var rid    = $('#side .reagent_div').attr('rid');
    var action = $(this).val();
    var aqid   = $(this).closest('.aliquot_detail').attr('aqid');

    if(action === "delete"){delete_aliquot(rid, aqid)}
})
}

function new_aliquot(rid){
    var status;
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/insert_new_aliquot.php",
        dataType: 'json',
        data: {
            aq_rid:rid
        }
    })
    .done(function( data ) {
        if(data.rows == 1){
           status = 1;
       }
       else{ status = 0; }
   })
    .fail(function(){status = 0;});

    //refresh the aliqout info on this page
    create_aliquots(rid);
}

function delete_aliquot(rid, aqid){
    var status;
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/delete_aliquot.php",
        dataType: 'json',
        data: {
            aq_aqid:aqid
        }
    })
    .done(function( data ) {
        if(data.rows == 1){
           status = 1;
       }
       else{ status = 0; }
   })
    .fail(function(){status = 0;});

    //refresh the aliqout info on this page
    create_aliquots(rid);
}
