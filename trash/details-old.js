/* 
 * these add click functionality to each .reagent_div titlebar that toggles display of detail information
 * adding text via sublimetext 2
 */
















function make_clickable_element(value){

    var element = $(document.createElement('div'))
        .addClass('detail_value')
        //TODO insert placeholder if blank
        .text(value)
        .bind('click',function(){
            make_blurable_edit_element($(this));

        });

    return element;
}

//this function changes the 
function make_blurable_edit_element(this_item) {

    //        get the current value for this field
    var current_value = $(this_item).text();

        // create the edit element
        var editbox = $(document.createElement('input'))
        .addClass('detail_value')
        .addClass('editable')
        .attr('type','text')
        .attr('value',current_value)
        .attr('previous',current_value)
        .blur(function(){
            if($(this).val() != $(this).attr("previous")){
                alert("changed, run update");
            }
            else{
                //replace this
                $(this).replaceWith(make_clickable_element($(this).attr("previous")));
                //TODO restore tabindex value
            }

        });

    $(this_item).replaceWith(editbox);
}



function set_tab_index(id){
    var index = 1;

    $("#"+id+" > .reagent_details > .reagent_detail > .detail_value").each(function(){
        $(this).attr("tabindex",index);
        index++;
    })
}


function replace_with_clickable_element(this_item) {
    
    if($(this_item).is('input')){
        var value = $(this_item).val();
        }
    else{
        var value = $(this_item).text();
        }
    
    var element = $(document.createElement('div'))
        .text(value)
        .click(function () {
            replace_with_editable_element($(this));
    });

    $(this_item).replaceWith(element);
}


function replace_with_editable_element(this_item) {
    var current = $(this_item).text();
   
    var element = $(document.createElement('input'))
        .attr("type", "text")
        .val(current)
        .blur(function () {
            replace_with_clickable_element($(this));
        });

    $(this_item).replaceWith(element);

    $('input:text')
        .focus()
        .select();
}

