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
            $('#org-msg-zone').append(messageHtml);

            scrollToBottom(document.getElementById('content'));
        },
        appendMessageChunk: function(messageHtml)
        {
            console.log('CHUNK #1');
            $('#org-msg-zone .message:last').find('.text').append(messageHtml);

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
        
        // $("#form-message").reset();
        $("#form-message").val('').change();
        $("#form-message").html('');

        clientInformation.isWriting = false;
        session.publish(clientInformation.wsConversationRoute + '/notifications', clientInformation.isWriting);
    });

    document.getElementById("form-message").focus();
    $(document).on("keypress", "#form-message", function(event){
        if(event.keyCode === 13 ) {
            event.preventDefault();
            $('#submit-message').trigger('click');
            $("#form-message").val("");
        }
    });

    $(document).on("input", "#form-message", function(event) {
        event.preventDefault();
        var msg = $("#form-message").val();

        if(msg.length > 0 && clientInformation.isWriting == false) 
        {
            clientInformation.isWriting = true;
            session.publish(clientInformation.wsConversationRoute + '/notifications', clientInformation.isWriting);
        }
        else if(msg.length == 0 && clientInformation.isWriting == true) 
        {
            clientInformation.isWriting = false;
            session.publish(clientInformation.wsConversationRoute + '/notifications', clientInformation.isWriting);
        }
    });

    subscribeToTopic(clientInformation.wsConversationRoute);
    function subscribeToTopic(topic)
    {
        session.subscribe(topic, function(uri, messageHtml) 
        {
            // first message..
            if($("#content").hasClass("empty"))
            {
                $("#content").removeClass('empty');
                $("#content .col-md-12").attr('id', 'org-msg-zone');
                $("#content .no-messages").remove();
            }

            if(messageHtml.msg.msgType == 'msg_block')
            {
                Chat.appendMessage(messageHtml.msg.template);
            }
            else 
            {
                Chat.appendMessageChunk(messageHtml.msg.template);
            }
        });

        session.subscribe(topic + '/notifications', function(uri, payload) 
        {
            console.log(payload);
            var responsePayload = JSON.parse(payload);
            var html = `
            <div class="message" data-writing="` + clientInformation.username + `">
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

            $('[data-writing="' + clientInformation.username + '"]').remove();
            for(var key in responsePayload) 
            {
                if(
                    responsePayload.hasOwnProperty(key) && 
                    !$('[data-writing="' + clientInformation.username + '"]').length && 
                    clientInformation.username != key
                ) 
                {
                    $('#writing-notif-zone').append(html);
                    scrollToBottom(document.getElementById('content'));
                }
            }   
        });
    }

    $(document).on("click", ".discussions li", function(event) {

        event.preventDefault();
    
        var cid = $(this).attr('data-cid');
        if(cid != clientInformation.conversationId)
        {
            var stateObj = { foo: "bar" };

            $('.discussions li').removeClass('active');
            $(this).addClass('active');

            $('#loading').show();
            $('#content').hide();
            
            $.ajax({
                url: '/message/' + cid + '/section',
                type: 'GET',
                success: function(data) 
                {
                    $("#msg-section").remove();
                    $(data).insertAfter("#sidebar");
        
                    scrollToBottom(document.getElementById('content'));
                    window.history.pushState(stateObj, 'Conversation', '/conversation/' + cid);

                    session.unsubscribe(clientInformation.wsConversationRoute);
                    session.unsubscribe(clientInformation.wsConversationRoute + '/notifications');
                    $('#writing-notif-zone').html('');
                    
                    clientInformation.wsConversationRoute = 'conversation/' + cid;
                    clientInformation.conversationId = cid;
                    subscribeToTopic(clientInformation.wsConversationRoute);

                    session.publish('unreaded/' + clientInformation.username, cid);

                    $('#loading').hide();
                    $('#content').css({display: 'flex'});
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
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

    session.subscribe('unreaded/' + clientInformation.username , function (uri, payload) {

        console.log('unreaded: ', payload);
        var responsePayload = JSON.parse(payload);
        for(var key in responsePayload)
        {
            if(responsePayload.hasOwnProperty(key))
            {
                var injectVal = '';
                if(responsePayload[key] > 0)
                {
                    injectVal = responsePayload[key];
                }

                $("li[data-cid='" + key + "'] .user-nickname span:last").html(injectVal);
            }
        }
    });

    console.log("Successfully Connected!");
})

webSocket.on("socket/disconnect", function(error) {

    console.log("Disconnected for " + error.reason + " with code " + error.code);
    location.reload();
})