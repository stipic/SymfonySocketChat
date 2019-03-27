require('./app.js');

import Tagify from '@yaireo/tagify';
import qq from 'fine-uploader';

$(document).on("click", "#file-picker", function(event) {
    event.preventDefault();
    $('.qq-upload-button-selector.qq-upload-button').find('input').trigger('click');
});


$(document).on("click","#emoji-picker",function(e){
    e.preventDefault();
    e.stopPropagation();
     $('.intercom-composer-emoji-popover').toggleClass("active");
 });
 
 $(document).click(function (e) {
     if ($(e.target).attr('class') != '.intercom-composer-emoji-popover' && $(e.target).parents(".intercom-composer-emoji-popover").length == 0) {
         $(".intercom-composer-emoji-popover").removeClass("active");
     }
 });
 
 $(document).on("click",".intercom-emoji-picker-emoji",function(e){
     $("#form-message").append($(this).html());
 });
 
 $('.intercom-composer-popover-input').on('input', function() {
     var query = this.value;
     if(query != ""){
       $(".intercom-emoji-picker-emoji:not([title*='"+query+"'])").hide();
     }
     else{
       $(".intercom-emoji-picker-emoji").show();
     }
 });


// var input = document.querySelector('input[name=participant]'),
// tagify = new Tagify(input, {
//     delimiters          : ",| ",  // add new tags when a comma or a space character
//     maxTags             : 6,
//     blacklist           : ["fuck", "shit", "pussy"],
//     keepInvalidTags     : false, 
//     whitelist           : ["temple","stun","detective","sign","passion","routine","deck","discriminate","relaxation","fraud","attractive","soft","forecast","point","thank","stage","eliminate","effective","flood","passive","skilled","separation","contact","compromise","reality","district","nationalist","leg","porter","conviction","worker","vegetable","commerce","conception","particle","honor","stick","tail","pumpkin","core","mouse","egg","population","unique","behavior","onion","disaster","cute","pipe","sock","dialect","horse","swear","owner","cope","global","improvement","artist","shed","constant","bond","brink","shower","spot","inject","bowel","homosexual","trust","exclude","tough","sickness","prevalence","sister","resolution","cattle","cultural","innocent","burial","bundle","thaw","respectable","thirsty","exposure","team","creed","facade","calendar","filter","utter","dominate","predator","discover","theorist","hospitality","damage","woman","rub","crop","unpleasant","halt","inch","birthday","lack","throne","maximum","pause","digress","fossil","policy","instrument","trunk","frame","measure","hall","support","convenience","house","partnership","inspector","looting","ranch","asset","rally","explicit","leak","monarch","ethics","applied","aviation","dentist","great","ethnic","sodium","truth","constellation","lease","guide","break","conclusion","button","recording","horizon","council","paradox","bride","weigh","like","noble","transition","accumulation","arrow","stitch","academy","glimpse","case","researcher","constitutional","notion","bathroom","revolutionary","soldier","vehicle","betray","gear","pan","quarter","embarrassment","golf","shark","constitution","club","college","duty","eaux","know","collection","burst","fun","animal","expectation","persist","insure","tick","account","initiative","tourist","member","example","plant","river","ratio","view","coast","latest","invite","help","falsify","allocation","degree","feel","resort","means","excuse","injury","pupil","shaft","allow","ton","tube","dress","speaker","double","theater","opposed","holiday","screw","cutting","picture","laborer","conservation","kneel","miracle","brand","nomination","characteristic","referral","carbon","valley","hot","climb","wrestle","motorist","update","loot","mosquito","delivery","eagle","guideline","hurt","feedback","finish","traffic","competence","serve","archive","feeling","hope","seal","ear","oven","vote","ballot","study","negative","declaration","particular","pattern","suburb","intervention","brake","frequency","drink","affair","contemporary","prince","dry","mole","lazy","undermine","radio","legislation","circumstance","bear","left","pony","industry","mastermind","criticism","sheep","failure","chain","depressed","launch","script","green","weave","please","surprise","doctor","revive","banquet","belong","correction","door","image","integrity","intermediate","sense","formal","cane","gloom","toast","pension","exception","prey","random","nose","predict","needle","satisfaction","establish","fit","vigorous","urgency","X-ray","equinox","variety","proclaim","conceive","bulb","vegetarian","available","stake","publicity","strikebreaker","portrait","sink","frog","ruin","studio","match","electron","captain","channel","navy","set","recommend","appoint","liberal","missile","sample","result","poor","efflux","glance","timetable","advertise","personality","aunt","dog"],
//     dropdown : {
//         enabled: 4,
//     }
// })

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

    initUploader();
    function initUploader()
    {
        $('#fine-uploader').unbind().empty();
        var uploader = new qq.FineUploader({
            element: document.getElementById('fine-uploader'),
            request: {
                endpoint: '/_uploader/gallery/upload',
                params: {
                    conversationId: clientInformation.conversationId
                }
            },
            multiple: false,
            autoUpload: false,
            deleteFile: {
                enabled: false,
            },
            callbacks: {
                onSubmit: function(id) {
                    var file = this.getFile(id);
                    processUpload(file, this);
                }
            }
        });
    }

    var isAskedForConfirmUpload = false;
    function processUpload(file, uploader)
    {
        console.log(file);
        $('#new-upload').modal('show');
        $('#new-upload').on('shown.bs.modal', function (e) {
            isAskedForConfirmUpload = true;
            $(document).on("click", "#confirm-upload", function(event) {
                event.preventDefault();
                if(isAskedForConfirmUpload == true)
                {
                    isAskedForConfirmUpload = false;
                    uploader.uploadStoredFiles();
                    $('#new-upload').modal('hide');
                }
            });
        });
    }

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

            $('#content').hide();
            $('#loading').show();
                       
            $.ajax({
                url: '/message/' + cid + '/section',
                type: 'GET',
                success: function(data) 
                {
                    $("#msg-section").remove();
                    $(data).insertAfter("#sidebar");
        
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

                    scrollToBottom(document.getElementById('content'));
                    
                    initUploader();
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