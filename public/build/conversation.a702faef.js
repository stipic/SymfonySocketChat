(window.webpackJsonp=window.webpackJsonp||[]).push([["conversation"],{k6dk:function(n,e,t){(function(n){t("ng4s");var e=window.location.hostname,s=WS.connect("ws://"+e+":5510");s.on("socket/connect",function(e){function t(n){n.scrollTop=n.scrollHeight}t(document.getElementById("content"));var s={appendMessage:function(e){n("#message-zone").append(e),t(document.getElementById("content"))}};function o(o){e.subscribe(o,function(n,e){s.appendMessage(e.msg)}),e.subscribe(o+"/notifications",function(e,s){var o=JSON.parse(s),a='\n            <div class="message" data-writing="'+o.username+'">\n                <img class="avatar-md" src="/avatar.jpg" data-toggle="tooltip" data-placement="top" title="" alt="avatar" data-original-title="Keith">\n                <div class="text-main">\n                    <div class="text-group">\n                        <div class="text typing">\n                            <div class="wave">\n                                <span class="dot"></span>\n                                <span class="dot"></span>\n                                <span class="dot"></span>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>';for(var i in n('[data-writing="'+o.username+'"]').remove(),o)if(o.hasOwnProperty(i)){o[i].displayName,o[i].message;n("#message-zone").append(a),t(document.getElementById("content"))}})}n(document).on("click","#submit-message",function(t){t.preventDefault();var s=n("#form-message").val();s&&n.ajax({url:"/message/"+clientInformation.conversationId+"/new",type:"POST",data:{message:s},complete:function(n){}}),n("#form-message").val(""),e.publish(clientInformation.wsConversationRoute+"/notifications","")}),document.getElementById("form-message").focus(),n(document).on("keypress","#form-message",function(e){13===e.keyCode&&(n("#submit-message").trigger("click"),n("#form-message").val(""))}),n(document).on("input","#form-message",function(t){t.preventDefault();var s=n("#form-message").val();e.publish(clientInformation.wsConversationRoute+"/notifications",s)}),o(clientInformation.wsConversationRoute),n(document).on("click",".discussions li",function(s){s.preventDefault(),n(".discussions li").removeClass("active"),n(this).addClass("active");var a=n(this).attr("data-cid");window.history.pushState({foo:"bar"},"Conversation","/conversation/"+a),e.unsubscribe(clientInformation.wsConversationRoute),e.unsubscribe(clientInformation.wsConversationRoute+"/notifications"),clientInformation.wsConversationRoute="conversation/"+a,clientInformation.conversationId=a,o(clientInformation.wsConversationRoute),n.ajax({url:"/message/"+a+"/section",type:"GET",success:function(e){n("#msg-section").remove(),n(e).insertAfter("#sidebar"),t(document.getElementById("content"))}})}),e.subscribe("online",function(e,t){var s=JSON.parse(t);n("li[data-usid]").each(function(e){var t=n(this).attr("data-usid");for(var o in n('li[data-usid="'+t+'"]').find(".user-details").removeClass("online"),s)s.hasOwnProperty(o)&&o==t&&n('li[data-usid="'+t+'"]').find(".user-details").addClass("online")})}),console.log("Successfully Connected!")}),s.on("socket/disconnect",function(n){console.log("Disconnected for "+n.reason+" with code "+n.code)})}).call(this,t("EVdn"))}},[["k6dk","runtime",1,0]]]);