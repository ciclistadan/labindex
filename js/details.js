/* 
 * these add click functionality to each .reagent_div titlebar that toggles display of detail information
 * 
 */



function bind_detail_functions() {
    //add a click function to the reagent titlebar that will toggle details
    $(".reagent_titlebar").click(function(){
        
        //      remove details if they're already showing
        if($(this).siblings('.reagent_details').length){
            $(this).siblings().remove();
        }
        
        //      else create a container for the details
        else{
            var type = $(this).parent().attr('type');
            var id = $(this).parent().attr('id');
            create_fields(type,id);
            refresh_all_field_values(id);
        }
    })
}


function create_fields(type,id) {
    
    var data = {
        r_reagent_type: type
    };


    //entire ajax function need to be adjusted for this script, it was taken from refresh_search.js
    $.ajax({
        type: "POST",
        url: "utility/fetch_reagent_detail_fields.php",
        dataType: 'json',
        data: data,
        success: function(data){		
         
            if(data.rows > 0){
               
                $.each(data.fields, function(key, val) {
                    
                    
                    //create entry container div for each reagent
                    $(document.createElement('div'))
                    .addClass(val.field_attr_column_name)
                    .attr('field',val.field_attr_column_name)
                    .addClass('reagent_details')
                    .text(val.field_attr_full_name)
                    .appendTo("#"+id);
                    
                  
                });

            }
            else{
                $(document.createElement('div')).text("there was a problem loading these details (sql query returned no results)").appendTo("#"+id);
            }

        },
        error: function(){
           
            $(document.createElement('div')).text("there was a problem loading these details (json return error)").appendTo("#"+id);
       
    
        }
    });
        
        
}
        
        
function refresh_all_field_values(id) {

  
    var data = {
        r_rid: id 
    };


    //entire ajax function need to be adjusted for this script, it was taken from refresh_search.js
    $.ajax({
        type: "POST",
        url: "utility/fetch_all_field_values.php",
        dataType: 'json',
        data: data,
        success: function(data){		
//            alert(data.rows);
            if(data.rows == 1){
               
                //select all the fields and get the values for them
                $("#"+id+' .reagent_details').each(function(){
                    $(document.createElement('div'))
                    .addClass('reagent_value')
                    .text(data.values[$(this).attr('field')])
                    .appendTo($(this));
                });
                
             bind_edit_functions();
                  
            }
            //more or +/- 1 row was found
            else{
                $(document.createElement('div')).text("there was a problem loading these details (sql query returned no results)").appendTo("#"+id);
            }

        },
        error: function(){
           
            $(document.createElement('div')).text("there was a problem loading these details (json return error)").appendTo("#"+id);
       
    
        }
    });    
        
}     
        
    function bind_edit_functions() {
    
    //add a click function to each editable value
    $(".reagent_value").focus(function(){
        
//        get the current value for this field
        var current_value = current_value();
        
        //create the edit element
        var editbox = $(document.createElement('input'))
                    .attr('type','text')
                    .attr('value',current_value)
                    .attr('previous',current_value)
                    .addClass('reagent_value')
                    .blur(function(){
                        //test if the value has changed
                        //if(new != previous){
                        //  update mysql 
                        //  fetch updated value 
                        //  replace with new element showing new value } 
                        //else{ 
                        //  replace with new element showing previous value}
                    });
                  
        $(this).replaceWith(editbox);
    })
}    
        
   
    