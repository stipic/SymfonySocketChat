/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

var webSocket = WS.connect("ws://127.0.0.1:8080");

webSocket.on("socket/connect", function (session) {
    //session is an Autobahn JS WAMP session.

    //the callback function in "subscribe" is called everytime an event is published in that channel.
    session.subscribe("conversation/1", function (uri, payload) {
        console.log("Received message", payload.msg);
    });


    session.publish("conversation/1", "This is a message!");

    console.log("Successfully Connected!");
})

webSocket.on("socket/disconnect", function (error) {
    //error provides us with some insight into the disconnection: error.reason and error.code

    console.log("Disconnected for " + error.reason + " with code " + error.code);
})