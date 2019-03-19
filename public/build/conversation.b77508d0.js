(window.webpackJsonp=window.webpackJsonp||[]).push([["conversation"],{"0m6P":function(t,e,i){(function(i){var n,s,a;s=[],void 0===(a="function"==typeof(n=function(){"use strict";function t(t,e){if(!t)return console.warn("Tagify: ","invalid input element ",t),this;this.applySettings(t,e),this.state={},this.value=[],this.listeners={},this.DOM={},this.extend(this,new this.EventDispatcher(this)),this.build(t),this.loadOriginalValues(),this.events.customBinding.call(this),this.events.binding.call(this),t.autofocus&&this.DOM.input.focus()}return t.prototype={isIE:window.document.documentMode,TEXTS:{empty:"empty",exceed:"number of tags exceeded",pattern:"pattern mismatch",duplicate:"already exists",notAllowed:"not allowed"},DEFAULTS:{delimiters:",",pattern:null,maxTags:1/0,callbacks:{},addTagOnBlur:!0,duplicates:!1,whitelist:[],blacklist:[],enforceWhitelist:!1,keepInvalidTags:!1,autoComplete:!0,mixTagsAllowedAfter:/,|\.|\:|\s/,backspace:!0,dropdown:{classname:"",enabled:2,maxItems:10,itemTemplate:"",fuzzySearch:!1}},customEventsList:["click","add","remove","invalid","input","edit"],applySettings:function(t,e){var i=t.getAttribute("data-whitelist"),n=t.getAttribute("data-blacklist");if(this.settings=this.extend({},this.DEFAULTS,e),this.settings.readonly=t.hasAttribute("readonly"),this.isIE&&(this.settings.autoComplete=!1),n&&(n=n.split(this.settings.delimiters))instanceof Array&&(this.settings.blacklist=n),i&&(i=i.split(this.settings.delimiters))instanceof Array&&(this.settings.whitelist=i),t.pattern)try{this.settings.pattern=new RegExp(t.pattern)}catch(t){}if(this.settings&&this.settings.delimiters)try{this.settings.delimiters=new RegExp("["+this.settings.delimiters+"]","g")}catch(t){}},parseHTML:function(t){return(new DOMParser).parseFromString(t.trim(),"text/html").body.firstElementChild},escapeHtml:function(t){var e=document.createTextNode(t),i=document.createElement("p");return i.appendChild(e),i.innerHTML},build:function(t){var e=this.DOM,i='<tags class="tagify '+(this.settings.mode?"tagify--mix":"")+" "+t.className+'" '+(this.settings.readonly?"readonly":"")+'>\n                            <span contenteditable data-placeholder="'+(t.placeholder||"&#8203;")+'" class="tagify__input"></span>\n                        </tags>';e.originalInput=t,e.scope=this.parseHTML(i),e.input=e.scope.querySelector("[contenteditable]"),t.parentNode.insertBefore(e.scope,t),0<=this.settings.dropdown.enabled&&this.dropdown.init.call(this)},destroy:function(){this.DOM.scope.parentNode.removeChild(this.DOM.scope)},loadOriginalValues:function(){var t=this.DOM.originalInput.value;if(t){try{t=JSON.parse(t)}catch(t){}"mix"==this.settings.mode?this.parseMixTags(t):this.addTags(t).forEach(function(t){t&&t.classList.add("tagify--noAnim")})}},extend:function(t,e,i){function n(t){var e=Object.prototype.toString.call(t).split(" ")[1].slice(0,-1);return t===Object(t)&&"Array"!=e&&"Function"!=e&&"RegExp"!=e&&"HTMLUnknownElement"!=e}function s(t,e){for(var i in e)e.hasOwnProperty(i)&&(n(e[i])?n(t[i])?s(t[i],e[i]):t[i]=Object.assign({},e[i]):t[i]=e[i])}return t instanceof Object||(t={}),s(t,e),i&&s(t,i),t},EventDispatcher:function(t){var e=document.createTextNode("");this.off=function(t,i){return i&&e.removeEventListener.call(e,t,i),this},this.on=function(t,i){return i&&e.addEventListener.call(e,t,i),this},this.trigger=function(n,s){var a;if(n)if(t.settings.isJQueryPlugin)i(t.DOM.originalInput).triggerHandler(n,[s]);else{try{a=new CustomEvent(n,{detail:s})}catch(n){console.warn(n)}e.dispatchEvent(a)}}},events:{customBinding:function(){var t=this;this.customEventsList.forEach(function(e){t.on(e,t.settings.callbacks[e])})},binding:function(){var t,e=!(0<arguments.length&&void 0!==arguments[0])||arguments[0],n=this.events.callbacks,s=e?"addEventListener":"removeEventListener";for(var a in e&&!this.listeners.main&&(this.DOM.input.addEventListener(this.isIE?"keydown":"input",n[this.isIE?"onInputIE":"onInput"].bind(this)),this.settings.isJQueryPlugin&&i(this.DOM.originalInput).on("tagify.removeAllTags",this.removeAllTags.bind(this))),t=this.listeners.main=this.listeners.main||{paste:["input",n.onPaste.bind(this)],focus:["input",n.onFocusBlur.bind(this)],blur:["input",n.onFocusBlur.bind(this)],keydown:["input",n.onKeydown.bind(this)],click:["scope",n.onClickScope.bind(this)],dblclick:["scope",n.onDoubleClickScope.bind(this)]})this.DOM[t[a][0]][s](a,t[a][1])},callbacks:{onFocusBlur:function(t){var e=t.target.textContent.trim();"mix"!=this.settings.mode&&("focus"==t.type?(this.DOM.scope.classList.add("tagify--focus"),0===this.settings.dropdown.enabled&&this.dropdown.show.call(this)):"blur"==t.type?(this.DOM.scope.classList.remove("tagify--focus"),e&&this.settings.addTagOnBlur&&this.addTags(e,!0).length):(this.DOM.input.removeAttribute("style"),this.dropdown.hide.call(this)))},onKeydown:function(t){var e,i,n=this,s=t.target.textContent;if("mix"==this.settings.mode){switch(t.key){case"Backspace":var a=[];i=this.DOM.input.children,setTimeout(function(){[].forEach.call(i,function(t){return a.push(t.getAttribute("value"))}),n.value=n.value.filter(function(t){return-1!=a.indexOf(t.value)})},20);break;case"Enter":t.preventDefault()}return!0}switch(t.key){case"Backspace":""!=s&&8203!=s.charCodeAt(0)||(e=(e=this.DOM.scope.querySelectorAll("tag:not(.tagify--hide):not([readonly])"))[e.length-1],!0===this.settings.backspace?this.removeTag(e):"edit"==this.settings.backspace&&this.editTag(e));break;case"Esc":case"Escape":this.input.set.call(this),t.target.blur();break;case"ArrowRight":case"Tab":if(!s)return!0;case"Enter":t.preventDefault(),this.addTags(this.input.value,!0)}},onInput:function(t){var e=this.input.normalize.call(this),i=e.length>=this.settings.dropdown.enabled;if("mix"==this.settings.mode)return this.events.callbacks.onMixTagsInput.call(this,t);e?this.input.value!=e&&(this.input.set.call(this,e,!1),this.trigger("input",e),-1!=e.search(this.settings.delimiters)?this.addTags(e).length&&this.input.set.call(this):0<=this.settings.dropdown.enabled&&this.dropdown[i?"show":"hide"].call(this,e)):this.input.set.call(this,"")},onMixTagsInput:function(t){var e,i,n,s,a;if(this.maxTagsReached())return!0;window.getSelection&&0<(e=window.getSelection()).rangeCount&&((i=e.getRangeAt(0).cloneRange()).collapse(!0),i.setStart(window.getSelection().focusNode,0),(s=(n=i.toString().split(this.settings.mixTagsAllowedAfter))[n.length-1].match(this.settings.pattern))&&(this.state.tag={prefix:s[0],value:s.input.split(s[0])[1]},s=this.state.tag,a=this.state.tag.value.length>=this.settings.dropdown.enabled)),this.update(),this.trigger("input",this.extend({},this.state.tag,{textContent:this.DOM.input.textContent})),this.state.tag&&this.dropdown[a?"show":"hide"].call(this,this.state.tag.value)},onInputIE:function(t){var e=this;setTimeout(function(){e.events.callbacks.onInput.call(e,t)})},onPaste:function(t){},onClickScope:function(t){var e,i=t.target.closest("tag");"TAGS"==t.target.tagName?this.DOM.input.focus():"X"==t.target.tagName?this.removeTag(t.target.parentNode):i&&(e=this.getNodeIndex(i),this.trigger("click",{tag:i,index:e,data:this.value[e]}))},onEditTagInput:function(t){var e=t.closest("tag"),i=this.getNodeIndex(e),n=this.input.normalize(t),s=n==t.originalValue||this.validateTag(n);e.classList.toggle("tagify--invalid",!0!==s),e.isValid=s,this.trigger("input",{tag:e,index:i,data:this.extend({},this.value[i],{newValue:n})})},onEditTagBlur:function(t){var e,i=t.closest("tag"),n=this.getNodeIndex(i),s=this.input.normalize(t)||t.originalValue,a=i.isValid;void 0!==a&&!0!==a||(t.textContent=s,this.value[n].value=s,this.update(),(e=t.cloneNode(!0)).removeAttribute("contenteditable"),i.title=s,i.classList.remove("tagify--editable"),t.parentNode.replaceChild(e,t),this.trigger("edit",{tag:i,index:n,data:this.value[n]}))},onEditTagkeydown:function(t){switch(t.key){case"Esc":case"Escape":t.target.textContent=t.target.originalValue;case"Enter":case"Tab":t.preventDefault(),t.target.blur()}},onDoubleClickScope:function(t){var e=t.target.closest("tag"),i=this.settings;"mix"==i.mode||i.readonly||i.enforceWhitelist||!e||e.classList.contains("tagify--editable")||e.hasAttribute("readonly")||this.editTag(e)}}},editTag:function(t){var e=this,i=t.querySelector(".tagify__tag-text"),n=this.events.callbacks;i?(t.classList.add("tagify--editable"),i.originalValue=i.textContent,i.setAttribute("contenteditable",!0),i.addEventListener("blur",n.onEditTagBlur.bind(this,i)),i.addEventListener("input",n.onEditTagInput.bind(this,i)),i.addEventListener("keydown",function(t){return n.onEditTagkeydown.call(e,t)}),i.focus()):console.warn("Cannot find element in Tag template: ",".tagify__tag-text")},input:{value:"",set:function(){var t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"",e=!(1<arguments.length&&void 0!==arguments[1])||arguments[1];this.input.value=t,e&&(this.DOM.input.innerHTML=t),t||this.dropdown.hide.call(this),t.length<2&&this.input.autocomplete.suggest.call(this,""),this.input.validate.call(this)},setRangeAtStartEnd:function(){var t,e,i=0<arguments.length&&void 0!==arguments[0]&&arguments[0],n=arguments[1];document.createRange&&((t=document.createRange()).selectNodeContents(n||this.DOM.input),t.collapse(i),(e=window.getSelection()).removeAllRanges(),e.addRange(t))},validate:function(){var t=!this.input.value||this.validateTag.call(this,this.input.value);this.DOM.input.classList.toggle("tagify__input--invalid",!0!==t)},normalize:function(){var t=(0<arguments.length&&void 0!==arguments[0]?arguments[0]:this.DOM.input).innerText;return"settings"in this&&(t=t.replace(/(?:\r\n|\r|\n)/g,this.settings.delimiters.source.charAt(1))),t=t.replace(/\s/g," ").replace(/^\s+/,"")},autocomplete:{suggest:function(t){t&&this.input.value?this.DOM.input.setAttribute("data-suggest",t.substring(this.input.value.length)):this.DOM.input.removeAttribute("data-suggest")},set:function(t){var e=this.DOM.input.getAttribute("data-suggest"),i=t||(e?this.input.value+e:null);return!!i&&(this.input.set.call(this,i),this.input.autocomplete.suggest.call(this,""),this.dropdown.hide.call(this),this.input.setRangeAtStartEnd.call(this),!0)}}},getNodeIndex:function(t){var e=0;if(t)for(;t=t.previousElementSibling;)e++;return e},isTagDuplicate:function(t){return this.value.findIndex(function(e){return t.trim().toLowerCase()===e.value.toLowerCase()})},getTagIndexByValue:function(t){var e=[];return this.DOM.scope.querySelectorAll("tag").forEach(function(i,n){i.textContent.trim().toLowerCase()==t.toLowerCase()&&e.push(n)}),e},getTagElmByValue:function(t){var e=this.getTagIndexByValue(t)[0];return this.DOM.scope.querySelectorAll("tag")[e]},markTagByValue:function(t,e){return!!(e=e||this.getTagElmByValue(t))&&(e.classList.add("tagify--mark"),e)},isTagBlacklisted:function(t){return t=t.toLowerCase().trim(),this.settings.blacklist.filter(function(e){return t==e.toLowerCase()}).length},isTagWhitelisted:function(t){return this.settings.whitelist.some(function(e){if((e.value||e).toLowerCase()===t.toLowerCase())return!0})},validateTag:function(t){var e=t.trim(),i=!0;return e?this.settings.pattern&&!this.settings.pattern.test(e)?i=this.TEXTS.pattern:this.settings.duplicates||-1===this.isTagDuplicate(e)?(this.isTagBlacklisted(e)||this.settings.enforceWhitelist&&!this.isTagWhitelisted(e))&&(i=this.TEXTS.notAllowed):i=this.TEXTS.duplicate:i=this.TEXTS.empty,i},maxTagsReached:function(){return this.value.length>=this.settings.maxTags&&this.TEXTS.exceed},normalizeTags:function(t){var e=this,i=this.settings.whitelist[0]instanceof Object,n=t instanceof Array&&t[0]instanceof Object&&"value"in t[0],s=[];if(n)return t;if("number"==typeof t&&(t=t.toString()),"string"==typeof t){if(!t.trim())return[];t=t.split(this.settings.delimiters).filter(function(t){return t}).map(function(t){return{value:t.trim()}})}else t instanceof Array&&(t=t.map(function(t){return{value:t.trim()}}));return i?(t.forEach(function(t){var i=e.settings.whitelist.filter(function(e){return e.value.toLowerCase()==t.value.toLowerCase()});i[0]?s.push(i[0]):"mix"!=e.settings.mode&&s.push(t)}),s):t},parseMixTags:function(t){var e=this;return t.split(this.settings.mixTagsAllowedAfter).filter(function(t){return t.match(e.settings.pattern)}).forEach(function(i){var n,s=i.replace(e.settings.pattern,"");e.isTagWhitelisted(s)&&!e.settings.duplicates&&-1==e.isTagDuplicate(s)&&(n=e.normalizeTags.call(e,s)[0],t=e.replaceMixStringWithTag(t,i,n).s)}),this.DOM.input.innerHTML=t,this.update(),t},replaceMixStringWithTag:function(t,e,i,n){return i&&t&&-1!=t.indexOf(e)&&(n=this.createTagElem(i),this.value.push(i),t=t.replace(e,n.outerHTML+"&#8288;")),{s:t,tagElm:n}},addMixTag:function(t){if(t&&this.state.tag){for(var e,i,n,s,a=this.state.tag.prefix+this.state.tag.value,o=document.createNodeIterator(this.DOM.input,NodeFilter.SHOW_TEXT),r=100;(e=o.nextNode())&&r--;)if(e.nodeType===Node.TEXT_NODE){if(-1==(n=e.nodeValue.indexOf(a)))continue;s=e.splitText(n),i=this.createTagElem(t),s.nodeValue=s.nodeValue.replace(a,""),e.parentNode.insertBefore(i,s),i.insertAdjacentHTML("afterend","&#8288;")}i&&(this.value.push(t),this.update(),this.trigger("add",this.extend({},{index:this.value.length,tag:i},t))),this.state.tag=null}},addTags:function(t,e){var i=this,n=[];return t=this.normalizeTags.call(this,t),"mix"==this.settings.mode?this.addMixTag(t[0]):(this.DOM.input.removeAttribute("style"),t.forEach(function(t){var e,s;t=Object.assign({},t),"function"==typeof i.settings.transformTag&&(t.value=i.settings.transformTag.call(i,t.value)||t.value),!0!==(e=i.maxTagsReached()||i.validateTag.call(i,t.value))&&(t.class=(t.class||"")+" tagify--notAllowed",t.title=e,i.markTagByValue(t.value),i.trigger("invalid",{data:t,index:i.value.length,message:e})),s=i.createTagElem(t),n.push(s),function(t){var e=this.DOM.scope.lastElementChild;e===this.DOM.input?this.DOM.scope.insertBefore(t,e):this.DOM.scope.appendChild(t)}.call(i,s),!0===e?(i.value.push(t),i.update(),i.DOM.scope.classList.toggle("hasMaxTags",i.value.length>=i.settings.maxTags),i.trigger("add",{tag:s,index:i.value.length-1,data:t})):i.settings.keepInvalidTags||setTimeout(function(){i.removeTag(s,!0)},1e3)}),t.length&&e&&this.input.set.call(this),n)},minify:function(t){return t.replace(new RegExp(">[\r\n ]+<","g"),"><")},createTagElem:function(t){var e,i=this.escapeHtml(t.value),n="<tag title='"+i+"' contenteditable='false' spellcheck=\"false\">\n                            <x title=''></x><div><span class='tagify__tag-text'>"+i+"</span></div>\n                        </tag>";if("function"==typeof this.settings.tagTemplate)try{n=this.settings.tagTemplate(i,t)}catch(t){}return this.settings.readonly&&(t.readonly=!0),n=this.minify(n),function(t,e){var i,n=Object.keys(e);for(i=n.length;i--;){var s=n[i];if(!e.hasOwnProperty(s))return;t.setAttribute(s,e[s])}}(e=this.parseHTML(n),t),e},removeTag:function(t,e){var i=2<arguments.length&&void 0!==arguments[2]?arguments[2]:250;if(t&&t instanceof HTMLElement){"string"==typeof t&&(t=this.getTagElmByValue(t));var n,s=this.getNodeIndex(t);i&&10<i?(t.style.width=parseFloat(window.getComputedStyle(t).width)+"px",document.body.clientTop,t.classList.add("tagify--hide"),setTimeout(a,400)):a(),e||(n=this.value.splice(s,1)[0],this.update(),this.trigger("remove",{tag:t,index:s,data:n}))}function a(){t.parentNode&&t.parentNode.removeChild(t)}},removeAllTags:function(){this.value=[],this.update(),Array.prototype.slice.call(this.DOM.scope.querySelectorAll("tag")).forEach(function(t){return t.parentNode.removeChild(t)})},update:function(){this.DOM.originalInput.value="mix"==this.settings.mode?this.DOM.input.textContent:JSON.stringify(this.value)},dropdown:{init:function(){this.DOM.dropdown=this.dropdown.build.call(this)},build:function(){var t='<div class="'+("tagify__dropdown "+this.settings.dropdown.classname).trim()+'"></div>';return this.parseHTML(t)},show:function(t){var e,i=this;if(this.settings.whitelist.length){if(this.suggestedListItems=t?this.dropdown.filterListItems.call(this,t):this.settings.whitelist.filter(function(t){return-1==i.isTagDuplicate(t.value||t)}),!this.suggestedListItems.length)return this.input.autocomplete.suggest.call(this),void this.dropdown.hide.call(this);e=this.dropdown.createListHTML.call(this,this.suggestedListItems),this.DOM.dropdown.innerHTML=e,this.dropdown.highlightOption.call(this,this.DOM.dropdown.querySelector(".tagify__dropdown__item")),this.dropdown.position.call(this),!this.DOM.dropdown.parentNode!=document.body&&(document.body.appendChild(this.DOM.dropdown),this.events.binding.call(this,!1),this.dropdown.events.binding.call(this))}},hide:function(){this.DOM.dropdown&&this.DOM.dropdown.parentNode==document.body&&(document.body.removeChild(this.DOM.dropdown),window.removeEventListener("resize",this.dropdown.position),this.dropdown.events.binding.call(this,!1),this.events.binding.call(this))},position:function(){var t=this.DOM.scope.getBoundingClientRect();this.DOM.dropdown.style.cssText="left: "+(t.left+window.pageXOffset)+"px;                                                top: "+(t.top+t.height-1+window.pageYOffset)+"px;                                                width: "+t.width+"px"},events:{binding:function(){var t=!(0<arguments.length&&void 0!==arguments[0])||arguments[0],e=this.listeners.dropdown=this.listeners.dropdown||{position:this.dropdown.position.bind(this),onKeyDown:this.dropdown.events.callbacks.onKeyDown.bind(this),onMouseOver:this.dropdown.events.callbacks.onMouseOver.bind(this),onClick:this.dropdown.events.callbacks.onClick.bind(this)},i=t?"addEventListener":"removeEventListener";window[i]("resize",e.position),window[i]("keydown",e.onKeyDown),window[i]("mousedown",e.onClick),this.DOM.dropdown[i]("mouseover",e.onMouseOver)},callbacks:{onKeyDown:function(t){var e=this.DOM.dropdown.querySelector("[class$='--active']")||this.DOM.dropdown.children[0],i="";switch(t.key){case"ArrowDown":case"ArrowUp":case"Down":case"Up":t.preventDefault(),e&&(e=e[("ArrowUp"==t.key||"Up"==t.key?"previous":"next")+"ElementSibling"]),e||(e=this.DOM.dropdown.children["ArrowUp"==t.key||"Up"==t.key?this.DOM.dropdown.children.length-1:0]),this.dropdown.highlightOption.call(this,e,!0);break;case"Escape":case"Esc":this.dropdown.hide.call(this);break;case"ArrowRight":case"Tab":if(t.preventDefault(),!this.input.autocomplete.set.call(this,e?e.textContent:null))return!1;case"Enter":return t.preventDefault(),i=this.suggestedListItems[this.getNodeIndex(e)]||this.input.value,this.addTags([i],!0),this.dropdown.hide.call(this),!1}},onMouseOver:function(t){t.target.className.includes("__item")&&this.dropdown.highlightOption.call(this,t.target)},onClick:function(t){var e,i,n=this,s=function(){return n.dropdown.hide.call(n)};if(0==t.button){if(t.target==document.documentElement)return s();(i=[t.target,t.target.parentNode].filter(function(t){return t.className.includes("tagify__dropdown__item")})[0])?(e=this.suggestedListItems[this.getNodeIndex(i)]||this.input.value,this.addTags([e],!0),this.dropdown.hide.call(this)):s()}}}},highlightOption:function(t,e){if(t){var i,n="tagify__dropdown__item--active";[].forEach.call(this.DOM.dropdown.querySelectorAll("[class$='--active']"),function(t){return t.classList.remove(n)}),t.classList.add(n),e&&(t.parentNode.scrollTop=t.clientHeight+t.offsetTop-t.parentNode.clientHeight),this.settings.autoComplete&&!this.settings.dropdown.fuzzySearch&&(i=this.suggestedListItems[this.getNodeIndex(t)].value||this.input.value,this.input.autocomplete.suggest.call(this,i))}},filterListItems:function(t){if(!t)return"";for(var e,i,n,s,a=[],o=this.settings.whitelist,r=this.settings.dropdown.maxItems||1/0,l=0;l<o.length&&(n=(e=o[l]instanceof Object?o[l]:{value:o[l]}).value.toLowerCase().indexOf(t.toLowerCase()),i=this.settings.dropdown.fuzzySearch?0<=n:0==n,s=!this.settings.duplicates&&-1<this.isTagDuplicate(e.value),i&&!s&&r--&&a.push(e),0!=r);l++);return a},createListHTML:function(t){var e=this.settings.dropdown.itemTemplate||function(t){return"<div class='tagify__dropdown__item "+(t.class?t.class:"")+"' "+function(t){var e,i=Object.keys(t),n="";for(e=i.length;e--;){var s=i[e];if("class"!=s&&!t.hasOwnProperty(s))return;n+=" "+s+(t[s]?"="+t[s]:"")}return n}(t)+">"+(t.value||t)+"</div>"};return t.map(e).join("")}}},t})?n.apply(e,s):n)||(t.exports=a)}).call(this,i("EVdn"))},k6dk:function(t,e,i){"use strict";i.r(e),function(t){i("0m6P");i("ng4s");var e=window.location.hostname,n=WS.connect("ws://"+e+":5510");n.on("socket/connect",function(e){function i(t){t.scrollTop=t.scrollHeight}i(document.getElementById("content"));var n={appendMessage:function(e){t("#org-msg-zone").append(e),i(document.getElementById("content"))},appendMessageChunk:function(e){console.log("CHUNK #1"),t("#org-msg-zone .message:last").find(".text").append(e),i(document.getElementById("content"))}};function s(s){e.subscribe(s,function(e,i){t("#content").hasClass("empty")&&(t("#content").removeClass("empty"),t("#content .col-md-12").attr("id","org-msg-zone"),t("#content .no-messages").remove()),"msg_block"==i.msg.msgType?n.appendMessage(i.msg.template):n.appendMessageChunk(i.msg.template)}),e.subscribe(s+"/notifications",function(e,n){console.log(n);var s=JSON.parse(n),a='\n            <div class="message" data-writing="'+clientInformation.username+'">\n                <img class="avatar-md" src="/avatar.jpg" data-toggle="tooltip" data-placement="top" title="" alt="avatar" data-original-title="Keith">\n                <div class="text-main">\n                    <div class="text-group">\n                        <div class="text typing">\n                            <div class="wave">\n                                <span class="dot"></span>\n                                <span class="dot"></span>\n                                <span class="dot"></span>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n            </div>';for(var o in t('[data-writing="'+clientInformation.username+'"]').remove(),s)s.hasOwnProperty(o)&&!t('[data-writing="'+clientInformation.username+'"]').length&&clientInformation.username!=o&&(t("#writing-notif-zone").append(a),i(document.getElementById("content")))})}t(document).on("click","#submit-message",function(i){i.preventDefault();var n=t("#form-message").val();n&&t.ajax({url:"/message/"+clientInformation.conversationId+"/new",type:"POST",data:{message:n},complete:function(t){}}),t("#form-message").val("").change(),t("#form-message").html(""),clientInformation.isWriting=!1,e.publish(clientInformation.wsConversationRoute+"/notifications",clientInformation.isWriting)}),document.getElementById("form-message").focus(),t(document).on("keypress","#form-message",function(e){13===e.keyCode&&(e.preventDefault(),t("#submit-message").trigger("click"),t("#form-message").val(""))}),t(document).on("input","#form-message",function(i){i.preventDefault();var n=t("#form-message").val();n.length>0&&0==clientInformation.isWriting?(clientInformation.isWriting=!0,e.publish(clientInformation.wsConversationRoute+"/notifications",clientInformation.isWriting)):0==n.length&&1==clientInformation.isWriting&&(clientInformation.isWriting=!1,e.publish(clientInformation.wsConversationRoute+"/notifications",clientInformation.isWriting))}),s(clientInformation.wsConversationRoute),t(document).on("click",".discussions li",function(n){n.preventDefault();var a=t(this).attr("data-cid");if(a!=clientInformation.conversationId){var o={foo:"bar"};t(".discussions li").removeClass("active"),t(this).addClass("active"),t("#content").hide(),t("#loading").show(),t.ajax({url:"/message/"+a+"/section",type:"GET",success:function(n){t("#msg-section").remove(),t(n).insertAfter("#sidebar"),window.history.pushState(o,"Conversation","/conversation/"+a),e.unsubscribe(clientInformation.wsConversationRoute),e.unsubscribe(clientInformation.wsConversationRoute+"/notifications"),t("#writing-notif-zone").html(""),clientInformation.wsConversationRoute="conversation/"+a,clientInformation.conversationId=a,s(clientInformation.wsConversationRoute),e.publish("unreaded/"+clientInformation.username,a),t("#loading").hide(),t("#content").css({display:"flex"}),t('[data-toggle="tooltip"]').tooltip(),i(document.getElementById("content"))}})}}),e.subscribe("online",function(e,i){var n=JSON.parse(i);t("li[data-usid]").each(function(e){var i=t(this).attr("data-usid");for(var s in t('li[data-usid="'+i+'"]').find(".user-details").removeClass("online"),n)n.hasOwnProperty(s)&&s==i&&t('li[data-usid="'+i+'"]').find(".user-details").addClass("online")})}),e.subscribe("unreaded/"+clientInformation.username,function(e,i){console.log("unreaded: ",i);var n=JSON.parse(i);for(var s in n)if(n.hasOwnProperty(s)){var a="";n[s]>0&&(a=n[s]),t("li[data-cid='"+s+"'] .user-nickname span:last").html(a)}}),console.log("Successfully Connected!")}),n.on("socket/disconnect",function(t){console.log("Disconnected for "+t.reason+" with code "+t.code),location.reload()})}.call(this,i("EVdn"))}},[["k6dk","runtime",1,0]]]);