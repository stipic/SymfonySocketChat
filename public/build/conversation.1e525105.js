(window.webpackJsonp=window.webpackJsonp||[]).push([["conversation"],{k6dk:function(n,e,t){(function(n){t("ng4s");var e=window.location.hostname,s=WS.connect("ws://"+e+":5510");s.on("socket/connect",function(e){function t(n){n.scrollTop=n.scrollHeight}t(document.getElementById("content"));var s=function(e){n("#message-zone").append(e),t(document.getElementById("content"))};n(document).on("click","#submit-message",function(t){t.preventDefault();var s=n("#form-message").val();s&&n.ajax({url:"/message/"+clientInformation.conversationId+"/new",type:"POST",data:{message:s},complete:function(n){}}),n("#form-message").val(""),e.publish(clientInformation.wsConversationRoute+"/notifications","")}),document.getElementById("form-message").focus(),n("#form-message").keypress(function(e){13===e.keyCode&&(n("#submit-message").trigger("click"),n("#form-message").val(""))}),n(document).on("input","#form-message",function(t){t.preventDefault();var s=n("#form-message").val();e.publish(clientInformation.wsConversationRoute+"/notifications",s)}),e.subscribe(clientInformation.wsConversationRoute,function(n,e){s(e.msg)}),e.subscribe("online",function(e,t){var s=JSON.parse(t);n("li[data-usid]").each(function(e){var t=n(this).attr("data-usid");for(var a in n('li[data-usid="'+t+'"]').find(".user-details").removeClass("online"),s)s.hasOwnProperty(a)&&a==t&&n('li[data-usid="'+t+'"]').find(".user-details").addClass("online")})}),e.subscribe(clientInformation.wsConversationRoute+"/notifications",function(e,s){var a=JSON.parse(s),o='\n        <div class="message" data-writing="'+a.username+'">\n            <img class="avatar-md" src="/avatar.jpg" data-toggle="tooltip" data-placement="top" title="" alt="avatar" data-original-title="Keith">\n            <div class="text-main">\n                <div class="text-group">\n                    <div class="text typing">\n                        <div class="wave">\n                            <span class="dot"></span>\n                            <span class="dot"></span>\n                            <span class="dot"></span>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>';for(var i in n('[data-writing="'+a.username+'"]').remove(),a)if(a.hasOwnProperty(i)){a[i].displayName,a[i].message;n("#message-zone").append(o),t(document.getElementById("content"))}}),console.log("Successfully Connected!")}),s.on("socket/disconnect",function(n){console.log("Disconnected for "+n.reason+" with code "+n.code)})}).call(this,t("EVdn"))}},[["k6dk","runtime",1,0]]]);