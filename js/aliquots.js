
function create_aliquots(rid) {

    $('div .aliquot_titlebar').remove();
    $('div .aliquot_details').remove();

//create a titlebar for the aliquots section
    $(document.createElement('div'))
    .addClass('aliquot_titlebar')
    .appendTo('[identifier='+rid+']');

    $(document.createElement('span'))
    .addClass('aliquot_titlebar_value')
    .text('Aliquots')
    .addClass('h2')
    .appendTo(".aliquot_titlebar");

    $(document.createElement('a'))
    .text(' (+add)')
    .click(function(){
        var rid = $('#side .reagent_div').attr('identifier');
        new_aliquot(rid);
    })
    .appendTo(".aliquot_titlebar");

//create a container for all the aliquots
    $(document.createElement('div'))
    .addClass('aliquot_details')
    // .addClass('table')
    .attr('table','aliquots')
    .appendTo('[identifier='+rid+']');

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
            .attr('identifier',val.aq_aqid)
            .attr('table','aliquot')
            .appendTo('.aliquot_details');

            //create container details

            $(document.createElement('select'))
            .addClass('container_selector')
            .appendTo('.aliquot_detail[identifier='+val.aq_aqid+']');

            $(document.createElement('option'))
            .attr('cid',val.c_cid)
            .text(val.c_cname+" ("+val.c_temp+")")
            .appendTo('.aliquot_detail[identifier='+val.aq_aqid+'] .container_selector');

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
                .appendTo('.aliquot_detail[identifier='+val.aq_aqid+']');

                $(document.createElement('input'))
                .attr('type','text')
                .addClass('aliquot_conc')
                .addClass('width_adjust')
                .addClass('editable')
                .attr('field','aq_conc')
                .attr('default_value','(conc)')
                .val(val.aq_conc)
                .appendTo('.aliquot_detail[identifier='+val.aq_aqid+']');

                $(document.createElement('input'))
                .attr('type','text')
                .addClass('aliquot_lot')
                .addClass('width_adjust')
                .addClass('editable')
                .attr('field','aq_lot')
                .attr('default_value','(lot/date)')
                .val(val.aq_lot)
                .appendTo('.aliquot_detail[identifier='+val.aq_aqid+']');

                $(document.createElement('select'))
                .addClass('action_options')
                .appendTo('.aliquot_detail[identifier='+val.aq_aqid+']');

                    $(document.createElement('option'))
                    .text('options')
                    .appendTo('.aliquot_detail[identifier='+val.aq_aqid+'] .action_options');

                    // $(document.createElement('option'))
                    // .val('duplicate')
                    // .text('duplicate')
                    // .appendTo('.aliquot_detail[identifier='+val.aq_aqid+'] .action_options');

                    $(document.createElement('option'))
                    .val('delete')
                    .text('delete')
                    .appendTo('.aliquot_detail[identifier='+val.aq_aqid+'] .action_options');

                    // $(document.createElement('option'))
                    // .val('archive')
                    // .text('archive')
                    // .appendTo('.aliquot_detail[identifier='+val.aq_aqid+'] .action_options');
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
            populate_container_selectors();
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

//bind functions to aliquot select pulldown-menu, this allows deletion (and duplication in the future)
function bind_aliquot_action(){
$('.action_options').change(function(){
    var rid    = $('#side .reagent_div').attr('identifier');
    var action = $(this).val();
    var aqid   = $(this).closest('.table_id').attr('identifier');

    if(action      === "delete"){delete_aliquot(rid, aqid)}
    else if(action === "duplicate"){}
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

function populate_container_selectors(){

    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_containers.php",
        dataType: 'json'
    })
    .done(function(data){

        if(data.rows > 0){

            $('.aliquot_detail').each(function(){
                //make an element for all fields

                var current = $(this).find('.container_selector');
                 $.each(data.containers, function(key, val) {

                     //create an option entry for all available containers
                     $(document.createElement('option'))
                    .val(val.c_cid)
                    .text(val.c_cname+" ("+val.c_temp+")")
                    .appendTo(current);
                })
            });
        }
        else{
            //TODO add error notice here
        }
    })
    .fail(function(){
        //TODO add error notice here
        });

    bind_container_selectors();
}


//bind functions to container pulldown-manu
function bind_container_selectors(){
$('.container_selector').change(function(){
    var rid  = $('#side .reagent_div').attr('identifier');
    var cid  = $(this).val();
    var aqid = $(this).closest('.aliquot_detail').attr('identifier');

    update_detail(cid, 'aq_cid', aqid, 'aliquot');

})
}
