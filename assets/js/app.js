require('../css/app.css');


var host = window.location.hostname;
host = '5.189.166.104';

var webSocket = WS.connect("ws://" + host + ":8080");

webSocket.on("socket/connect", function(session) {


    var Chat = 
    {
        appendMessage: function(entityPayload, message)
        {
            var from;
            
            if(entityPayload.username == clientInformation.username)
            {
                from = "me";
            }
            else
            {
                from = entityPayload.displayName;
            }

            var ul = document.getElementById("chat-list");
            var li = document.createElement("li");
            li.appendChild(document.createTextNode(from + ': ' + message));
            ul.appendChild(li);
        },
        sendMessage: function(text)
        {
            clientInformation.message = text;
            session.publish(clientInformation.wsConversationRoute, JSON.stringify(clientInformation));

            this.appendMessage(clientInformation, text);
        }
    };

    document.getElementById("form-submit").addEventListener("click", function(){
        var msg = document.getElementById("form-message").value;
        
        if(!msg) {
            alert("Please send something on the chat");
        }
        
        Chat.sendMessage(msg);
        document.getElementById("form-message").value = "";
    }, false);

    session.subscribe(clientInformation.wsConversationRoute, function(uri, payload) 
    {
        var responsePayload = JSON.parse(payload);
        var message = responsePayload.message;
        Chat.appendMessage(responsePayload, message);
    });

    console.log("Successfully Connected!");
})

webSocket.on("socket/disconnect", function(error) {

    console.log("Disconnected for " + error.reason + " with code " + error.code);
})