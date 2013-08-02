

(function() {
    if (typeof window.janrain !== 'object') window.janrain = {};
    if (typeof window.janrain.settings !== 'object') window.janrain.settings = {};

    // janrain.settings.tokenUrl = 'http://localhost/labindex/utility/janrain.php';

    janrain.settings.tokenAction='event';

    function isReady() { janrain.ready = true; }
    if (document.addEventListener) {
      document.addEventListener("DOMContentLoaded", isReady, false);
    } else {
      window.attachEvent('onload', isReady);
    }

    var e = document.createElement('script');
    e.type = 'text/javascript';
    e.id = 'janrainAuthWidget';

    if (document.location.protocol === 'https:') {
      e.src = 'https://rpxnow.com/js/lib/labindex/engage.js';
    } else {
      e.src = 'http://widget-cdn.rpxnow.com/js/lib/labindex/engage.js';
    }

    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(e, s);


})();



function janrainWidgetOnload() {
    janrain.events.onProviderLoginToken.addHandler(function(response) {
        $.ajax({
            type: "POST",
            url: "utility/authenticate.php",
            data: "token=" + response.token
        })
        .done(function(res){
            janrain.engage.signin.modal.close();
            if(res.stat==='ok'){
                $('#side').addClass('verified');
                $('.janrainEngage').remove();

                $(document.createElement('a'))
                    .text('Hi '+res.profile['name']['givenName'])
                    .appendTo('.user_panel');

                $(document.createElement('a'))
                    .text('(logout)')
                    .attr('href','utility/destroy_session.php')
                    .appendTo('.user_panel');

            }else{ 
                // TODO error handling
            }
        })
        .fail(function(){
            janrain.engage.signin.modal.close();
            // TODO error handling
        });
    });
}
