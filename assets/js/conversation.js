require('./app.js');

var host = window.location.hostname;
// host = '5.189.166.104';

var webSocket = WS.connect("ws://" + host + ":5510");

webSocket.on("socket/connect", function(session) {

    function scrollToBottom(el) { el.scrollTop = el.scrollHeight; }
    scrollToBottom(document.getElementById('content'));

    var Chat = 
    {
        appendMessage: function(messageHtml)
        {
            $('#message-zone').append(messageHtml);

            scrollToBottom(document.getElementById('content'));
        }
    };

    $(document).on("click", "#submit-message", function(event) {
        event.preventDefault();
        // napravi POST request.
        var msg = $("#form-message").val();
        
        if(msg) 
        {
            $.ajax({
                url: '/message/' + clientInformation.conversationId + '/new',
                type: 'POST',
                data: {
                    'message': msg
                },
                complete: function(data) 
                {
                }
            });
        }
        
        $("#form-message").val("");

        session.publish(clientInformation.wsConversationRoute + '/notifications', '');
    });

    document.getElementById("form-message").focus();
    $('#form-message').keypress(function(event){
        if(event.keyCode === 13 ) {
            $('#submit-message').trigger('click');
            $("#form-message").val("");
        }
    });

    $(document).on("input", "#form-message", function(event) {
        event.preventDefault();
        var msg = $("#form-message").val();
        session.publish(clientInformation.wsConversationRoute + '/notifications', msg);
    });

    session.subscribe(clientInformation.wsConversationRoute, function(uri, messageHtml) 
    {
        Chat.appendMessage(messageHtml.msg);
    });

    session.subscribe('online', function(uri, payload) 
    {
        var responsePayload = JSON.parse(payload);
        
        $('li[data-usid]').each(function(event) {

            var userId = $(this).attr('data-usid');

            $('li[data-usid="'+userId+'"]').find('.user-details').removeClass('online');

            for(var key in responsePayload)
            {
                if(responsePayload.hasOwnProperty(key))
                {
                    if(key == userId)
                    {
                        $('li[data-usid="'+userId+'"]').find('.user-details').addClass('online');
                    }
                }
            }
        });
    });

    session.subscribe(clientInformation.wsConversationRoute + '/notifications', function(uri, payload) 
    {
        var responsePayload = JSON.parse(payload);

        var html = `
        <div class="message" data-writing="` + responsePayload.username + `">
            <img class="avatar-md" src="/avatar.jpg" data-toggle="tooltip" data-placement="top" title="" alt="avatar" data-original-title="Keith">
            <div class="text-main">
                <div class="text-group">
                    <div class="text typing">
                        <div class="wave">
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        $('[data-writing="' + responsePayload.username + '"]').remove();
        for(var key in responsePayload) 
        {
            if(responsePayload.hasOwnProperty(key)) 
            {
                var who = responsePayload[key].displayName;
                var doWhat = responsePayload[key].message;

                var message = who + ' ' + doWhat;

                $('#message-zone').append(html);
                scrollToBottom(document.getElementById('content'));
            }
        }   
    });

    console.log("Successfully Connected!");
})

webSocket.on("socket/disconnect", function(error) {

    console.log("Disconnected for " + error.reason + " with code " + error.code);
})