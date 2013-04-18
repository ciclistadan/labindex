
//let rethink this whole thing
// http://jsfiddle.net/ciclistadan/6AwHQ/3/



function bind_titlebar_functions(single_element) {
    var selector;
    //if an elmenet is used as an argument, only bind that, otherwise bind all .reagent_titlar classes
    if($(single_element).length > 0){
        selector = $(single_element);
    }
    else{
        selector = $(".reagent_titlebar");
    }
    //add a click function to the reagent titlebar that will toggle details
    var type;
    var id;
    $(selector).click(function(){
        //if placeholder is in the #side
        if($('#side').find('.placeholder').first().length == 1){
            $(this).parent().replaceWith($('#side').find('.placeholder').first()).appendTo($('#side'));
            type = $(this).parent().attr('type');
            id = $(this).parent().attr('id');
            create_fields(type,id);
        }
        //else placeholder in in the list and another reagent is in the #side
        else{
            $('#side').find('.reagent_details').remove();
            bind_titlebar_functions($('#side').find('.reagent_titlebar'));

            $('.placeholder').first().replaceWith($('#side').find('.reagent_div')).appendTo('#side');
            $(this).closest('.reagent_div').replaceWith($('#side').find('.placeholder')).appendTo($('#side'));
            type = $(this).parent().attr('type');
            id = $(this).parent().attr('id');
            create_fields(type,id);
        }
    });
}

function create_fields(type,id) {
    var data = {
        r_reagent_type: type
    };
// alert(type);
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_reagent_detail_fields.php",
        dataType: 'json',
        data: data,
        success: function(data){
            if(data.rows > 0){
                //create a div for my details
                $(document.createElement('div'))
                .addClass('reagent_details')
                .appendTo("#"+id);
                //go ahead and make an element for all fields, use css to format or hide as appropriate based on class or attr:field (e.g. we don't need to display r_rid)
                $.each(data.fields, function(key, val) {
                    $(document.createElement('div'))
                    .addClass('reagent_detail')
                    .addClass('small_field')
                    .addClass(val.field_attr_column_name)
                    .attr('field',val.field_attr_column_name)
                    .appendTo("#"+id +" > .reagent_details");

                    $(document.createElement('div'))
                    .addClass('detail_name')
                    .text(val.field_attr_full_name)
                    .appendTo("#"+id+" > .reagent_details > [field="+val.field_attr_column_name+"]");

                    $(document.createElement('div'))
                    .addClass('detail_value')
                    .appendTo("#"+id+" > .reagent_details > [field="+val.field_attr_column_name+"]");

                    $(document.createElement('textarea'))
                    .attr('type','text')
                    .val(get_detail_value(val.field_attr_column_name, id))
                    .appendTo("#"+id+" > .reagent_details > [field="+val.field_attr_column_name+"] > .detail_value");

                    bind_edit_functions("#"+id+" > .reagent_details > [field="+val.field_attr_column_name+"] > .detail_value > textarea");
                });

         }
         else{
             $(document.createElement('div')).text("there was a problem loading these details (  sql query returned no results)").appendTo("#"+id);
         }

         },
         error: function(){
           $(document.createElement('div')).text("there was a problem loading these details (json return error)").appendTo("#"+id);
         }
    });
}



function get_detail_value(field, id){
    var retval = 'pre';
    var data = {
        r_rid: id,
        r_field: field
    };
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/fetch_field_value.php",
        dataType: 'json',
        data: data,
        success: function(data){
            if(data.rows == 1){
                retval = data.return_value;
            }
            else{ retval =  'blank'; }
        },
        error: function(){ retval = 'error'; }
    });
    return retval;
}

function bind_edit_functions(element){
    $(element)
    .focus(function(){
        var previous = $(element).val();
        $(element).addClass('editing').addClass('ui-state-highlight').attr('previous',previous);
    })
    .blur(function(){
        if($(element).val() != $(element).attr('previous')){
            var new_value = $(element).val();
            var field = $(element).closest('.reagent_detail').attr('field');
            var id = $(element).closest('.reagent_div').attr('id');

            var updated = update_detail(new_value, field, id);
            if(updated){
                $(element).removeClass('editing').removeAttr('previous');
            }
            else{
                $(element).addClass('error').focus();
            }
        }
        else{
            $(element).removeClass('editing').removeAttr('previous');
        }
    });
}

function update_detail(new_value, field, id){
    var status;
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/update_reagent_detail.php",
        dataType: 'json',
        data: {
            field: field,
            id: id,
            new_value: new_value
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