function refresh_reagent_details(reagent_id) {

    //    disable search box while function is running    
    $(this).siblings().remove();

    //search parameters
    var url = "";
    dataString += '&callback=?';
 
    if(reagent_id > 0){
        dataString += '&reagent_id='+ reagent_id
    }
  
    url = "utility/fetch_reagent_details.php";	
    $.ajax({
        url: url,
        dataType: 'json',
        data: dataString,
        success: function(data){		
     
            if(data.rows === 1){
                
                //TODO add this functionality to the php fetch file
                var field_names = data.field_names;
                $.each(data.fields, function(key, val) {
                    
                    //create label and value for each field
                    
                    
                    
                    
                    
                    
                    $(document.createElement('div'))
                    .appendTo("#content_container")
                        
                    .click(function(){
                        //document.location.href='details.php?type=virus&id='+val.virus_id;
                        })
                    .text(val.r_systematicname)
                    .attr('id', val.r_rid)
                    .attr('type', val.r_reagent_type)
                    .addClass('list-button')
                    .addClass('reagents')
                    .button();	
                });
                //create pages navigation
                for (var i = 1; i <= data.pages; i++) {
                    var current_search = search_input;
                    var current_page   = i;
                    
                    $(document.createElement('span'))
                    .text(i)
                    .attr("value",i)
                    .addClass("page_navigator")
                    .click(function(){
                        var j = $(this).attr("value")
                        var search_input = $("#search_input").val();
                        if(search_input.length > 2){
                            refresh_search(search_input, j);
                        }
                    })
                    .appendTo("#content_container");
                };
                
                //highlight the current page and create 'next'/'prev' buttons
                $(".page_navigator[value="+data.page+"]").css("text-decoration", "underline");
                    
                
        
            }
            else{
                $(document.createElement('div')).text("No reagents were found for that search").appendTo("#content_container");
            }

        },
        error: function(){
            $(document.createElement('div'))
            .appendTo("#content_container")
            .text("something went wrong with the search. reload the page, get a cup of coffee. Call Dan if that doesn't work");
        }
    });
    


  
}
	