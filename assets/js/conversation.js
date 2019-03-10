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
        },
        appendMessageChunk: function(messageHtml)
        {
            if($('#message-zone').is(':empty'))
            {
                console.log('CHUNK #1');
                $('#org-msg-zone .message:last').find('.text').append(messageHtml);
            }
            else 
            {
                console.log('CHUNK #2');
                $('#message-zone .message:last-child').find('.text').append(messageHtml);
            }

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
    $(document).on("keypress", "#form-message", function(event){
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

    subscribeToTopic(clientInformation.wsConversationRoute);
    function subscribeToTopic(topic)
    {
        session.subscribe(topic, function(uri, messageHtml) 
        {
            if(messageHtml.msg.msgType == 'msg_block')
            {
                console.log('Blok');
                Chat.appendMessage(messageHtml.msg.template);
            }
            else 
            {
                console.log('Chunk');
                Chat.appendMessageChunk(messageHtml.msg.template);
            }
        });

        // session.subscribe(topic + '/notifications', function(uri, payload) 
        // {
        //     var responsePayload = JSON.parse(payload);
        //     var html = `
        //     <div class="message" data-writing="` + clientInformation.username + `">
        //         <img class="avatar-md" src="/avatar.jpg" data-toggle="tooltip" data-placement="top" title="" alt="avatar" data-original-title="Keith">
        //         <div class="text-main">
        //             <div class="text-group">
        //                 <div class="text typing">
        //                     <div class="wave">
        //                         <span class="dot"></span>
        //                         <span class="dot"></span>
        //                         <span class="dot"></span>
        //                     </div>
        //                 </div>
        //             </div>
        //         </div>
        //     </div>`;

        //     $('[data-writing="' + clientInformation.username + '"]').remove();
        //     for(var key in responsePayload) 
        //     {
        //         if(responsePayload.hasOwnProperty(key)) 
        //         {
        //             var who = responsePayload[key].username;
        //             var doWhat = responsePayload[key].message;

        //             $('#message-zone').append(html);
        //             scrollToBottom(document.getElementById('content'));
        //         }
        //     }   
        // });
    }

    $(document).on("click", ".discussions li", function(event) {

        event.preventDefault();
    
        $('.discussions li').removeClass('active');
        $(this).addClass('active');
    
        var cid = $(this).attr('data-cid');
        var stateObj = { foo: "bar" };
        window.history.pushState(stateObj, 'Conversation', '/conversation/' + cid);

        session.unsubscribe(clientInformation.wsConversationRoute);
        session.unsubscribe(clientInformation.wsConversationRoute + '/notifications');
        
        clientInformation.wsConversationRoute = 'conversation/' + cid;
        clientInformation.conversationId = cid;
        subscribeToTopic(clientInformation.wsConversationRoute);
        
        $.ajax({
            url: '/message/' + cid + '/section',
            type: 'GET',
            success: function(data) 
            {
                $("#msg-section").remove();
                $(data).insertAfter("#sidebar");
    
                scrollToBottom(document.getElementById('content'));
            }
        });
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

    console.log("Successfully Connected!");
})

webSocket.on("socket/disconnect", function(error) {

    console.log("Disconnected for " + error.reason + " with code " + error.code);
})