(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["conversation"],{

/***/ "./assets/js/conversation.js":
/*!***********************************!*\
  !*** ./assets/js/conversation.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function($) {__webpack_require__(/*! ./app.js */ "./assets/js/app.js");

var host = window.location.hostname; // host = '5.189.166.104';

var webSocket = WS.connect("ws://" + host + ":5510");
webSocket.on("socket/connect", function (session) {
  function scrollToBottom(el) {
    el.scrollTop = el.scrollHeight;
  }

  scrollToBottom(document.getElementById('content'));
  var Chat = {
    appendMessage: function appendMessage(entityPayload, message) {
      var currentdate = new Date();
      var metaData = currentdate.getDate() + '.' + currentdate.getMonth() + '.' + currentdate.getFullYear() + ' ' + currentdate.getHours() + ':' + currentdate.getMinutes() + ':' + currentdate.getSeconds() + ' - ' + entityPayload.displayName;
      var html = "\n            <div class=\"message\">\n                <img class=\"avatar-md\" src=\"/avatar.jpg\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" alt=\"avatar\" data-original-title=\"Keith\">\n                <div class=\"text-main\">\n                    <div class=\"text-group\">\n                        <div class=\"text\">\n                            <p>" + message + "</p>\n                        </div>\n                    </div>\n                    <span>" + metaData + "</span>\n                </div>\n            </div>";
      $('#message-zone').append(html);
      scrollToBottom(document.getElementById('content'));
    },
    sendMessage: function sendMessage(text) {
      clientInformation.message = text;
      session.publish(clientInformation.wsConversationRoute, JSON.stringify(clientInformation));
      this.appendMessage(clientInformation, text);
    }
  };
  $(document).on("click", "#submit-message", function (event) {
    event.preventDefault();
    var msg = $("#form-message").val();

    if (!msg) {
      alert("Please send something on the chat");
    }

    Chat.sendMessage(msg);
    $("#form-message").val("");
    session.publish(clientInformation.wsConversationRoute + '/notifications', '');
  });
  $(document).on("input", "#form-message", function (event) {
    event.preventDefault();
    var msg = $("#form-message").val();
    session.publish(clientInformation.wsConversationRoute + '/notifications', msg);
  });
  session.subscribe(clientInformation.wsConversationRoute, function (uri, payload) {
    var responsePayload = JSON.parse(payload);
    var message = responsePayload.message;
    Chat.appendMessage(responsePayload, message);
  });
  session.subscribe('online', function (uri, payload) {
    var responsePayload = JSON.parse(payload);
    $('li[data-usid]').each(function (event) {
      var userId = $(this).attr('data-usid');
      $('li[data-usid="' + userId + '"]').find('.user-details').removeClass('online');

      for (var key in responsePayload) {
        if (responsePayload.hasOwnProperty(key)) {
          if (key == userId) {
            $('li[data-usid="' + userId + '"]').find('.user-details').addClass('online');
          }
        }
      }
    });
  });
  session.subscribe(clientInformation.wsConversationRoute + '/notifications', function (uri, payload) {
    var responsePayload = JSON.parse(payload);
    var html = "\n        <div class=\"message\" data-writing=\"" + responsePayload.username + "\">\n            <img class=\"avatar-md\" src=\"/avatar.jpg\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" alt=\"avatar\" data-original-title=\"Keith\">\n            <div class=\"text-main\">\n                <div class=\"text-group\">\n                    <div class=\"text typing\">\n                        <div class=\"wave\">\n                            <span class=\"dot\"></span>\n                            <span class=\"dot\"></span>\n                            <span class=\"dot\"></span>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>";
    $('[data-writing="' + responsePayload.username + '"]').remove();

    for (var key in responsePayload) {
      if (responsePayload.hasOwnProperty(key)) {
        var who = responsePayload[key].displayName;
        var doWhat = responsePayload[key].message;
        var message = who + ' ' + doWhat;
        $('#message-zone').append(html);
        scrollToBottom(document.getElementById('content'));
      }
    }
  });
  console.log("Successfully Connected!");
});
webSocket.on("socket/disconnect", function (error) {
  console.log("Disconnected for " + error.reason + " with code " + error.code);
});
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js")))

/***/ })

},[["./assets/js/conversation.js","runtime","vendors~app~conversation","app~conversation"]]]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvY29udmVyc2F0aW9uLmpzIl0sIm5hbWVzIjpbInJlcXVpcmUiLCJob3N0Iiwid2luZG93IiwibG9jYXRpb24iLCJob3N0bmFtZSIsIndlYlNvY2tldCIsIldTIiwiY29ubmVjdCIsIm9uIiwic2Vzc2lvbiIsInNjcm9sbFRvQm90dG9tIiwiZWwiLCJzY3JvbGxUb3AiLCJzY3JvbGxIZWlnaHQiLCJkb2N1bWVudCIsImdldEVsZW1lbnRCeUlkIiwiQ2hhdCIsImFwcGVuZE1lc3NhZ2UiLCJlbnRpdHlQYXlsb2FkIiwibWVzc2FnZSIsImN1cnJlbnRkYXRlIiwiRGF0ZSIsIm1ldGFEYXRhIiwiZ2V0RGF0ZSIsImdldE1vbnRoIiwiZ2V0RnVsbFllYXIiLCJnZXRIb3VycyIsImdldE1pbnV0ZXMiLCJnZXRTZWNvbmRzIiwiZGlzcGxheU5hbWUiLCJodG1sIiwiJCIsImFwcGVuZCIsInNlbmRNZXNzYWdlIiwidGV4dCIsImNsaWVudEluZm9ybWF0aW9uIiwicHVibGlzaCIsIndzQ29udmVyc2F0aW9uUm91dGUiLCJKU09OIiwic3RyaW5naWZ5IiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsIm1zZyIsInZhbCIsImFsZXJ0Iiwic3Vic2NyaWJlIiwidXJpIiwicGF5bG9hZCIsInJlc3BvbnNlUGF5bG9hZCIsInBhcnNlIiwiZWFjaCIsInVzZXJJZCIsImF0dHIiLCJmaW5kIiwicmVtb3ZlQ2xhc3MiLCJrZXkiLCJoYXNPd25Qcm9wZXJ0eSIsImFkZENsYXNzIiwidXNlcm5hbWUiLCJyZW1vdmUiLCJ3aG8iLCJkb1doYXQiLCJjb25zb2xlIiwibG9nIiwiZXJyb3IiLCJyZWFzb24iLCJjb2RlIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7QUFBQUEsNERBQU8sQ0FBQyxvQ0FBRCxDQUFQOztBQUVBLElBQUlDLElBQUksR0FBR0MsTUFBTSxDQUFDQyxRQUFQLENBQWdCQyxRQUEzQixDLENBQ0E7O0FBRUEsSUFBSUMsU0FBUyxH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QUFYLENBQXRCO0FBRUEsUUFBSWpCLElBQUksR0FBRyxxREFDNEJrQixlQUFlLENBQUNVLFFBRDVDLCtuQkFBWDtBQWdCQTNCLEtBQUMsQ0FBQyxvQkFBb0JpQixlQUFlLENBQUNVLFFBQXBDLEdBQStDLElBQWhELENBQUQsQ0FBdURDLE1BQXZEOztBQUNBLFNBQUksSUFBSUosR0FBUixJQUFlUCxlQUFmLEVBQ0E7QUFDSSxVQUFHQSxlQUFlLENBQUNRLGNBQWhCLENBQStCRCxHQUEvQixDQUFILEVBQ0E7QUFDSSxZQUFJSyxHQUFHLEdBQUdaLGVBQWUsQ0FBQ08sR0FBRCxDQUFmLENBQXFCMUIsV0FBL0I7QUFDQSxZQUFJZ0MsTUFBTSxHQUFHYixlQUFlLENBQUNPLEdBQUQsQ0FBZixDQUFxQnBDLE9BQWxDO0FBRUEsWUFBSUEsT0FBTyxHQUFHeUMsR0FBRyxHQUFHLEdBQU4sR0FBWUMsTUFBMUI7QUFFQTlCLFNBQUMsQ0FBQyxlQUFELENBQUQsQ0FBbUJDLE1BQW5CLENBQTBCRixJQUExQjtBQUNBcEIsc0JBQWMsQ0FBQ0ksUUFBUSxDQUFDQyxjQUFULENBQXdCLFNBQXhCLENBQUQsQ0FBZDtBQUNIO0FBQ0o7QUFDSixHQWxDRDtBQW9DQStDLFNBQU8sQ0FBQ0MsR0FBUixDQUFZLHlCQUFaO0FBQ0gsQ0E1SEQ7QUE4SEExRCxTQUFTLENBQUNHLEVBQVYsQ0FBYSxtQkFBYixFQUFrQyxVQUFTd0QsS0FBVCxFQUFnQjtBQUU5Q0YsU0FBTyxDQUFDQyxHQUFSLENBQVksc0JBQXNCQyxLQUFLLENBQUNDLE1BQTVCLEdBQXFDLGFBQXJDLEdBQXFERCxLQUFLLENBQUNFLElBQXZFO0FBQ0gsQ0FIRCxFIiwiZmlsZSI6ImNvbnZlcnNhdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbInJlcXVpcmUoJy4vYXBwLmpzJyk7XG5cbnZhciBob3N0ID0gd2luZG93LmxvY2F0aW9uLmhvc3RuYW1lO1xuLy8gaG9zdCA9ICc1LjE4OS4xNjYuMTA0JztcblxudmFyIHdlYlNvY2tldCA9IFdTLmNvbm5lY3QoXCJ3czovL1wiICsgaG9zdCArIFwiOjU1MTBcIik7XG5cbndlYlNvY2tldC5vbihcInNvY2tldC9jb25uZWN0XCIsIGZ1bmN0aW9uKHNlc3Npb24pIHtcblxuICAgIGZ1bmN0aW9uIHNjcm9sbFRvQm90dG9tKGVsKSB7IGVsLnNjcm9sbFRvcCA9IGVsLnNjcm9sbEhlaWdodDsgfVxuICAgIHNjcm9sbFRvQm90dG9tKGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdjb250ZW50JykpO1xuXG4gICAgdmFyIENoYXQgPSBcbiAgICB7XG4gICAgICAgIGFwcGVuZE1lc3NhZ2U6IGZ1bmN0aW9uKGVudGl0eVBheWxvYWQsIG1lc3NhZ2UpXG4gICAgICAgIHtcbiAgICAgICAgICAgIHZhciBjdXJyZW50ZGF0ZSA9IG5ldyBEYXRlKCk7IFxuICAgICAgICAgICAgdmFyIG1ldGFEYXRhID0gY3VycmVudGRhdGUuZ2V0RGF0ZSgpICsgJy4nICsgY3VycmVudGRhdGUuZ2V0TW9udGgoKSArICcuJyArIGN1cnJlbnRkYXRlLmdldEZ1bGxZZWFyKCkgKyAnICcgKyBjdXJyZW50ZGF0ZS5nZXRIb3VycygpICsgJzonICsgY3VycmVudGRhdGUuZ2V0TWludXRlcygpICsgJzonICsgY3VycmVudGRhdGUuZ2V0U2Vjb25kcygpICsgJyAtICcgKyBlbnRpdHlQYXlsb2FkLmRpc3BsYXlOYW1lOyBcbiAgICAgICAgICAgIHZhciBodG1sID0gYFxuICAgICAgICAgICAgPGRpdiBjbGFzcz1cIm1lc3NhZ2VcIj5cbiAgICAgICAgICAgICAgICA8aW1nIGNsYXNzPVwiYXZhdGFyLW1kXCIgc3JjPVwiL2F2YXRhci5qcGdcIiBkYXRhLXRvZ2dsZT1cInRvb2x0aXBcIiBkYXRhLXBsYWNlbWVudD1cInRvcFwiIHRpdGxlPVwiXCIgYWx0PVwiYXZhdGFyXCIgZGF0YS1vcmlnaW5hbC10aXRsZT1cIktlaXRoXCI+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInRleHQtbWFpblwiPlxuICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwidGV4dC1ncm91cFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInRleHRcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cD5gICsgbWVzc2FnZSArIGA8L3A+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgIDxzcGFuPmAgKyBtZXRhRGF0YSArIGA8L3NwYW4+XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICA8L2Rpdj5gO1xuICAgICAgICAgICAgJCgnI21lc3NhZ2Utem9uZScpLmFwcGVuZChodG1sKTtcblxuICAgICAgICAgICAgc2Nyb2xsVG9Cb3R0b20oZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2NvbnRlbnQnKSk7XG4gICAgICAgIH0sXG4gICAgICAgIHNlbmRNZXNzYWdlOiBmdW5jdGlvbih0ZXh0KVxuICAgICAgICB7XG4gICAgICAgICAgICBjbGllbnRJbmZvcm1hdGlvbi5tZXNzYWdlID0gdGV4dDtcbiAgICAgICAgICAgIHNlc3Npb24ucHVibGlzaChjbGllbnRJbmZvcm1hdGlvbi53c0NvbnZlcnNhdGlvblJvdXRlLCBKU09OLnN0cmluZ2lmeShjbGllbnRJbmZvcm1hdGlvbikpO1xuXG4gICAgICAgICAgICB0aGlzLmFwcGVuZE1lc3NhZ2UoY2xpZW50SW5mb3JtYXRpb24sIHRleHQpO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgICQoZG9jdW1lbnQpLm9uKFwiY2xpY2tcIiwgXCIjc3VibWl0LW1lc3NhZ2VcIiwgZnVuY3Rpb24oZXZlbnQpIHtcbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICB2YXIgbXNnID0gJChcIiNmb3JtLW1lc3NhZ2VcIikudmFsKCk7XG4gICAgICAgIFxuICAgICAgICBpZighbXNnKSB7XG4gICAgICAgICAgICBhbGVydChcIlBsZWFzZSBzZW5kIHNvbWV0aGluZyBvbiB0aGUgY2hhdFwiKTtcbiAgICAgICAgfVxuICAgICAgICBcbiAgICAgICAgQ2hhdC5zZW5kTWVzc2FnZShtc2cpO1xuICAgICAgICAkKFwiI2Zvcm0tbWVzc2FnZVwiKS52YWwoXCJcIik7XG5cbiAgICAgICAgc2Vzc2lvbi5wdWJsaXNoKGNsaWVudEluZm9ybWF0aW9uLndzQ29udmVyc2F0aW9uUm91dGUgKyAnL25vdGlmaWNhdGlvbnMnLCAnJyk7XG4gICAgfSk7XG5cbiAgICAkKGRvY3VtZW50KS5vbihcImlucHV0XCIsIFwiI2Zvcm0tbWVzc2FnZVwiLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB2YXIgbXNnID0gJChcIiNmb3JtLW1lc3NhZ2VcIikudmFsKCk7XG4gICAgICAgIHNlc3Npb24ucHVibGlzaChjbGllbnRJbmZvcm1hdGlvbi53c0NvbnZlcnNhdGlvblJvdXRlICsgJy9ub3RpZmljYXRpb25zJywgbXNnKTtcbiAgICB9KTtcblxuICAgIHNlc3Npb24uc3Vic2NyaWJlKGNsaWVudEluZm9ybWF0aW9uLndzQ29udmVyc2F0aW9uUm91dGUsIGZ1bmN0aW9uKHVyaSwgcGF5bG9hZCkgXG4gICAge1xuICAgICAgICB2YXIgcmVzcG9uc2VQYXlsb2FkID0gSlNPTi5wYXJzZShwYXlsb2FkKTtcbiAgICAgICAgdmFyIG1lc3NhZ2UgPSByZXNwb25zZVBheWxvYWQubWVzc2FnZTtcbiAgICAgICAgQ2hhdC5hcHBlbmRNZXNzYWdlKHJlc3BvbnNlUGF5bG9hZCwgbWVzc2FnZSk7XG4gICAgfSk7XG5cbiAgICBzZXNzaW9uLnN1YnNjcmliZSgnb25saW5lJywgZnVuY3Rpb24odXJpLCBwYXlsb2FkKSBcbiAgICB7XG4gICAgICAgIHZhciByZXNwb25zZVBheWxvYWQgPSBKU09OLnBhcnNlKHBheWxvYWQpO1xuICAgICAgICBcbiAgICAgICAgJCgnbGlbZGF0YS11c2lkXScpLmVhY2goZnVuY3Rpb24oZXZlbnQpIHtcblxuICAgICAgICAgICAgdmFyIHVzZXJJZCA9ICQodGhpcykuYXR0cignZGF0YS11c2lkJyk7XG5cbiAgICAgICAgICAgICQoJ2xpW2RhdGEtdXNpZD1cIicrdXNlcklkKydcIl0nKS5maW5kKCcudXNlci1kZXRhaWxzJykucmVtb3ZlQ2xhc3MoJ29ubGluZScpO1xuXG4gICAgICAgICAgICBmb3IodmFyIGtleSBpbiByZXNwb25zZVBheWxvYWQpXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgaWYocmVzcG9uc2VQYXlsb2FkLmhhc093blByb3BlcnR5KGtleSkpXG4gICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICBpZihrZXkgPT0gdXNlcklkKVxuICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAkKCdsaVtkYXRhLXVzaWQ9XCInK3VzZXJJZCsnXCJdJykuZmluZCgnLnVzZXItZGV0YWlscycpLmFkZENsYXNzKCdvbmxpbmUnKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICBzZXNzaW9uLnN1YnNjcmliZShjbGllbnRJbmZvcm1hdGlvbi53c0NvbnZlcnNhdGlvblJvdXRlICsgJy9ub3RpZmljYXRpb25zJywgZnVuY3Rpb24odXJpLCBwYXlsb2FkKSBcbiAgICB7XG4gICAgICAgIHZhciByZXNwb25zZVBheWxvYWQgPSBKU09OLnBhcnNlKHBheWxvYWQpO1xuXG4gICAgICAgIHZhciBodG1sID0gYFxuICAgICAgICA8ZGl2IGNsYXNzPVwibWVzc2FnZVwiIGRhdGEtd3JpdGluZz1cImAgKyByZXNwb25zZVBheWxvYWQudXNlcm5hbWUgKyBgXCI+XG4gICAgICAgICAgICA8aW1nIGNsYXNzPVwiYXZhdGFyLW1kXCIgc3JjPVwiL2F2YXRhci5qcGdcIiBkYXRhLXRvZ2dsZT1cInRvb2x0aXBcIiBkYXRhLXBsYWNlbWVudD1cInRvcFwiIHRpdGxlPVwiXCIgYWx0PVwiYXZhdGFyXCIgZGF0YS1vcmlnaW5hbC10aXRsZT1cIktlaXRoXCI+XG4gICAgICAgICAgICA8ZGl2IGNsYXNzPVwidGV4dC1tYWluXCI+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInRleHQtZ3JvdXBcIj5cbiAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cInRleHQgdHlwaW5nXCI+XG4gICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwid2F2ZVwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwiZG90XCI+PC9zcGFuPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwiZG90XCI+PC9zcGFuPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwiZG90XCI+PC9zcGFuPlxuICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgIDwvZGl2PmA7XG5cbiAgICAgICAgJCgnW2RhdGEtd3JpdGluZz1cIicgKyByZXNwb25zZVBheWxvYWQudXNlcm5hbWUgKyAnXCJdJykucmVtb3ZlKCk7XG4gICAgICAgIGZvcih2YXIga2V5IGluIHJlc3BvbnNlUGF5bG9hZCkgXG4gICAgICAgIHtcbiAgICAgICAgICAgIGlmKHJlc3BvbnNlUGF5bG9hZC5oYXNPd25Qcm9wZXJ0eShrZXkpKSBcbiAgICAgICAgICAgIHtcbiAgICAgICAgICAgICAgICB2YXIgd2hvID0gcmVzcG9uc2VQYXlsb2FkW2tleV0uZGlzcGxheU5hbWU7XG4gICAgICAgICAgICAgICAgdmFyIGRvV2hhdCA9IHJlc3BvbnNlUGF5bG9hZFtrZXldLm1lc3NhZ2U7XG5cbiAgICAgICAgICAgICAgICB2YXIgbWVzc2FnZSA9IHdobyArICcgJyArIGRvV2hhdDtcblxuICAgICAgICAgICAgICAgICQoJyNtZXNzYWdlLXpvbmUnKS5hcHBlbmQoaHRtbCk7XG4gICAgICAgICAgICAgICAgc2Nyb2xsVG9Cb3R0b20oZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2NvbnRlbnQnKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0gICBcbiAgICB9KTtcblxuICAgIGNvbnNvbGUubG9nKFwiU3VjY2Vzc2Z1bGx5IENvbm5lY3RlZCFcIik7XG59KVxuXG53ZWJTb2NrZXQub24oXCJzb2NrZXQvZGlzY29ubmVjdFwiLCBmdW5jdGlvbihlcnJvcikge1xuXG4gICAgY29uc29sZS5sb2coXCJEaXNjb25uZWN0ZWQgZm9yIFwiICsgZXJyb3IucmVhc29uICsgXCIgd2l0aCBjb2RlIFwiICsgZXJyb3IuY29kZSk7XG59KSJdLCJzb3VyY2VSb290IjoiIn0=