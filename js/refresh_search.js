$(function(){
    bind_search_functions();
    refresh_search();
});

function bind_search_functions(){
    
    // capture dynamic search function input, verify length and prevent multiple searches by forcing a delay
    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();

    $('.search_input').unbind()
    .keyup(function() {
        delay(function(){
            refresh_search();
        }, 500 );
    })
    //select the text withing a search window when it is focused on
    .focus(function(){
        $(this).select();
    });

    $('.search_field').unbind().change(function(){
        refresh_search();
    });

    $('.add_search_row').remove();
    $('.remove_search_row').remove();

    $('.search_row:not(:first)').each(function(){
        remove_search_row_button($(this));
    });

    $('.search_row:last').each(function(){
        add_search_row_button($(this));
    });




}

function add_search_row_button(search_row){

    $(document.createElement('div'))
        .addClass('add_search_row button ui-icon ui-icon-plus')
        .click(function(){
            $(this).closest('.search_row').clone().appendTo('.search_form');
            bind_search_functions();
            $('.search_input:last').val('').focus();
        })
        .button()
        .appendTo(search_row);
    
    rename_search_rows();
}

function remove_search_row_button(search_row){

    $(document.createElement('div'))
        .addClass('remove_search_row button ui-icon ui-icon-minus')
        .click(function(){
            $(this).closest('.search_row').remove();
            bind_search_functions();
            refresh_search();
        })
        .button()
        .appendTo(search_row);
}

function rename_search_rows(){
    $('.search_row').each(function(){
        var row = $(this).index();
        $(this).find('.search_input').attr('name','search['+row+'][text]');
        $(this).find('.search_field').attr('name','search['+row+'][field]');
    });
}
    
function refresh_search(page) {

    // TODO    disable search box while function is running, add loading gifs
    var newpage = page;
    //remove existing details
    $("#content_container").children().remove();
    $('#side').children().remove();

    //capture search terms and fields into an ajax datastring
    var encoded = $('.search_form').serializeArray();
    // console.log(encoded);
    if(newpage > 0){
        encoded.push({"name":"page","value":newpage});
    }
    // console.log(encoded);

    $.ajax({
        url: "utility/fetch_reagents_list.php",
        type: 'POST',
        data: encoded,
        //add ability to specifiy page number
        dataType: 'json',
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

                bind_titlebar_functions();
            }
            else{
                $(document.createElement('div')).text("No reagents were found for that search").appendTo("#content_container");
            }

            //add page indicator and next page button 
            if(parseInt(data.pages,10) > 1){

                //next page button
                if(parseInt(data.page,10) < parseInt(data.pages,10)){
                    $(document.createElement('span'))
                    .text('>')
                    .button()
                    .css('float','right')
                    .click(function(){
                        refresh_search(parseInt(data.page,10)+1);
                    })
                    .appendTo("#content_container");
                }
                //add an indicator
                $(document.createElement('span'))
                .text('(page '+data.page+' of '+data.pages+')')
                .button()
                .css('float','right')
                .appendTo("#content_container");

                //prev page button
                if(parseInt(data.page,10) >1 ){
                    $(document.createElement('span'))
                    .text('<')
                    .button()
                    .css('float','right')
                    .click(function(){
                        refresh_search(parseInt(data.page,10)-1);
                    })
                    .appendTo("#content_container");
                }
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

            //automatically show details if the search only returns one reagent (remember the "-->" placeholder is also a ".reagent_titlebar" so we check for 2)
            if($('.reagent_titlebar').length==2){
                var element = $('.reagent_titlebar').first();
                swap_reagents(element);
            }

        },
        error: function(){
            $(document.createElement('div'))
            .appendTo("#content_container")
            .text("something went wrong with the search. reload the page, get a cup of coffee. Call Dan if that doesn't work");
        }
    });
}