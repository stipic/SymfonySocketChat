(window.webpackJsonp=window.webpackJsonp||[]).push([["app"],{0:function(e,n,o){e.exports=o("ng4s")},ng4s:function(e,n,o){o("sZ/o");var t=window.location.hostname,s=WS.connect("ws://"+t);s.on("socket/connect",function(e){var n={appendMessage:function(e,n){var o;o=e.username==clientInformation.username?"me":e.displayName;var t=document.getElementById("chat-list"),s=document.createElement("li");s.appendChild(document.createTextNode(o+": "+n)),t.appendChild(s)},sendMessage:function(n){clientInformation.message=n,e.publish(clientInformation.wsConversationRoute,JSON.stringify(clientInformation)),this.appendMessage(clientInformation,n)}};document.getElementById("form-submit").addEventListener("click",function(){var e=document.getElementById("form-message").value;e||alert("Please send something on the chat"),n.sendMessage(e),document.getElementById("form-message").value=""},!1),e.subscribe(clientInformation.wsConversationRoute,function(e,o){var t=JSON.parse(o),s=t.message;n.appendMessage(t,s)}),console.log("Successfully Connected!")}),s.on("socket/disconnect",function(e){console.log("Disconnected for "+e.reason+" with code "+e.code)})},"sZ/o":function(e,n,o){}},[[0,"runtime"]]]);