function refresh_search(keyword, page) {

    //    disable search box while function is running
    $('input#search_input').addClass('loading');    
    $("#content_container").children().remove().addClass('loading');

    //search parameters
    var url = "";
    var dataString = 'keyword='+ keyword;
    
    dataString += '&callback=?';
 
    if(page > 0){
        dataString += '&page='+ page
    }
 

    $.ajax({
        url: "utility/fetch_reagents_list.php",
        dataType: 'json',
        data: dataString,
        success: function(data){		
     
            if(data.rows > 0){
                $.each(data.reagents, function(key, val) {
                    
                    
                    //create entry container div for each reagent
                    $(document.createElement('div'))
                    .attr('id', val.r_rid)
                    .attr('type', val.r_reagent_type)
                    .addClass('reagent_div')
                    .appendTo("#content_container");
                    
                    $(document.createElement('div'))
                    .text(val.r_systematicname)
                    .addClass('reagent_titlebar')
                    .appendTo("#"+val.r_rid);;
                  
                });
                //create page navigation
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
                    
                bind_detail_functions();
        
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
	