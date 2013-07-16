
function refresh_search(keyword, page) {

    // TODO    disable search box while function is running, add loading gifs

    //remove existing details
    $("#content_container").children().remove();
    $('#side').children().remove();
    $('.page_navigator').remove();

    var dataString = "";

    //create a simple fuzzy search from the website search
    if(keyword.length > 2){
        dataString += 'keyword='+ keyword;
        dataString += '&'+query_string;
        dataString += '&callback=?';
        if(page > 0){
            dataString += '&page='+ page;
        }
    }
    //TODO important, add ability to remove url-encoded query for fresh search
    //if you don't have an appropriate keyword, check for a url-encoded query
    else if (keyword.length < 3 && query_string.length > 0){
        dataString += query_string;
        dataString  += '&callback=?';
        if(page > 0){
            dataString += '&page='+ page;
        }
    }
    else{return;}

    $.ajax({
        url: "utility/fetch_reagents_list.php",
        dataType: 'json',
        data: dataString,
        success: function(data){

            if(data.rows > 0){
                $.each(data.reagents, function(key, val) {
                    //create .reagent_div container div for each reagent
                    $(document.createElement('div'))
                    .attr('id', val.r_rid)
                    .attr('identifier', val.r_rid)
                    .attr('type', val.r_reagent_type)
                    .attr('table', 'reagent_detail')
                    .addClass('reagent_div')
                    .addClass('table_id')
                    .appendTo("#content_container");

                    $(document.createElement('div'))
                    .addClass('reagent_titlebar')
                    .appendTo("#"+val.r_rid);

                    $(document.createElement('div'))
                    .text(val.r_systematicname)
                    .addClass('titlebar_value')
                    .appendTo("#"+val.r_rid+" > .reagent_titlebar");
                });
                // TODO automatically launch if results return a single totalRows

                $(document.createElement('span'))
                    .text('page: ')
                    .addClass("page_navigator")
                    .appendTo('#top');

                for (var i = 1; i <= data.pages; i++) {
                    var current_search = search_input;
                    var current_page   = i;
                    $(document.createElement('span'))
                    .text(i)
                    .attr("value",i)
                    .addClass("page_navigator")
                    .click(function(){
                        var j = $(this).attr("value");
                        var search_input = $("#search_input").val();
                        if(search_input.length > 2){
                            refresh_search(search_input, j);
                        }
                    })
                    .appendTo("#top");
                }
                //highlight the current page and create 'next'/'prev' buttons
                $(".page_navigator[value="+data.page+"]").css("text-decoration", "underline");
                bind_titlebar_functions();
            }
            else{
                $(document.createElement('div')).text("No reagents were found for that search").appendTo("#content_container");
            }

            //create a placeholder in the #side
            $(document.createElement('div'))
            .addClass('reagent_div')
            .addClass('placeholder')
            .appendTo('#side');

            $(document.createElement('div'))
            .addClass('reagent_titlebar')
            .appendTo('#side .reagent_div');

            $(document.createElement('div'))
            .addClass('reagent_value')
            .text('--->')
            .appendTo('#side .reagent_titlebar');


        },
        error: function(){
            $(document.createElement('div'))
            .appendTo("#content_container")
            .text("something went wrong with the search. reload the page, get a cup of coffee. Call Dan if that doesn't work");
        }
    });
}


function new_reagent(type){
    var status;
    $.ajax({
        async: false,
        type: "POST",
        url: "utility/insert_new_reagent.php",
        dataType: 'json',
        data: {
            r_reagent_type:type
        }
    })
    //TODO no real reason why I need to do this as AJAX, submit a form to insert_new_reagent.php and use header('Location: ../index.php?r_rid=75'); 
    .done(function( data ) {
        if(data.rows == 1){
            var pathname = window.location.pathname;
            var new_rid  = data.new_rid;
            alert(pathname);
            window.location.href = pathname+"?r_rid="+new_rid;
       }
       else{ status = 0; }
   })
    .fail(function(){status = 0;});
}







