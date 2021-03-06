function bind_titlebar_functions(single_element) {
    var selector;
    //if an elemenet is used as an argument, only bind that, otherwise bind all .reagent_titlebar classes
    if($(single_element).length > 0){
        selector = $(single_element);
    }
    else{
        selector = $(".reagent_titlebar");
    }
    
    //add a click function to the reagent titlebar that will toggle details
    $(selector).click(function(){
        swap_reagents($(this));
    });
}


function swap_reagents(element){
    var type;
    var rid;
    var this_element = element.length > 0 ? element : $(this);

    // prevent detail requests without authentication
    // this is not a 'real' security checkpoint, just prevents error messages when the server checks your credentials
    if($('.verified').length == 1){

        //if placeholder is in the #side container
        if($('#side').find('.placeholder').first().length == 1){

            $(this_element).parent().replaceWith($('#side').find('.placeholder').first()).appendTo($('#side'));

        }
        //else placeholder in in the list and another reagent is in the #side
        else{
            $('#side').find('.reagent_titlebar').siblings().remove();
            bind_titlebar_functions($('#side').find('.reagent_titlebar'));

            $('.placeholder').first().replaceWith($('#side').find('.reagent_div')).appendTo('#side');
            $(this_element).closest('.reagent_div').replaceWith($('#side').find('.placeholder')).appendTo($('#side'));
        }
        type = $(this_element).closest('.table_id').attr('type');
        rid = $(this_element).closest('.table_id').attr('identifier');

        create_fields(type,rid);
        populate_details(rid);
        create_aliquots(rid);

        //resize the textareas to fit actual text
        $('#side textarea').keyup(function (){
            $(this).height( 0 );
            $(this).height( this.scrollHeight );
        });
        $('#side textarea').keyup();
    }
    else{
        alert("You are not properly authenticated to view reagent details");
    }
}


function create_fields(type,id) {
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_reagent_detail_fields.php",
        dataType: 'json',
        data: { r_reagent_type: type}
    })
    .done(function(data){

        if(data.rows > 0){
            //create a two .reagent_details divs for two columns of fields
            $(document.createElement('div'))
            .addClass('reagent_details1')
            .attr('table','details')
            .appendTo("#"+id);

            $(document.createElement('div'))
            .addClass('reagent_details2')
            .attr('table','details')
            .appendTo("#"+id);

            //make an element for all fields
            $.each(data.fields, function(key, val) {

                $(document.createElement('div'))
                .addClass('reagent_detail')
                .addClass(val.field_class)
                .addClass(val.field_attr_column_name)
                .attr('field',val.field_attr_column_name)
                .appendTo("#"+id +" .reagent_details"+val.field_column);

                $(document.createElement('div'))
                .addClass('detail_name')
                .text(val.field_attr_full_name)
                .appendTo("#"+id+" [field="+val.field_attr_column_name+"]");

                $(document.createElement('div'))
                .addClass('detail_value')
                .appendTo("#"+id+" [field="+val.field_attr_column_name+"]");

                $(document.createElement('textarea'))
                .attr('type','text')
                .appendTo("#"+id+" [field="+val.field_attr_column_name+"] .detail_value");

                bind_edit_functions("#"+id+" [field="+val.field_attr_column_name+"] .detail_value > textarea");
            });

        }
        else{
            $(document.createElement('div')).text("no results returned").appendTo("#"+id);
        }
    })
    .fail(function(){
        $(document.createElement('div')).text("there was a problem loading these details (json return error)").appendTo("#"+id);
            });
}


function populate_details(id){
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_field_values.php",
        dataType: 'json',
        data: { r_rid: id },
        success: function(data){
            if(data.rows == 1){
                $.each(data.details, function(key, val) {
                    $('.reagent_detail[field='+key+']').find('.detail_value textarea').val(val);
                });
            }
            else{
                // TODO error handling
            }
        },
        error: function(){ 
            // TODO error handling
        }
    });
}

// TODO: in the middle of editing this function,
// trying to abstract it to work as an
// update function for reagent details, aliquots, and containers...
function bind_edit_functions(element){
    $(element)
    .focus(function(){
        //remove default value on focus
        if($(this).val() === $(this).attr('default_value')){
            $(this).val('');
        }
        //remember non-default values in 'previous' attribute
        var previous = $(element).val();
        $(element).addClass('editing').attr('previous',previous);

    })
    .blur(function(){
        //do nothing if unchanged
        if($(element).val() === $(element).attr('previous')){

            $(element).val( $(element).attr('previous') );
            $(element).removeClass('editing')
                .removeAttr('previous')
                .each(function(){
                    if($(element).hasClass('width_adjust')){ width_adjust(this); }

                    });
        }

        //update if changed
        else if($(element).val() != $(element).attr('previous')){

            var new_value = $(element).val();
            var table = $(element).closest('.table_id').attr('table');
            var col = $(element).closest('[field]').attr('field');
            var row = $(element).closest('[identifier]').attr('identifier');

            var updated = update_detail(new_value, col, row, table);

            if(updated){
                $(element).removeClass('editing').removeAttr('previous').removeClass('error');
            }
            else{
                $(element).removeClass('editing').addClass('error').focus();
            }
        }

        // replace with default if blank
        if($(element).val().length == 0){
            $(element).val( $(element).attr('default_value'));
            if($(element).hasClass('width_adjust')){ width_adjust(this); }
        }


    });
}
// type refers to reagent 'detail' or 'aliquot'
function update_detail(new_value, col, row, table){
    var status;
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/update_reagent_detail.php",
        dataType: 'json',
        data: {
            col: col,
            row: row,
            new_value: new_value,
            table: table
        }
    })
    .done(function( data ) {
        if(data.rows == 1){
           status = 1;
       }
       else{ status = 0; }
   })
    .fail(function(){status = 0;});
    return status;
}