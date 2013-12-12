// In case we forget to take out console statements. IE becomes very unhappy when we forget. Let's not make IE unhappy
try {
    console.log('> Console loaded');
} catch(err){
    var console = {};
    console.log = console.error = console.info = console.warn = function() {};
}

// jQuery Tools v1.2.5 - The missing UI library for the Web - Tooltips & Tabs
(function(a){a.tools=a.tools||{version:"v1.2.5"},a.tools.tabs={conf:{tabs:"a",current:"current",onBeforeClick:null,onClick:null,effect:"default",initialIndex:0,event:"click",rotate:!1,history:!1},addEffect:function(a,c){b[a]=c}};var b={"default":function(a,b){this.getPanes().hide().eq(a).show(),b.call()},fade:function(a,b){var c=this.getConf(),d=c.fadeOutSpeed,e=this.getPanes();d?e.fadeOut(d):e.hide(),e.eq(a).fadeIn(c.fadeInSpeed,b)},slide:function(a,b){this.getPanes().slideUp(200),this.getPanes().eq(a).slideDown(400,b)},ajax:function(a,b){this.getPanes().eq(0).load(this.getTabs().eq(a).attr("href"),b)}},c;a.tools.tabs.addEffect("horizontal",function(b,d){c||(c=this.getPanes().eq(0).width()),this.getCurrentPane().animate({width:0},function(){a(this).hide()}),this.getPanes().eq(b).animate({width:c},function(){a(this).show(),d.call()})});function d(c,d,e){var f=this,g=c.add(this),h=c.find(e.tabs),i=d.jquery?d:c.children(d),j;h.length||(h=c.children()),i.length||(i=c.parent().find(d)),i.length||(i=a(d)),a.extend(this,{click:function(c,d){var i=h.eq(c);typeof c=="string"&&c.replace("#","")&&(i=h.filter("[href*="+c.replace("#","")+"]"),c=Math.max(h.index(i),0));if(e.rotate){var k=h.length-1;if(c<0)return f.click(k,d);if(c>k)return f.click(0,d)}if(!i.length){if(j>=0)return f;c=e.initialIndex,i=h.eq(c)}if(c===j)return f;d=d||a.Event(),d.type="onBeforeClick",g.trigger(d,[c]);if(!d.isDefaultPrevented()){b[e.effect].call(f,c,function(){d.type="onClick",g.trigger(d,[c])}),j=c,h.removeClass(e.current),i.addClass(e.current);return f}},getConf:function(){return e},getTabs:function(){return h},getPanes:function(){return i},getCurrentPane:function(){return i.eq(j)},getCurrentTab:function(){return h.eq(j)},getIndex:function(){return j},next:function(){return f.click(j+1)},prev:function(){return f.click(j-1)},destroy:function(){h.unbind(e.event).removeClass(e.current),i.find("a[href^=#]").unbind("click.T");return f}}),a.each("onBeforeClick,onClick".split(","),function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}}),e.history&&a.fn.history&&(a.tools.history.init(h),e.event="history"),h.each(function(b){a(this).bind(e.event,function(a){f.click(b,a);return a.preventDefault()})}),i.find("a[href^=#]").bind("click.T",function(b){f.click(a(this).attr("href"),b)}),location.hash&&e.tabs=="a"&&c.find("[href="+location.hash+"]").length?f.click(location.hash):(e.initialIndex===0||e.initialIndex>0)&&f.click(e.initialIndex)}a.fn.tabs=function(b,c){var e=this.data("tabs");e&&(e.destroy(),this.removeData("tabs")),a.isFunction(c)&&(c={onBeforeClick:c}),c=a.extend({},a.tools.tabs.conf,c),this.each(function(){e=new d(a(this),b,c),a(this).data("tabs",e)});return c.api?e:this}})(jQuery);
(function(a){a.tools=a.tools||{version:"v1.2.5"},a.tools.tooltip={conf:{effect:"toggle",fadeOutSpeed:"fast",predelay:0,delay:30,opacity:1,tip:0,position:["top","center"],offset:[0,0],relative:!1,cancelDefault:!0,events:{def:"mouseenter,mouseleave",input:"focus,blur",widget:"focus mouseenter,blur mouseleave",tooltip:"mouseenter,mouseleave"},layout:"<div/>",tipClass:"tooltip"},addEffect:function(a,c,d){b[a]=[c,d]}};var b={toggle:[function(a){var b=this.getConf(),c=this.getTip(),d=b.opacity;d<1&&c.css({opacity:d}),c.show(),a.call()},function(a){this.getTip().hide(),a.call()}],fade:[function(a){var b=this.getConf();this.getTip().fadeTo(b.fadeInSpeed,b.opacity,a)},function(a){this.getTip().fadeOut(this.getConf().fadeOutSpeed,a)}]};function c(b,c,d){var e=d.relative?b.position().top:b.offset().top,f=d.relative?b.position().left:b.offset().left,g=d.position[0];e-=c.outerHeight()-d.offset[0],f+=b.outerWidth()+d.offset[1],/iPad/i.test(navigator.userAgent)&&(e-=a(window).scrollTop());var h=c.outerHeight()+b.outerHeight();g=="center"&&(e+=h/2),g=="bottom"&&(e+=h),g=d.position[1];var i=c.outerWidth()+b.outerWidth();g=="center"&&(f-=i/2),g=="left"&&(f-=i);return{top:e,left:f}}function d(d,e){var f=this,g=d.add(f),h,i=0,j=0,k=d.attr("title"),l=d.attr("data-tooltip"),m=b[e.effect],n,o=d.is(":input"),p=o&&d.is(":checkbox, :radio, select, :button, :submit"),q=d.attr("type"),r=e.events[q]||e.events[o?p?"widget":"input":"def"];if(!m)throw"Nonexistent effect \""+e.effect+"\"";r=r.split(/,\s*/);if(r.length!=2)throw"Tooltip: bad events configuration for "+q;d.bind(r[0],function(a){clearTimeout(i),e.predelay?j=setTimeout(function(){f.show(a)},e.predelay):f.show(a)}).bind(r[1],function(a){clearTimeout(j),e.delay?i=setTimeout(function(){f.hide(a)},e.delay):f.hide(a)}),k&&e.cancelDefault&&(d.removeAttr("title"),d.data("title",k)),a.extend(f,{show:function(b){if(!h){l?h=a(l):e.tip?h=a(e.tip).eq(0):k?h=a(e.layout).addClass(e.tipClass).appendTo(document.body).hide().append(k):(h=d.next(),h.length||(h=d.parent().next()));if(!h.length)throw"Cannot find tooltip for "+d}if(f.isShown())return f;h.stop(!0,!0);var o=c(d,h,e);e.tip&&h.html(d.data("title")),b=a.Event(),b.type="onBeforeShow",g.trigger(b,[o]);if(b.isDefaultPrevented())return f;o=c(d,h,e),h.css({position:"absolute",top:o.top,left:o.left}),n=!0,m[0].call(f,function(){b.type="onShow",n="full",g.trigger(b)});var p=e.events.tooltip.split(/,\s*/);h.data("__set")||(h.bind(p[0],function(){clearTimeout(i),clearTimeout(j)}),p[1]&&!d.is("input:not(:checkbox, :radio), textarea")&&h.bind(p[1],function(a){a.relatedTarget!=d[0]&&d.trigger(r[1].split(" ")[0])}),h.data("__set",!0));return f},hide:function(c){if(!h||!f.isShown())return f;c=a.Event(),c.type="onBeforeHide",g.trigger(c);if(!c.isDefaultPrevented()){n=!1,b[e.effect][1].call(f,function(){c.type="onHide",g.trigger(c)});return f}},isShown:function(a){return a?n=="full":n},getConf:function(){return e},getTip:function(){return h},getTrigger:function(){return d}}),a.each("onHide,onBeforeShow,onShow,onBeforeHide".split(","),function(b,c){a.isFunction(e[c])&&a(f).bind(c,e[c]),f[c]=function(b){b&&a(f).bind(c,b);return f}})}a.fn.tooltip=function(b){var c=this.data("tooltip");if(c)return c;b=a.extend(!0,{},a.tools.tooltip.conf,b),typeof b.position=="string"&&(b.position=b.position.split(/,?\s/)),this.each(function(){c=new d(a(this),b),a(this).data("tooltip",c)});return b.api?c:this}})(jQuery);
(function(a){var b=a.tools.tooltip;a.extend(b.conf,{direction:"up",bounce:!1,slideOffset:10,slideInSpeed:200,slideOutSpeed:200,slideFade:!a.browser.msie});var c={up:["-","top"],down:["+","top"],left:["-","left"],right:["+","left"]};b.addEffect("slide",function(a){var b=this.getConf(),d=this.getTip(),e=b.slideFade?{opacity:b.opacity}:{},f=c[b.direction]||c.up;e[f[1]]=f[0]+"="+b.slideOffset,b.slideFade&&d.css({opacity:0}),d.show().animate(e,b.slideInSpeed,a)},function(b){var d=this.getConf(),e=d.slideOffset,f=d.slideFade?{opacity:0}:{},g=c[d.direction]||c.up,h=""+g[0];d.bounce&&(h=h=="+"?"-":"+"),f[g[1]]=h+"="+e,this.getTip().animate(f,d.slideOutSpeed,function(){a(this).hide(),b.call()})})})(jQuery);

// A few helper functions by: PHP.js - http://phpjs.org/
// Main functions: explode, time, serialize, unserialize, rand, percent, pretty_date, number_format
function utf8_decode(str_data){var tmp_arr=[],i=0,ac=0,c1=0,c2=0,c3=0;str_data+='';while(i<str_data.length){c1=str_data.charCodeAt(i);if(c1<128){tmp_arr[ac++]=String.fromCharCode(c1);i++}else if(c1>191&&c1<224){c2=str_data.charCodeAt(i+1);tmp_arr[ac++]=String.fromCharCode(((c1&31)<<6)|(c2&63));i+=2}else{c2=str_data.charCodeAt(i+1);c3=str_data.charCodeAt(i+2);tmp_arr[ac++]=String.fromCharCode(((c1&15)<<12)|((c2&63)<<6)|(c3&63));i+=3}}return tmp_arr.join('')}function utf8_encode(argString){if(argString===null||typeof argString==="undefined"){return""}var string=(argString+'');var utftext="",start,end,stringl=0;start=end=0;stringl=string.length;for(var n=0;n<stringl;n++){var c1=string.charCodeAt(n);var enc=null;if(c1<128){end++}else if(c1>127&&c1<2048){enc=String.fromCharCode((c1>>6)|192)+String.fromCharCode((c1&63)|128)}else{enc=String.fromCharCode((c1>>12)|224)+String.fromCharCode(((c1>>6)&63)|128)+String.fromCharCode((c1&63)|128)}if(enc!==null){if(end>start){utftext+=string.slice(start,end)}utftext+=enc;start=end=n+1}}if(end>start){utftext+=string.slice(start,stringl)}return utftext}function unserialize(data){var that=this;var utf8Overhead=function(chr){var code=chr.charCodeAt(0);if(code<0x0080){return 0}if(code<0x0800){return 1}return 2};var error=function(type,msg,filename,line){throw new that.window[type](msg,filename,line);};var read_until=function(data,offset,stopchr){var buf=[];var chr=data.slice(offset,offset+1);var i=2;while(chr!=stopchr){if((i+offset)>data.length){error('Error','Invalid')}buf.push(chr);chr=data.slice(offset+(i-1),offset+i);i+=1}return[buf.length,buf.join('')]};var read_chrs=function(data,offset,length){var buf;buf=[];for(var i=0;i<length;i++){var chr=data.slice(offset+(i-1),offset+i);buf.push(chr);length-=utf8Overhead(chr)}return[buf.length,buf.join('')]};var _unserialize=function(data,offset){var readdata;var readData;var chrs=0;var ccount;var stringlength;var keyandchrs;var keys;if(!offset){offset=0}var dtype=(data.slice(offset,offset+1)).toLowerCase();var dataoffset=offset+2;var typeconvert=function(x){return x};switch(dtype){case'i':typeconvert=function(x){return parseInt(x,10)};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'b':typeconvert=function(x){return parseInt(x,10)!==0};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'd':typeconvert=function(x){return parseFloat(x)};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'n':readdata=null;break;case's':ccount=read_until(data,dataoffset,':');chrs=ccount[0];stringlength=ccount[1];dataoffset+=chrs+2;readData=read_chrs(data,dataoffset+1,parseInt(stringlength,10));chrs=readData[0];readdata=readData[1];dataoffset+=chrs+2;if(chrs!=parseInt(stringlength,10)&&chrs!=readdata.length){error('SyntaxError','String length mismatch')}readdata=that.utf8_decode(readdata);break;case'a':readdata={};keyandchrs=read_until(data,dataoffset,':');chrs=keyandchrs[0];keys=keyandchrs[1];dataoffset+=chrs+2;for(var i=0;i<parseInt(keys,10);i++){var kprops=_unserialize(data,dataoffset);var kchrs=kprops[1];var key=kprops[2];dataoffset+=kchrs;var vprops=_unserialize(data,dataoffset);var vchrs=vprops[1];var value=vprops[2];dataoffset+=vchrs;readdata[key]=value}dataoffset+=1;break;default:error('SyntaxError','Unknown / Unhandled data type(s): '+dtype);break}return[dtype,dataoffset-offset,typeconvert(readdata)]};return _unserialize((data+''),0)[2]}function time(){return Math.floor(new Date().getTime()/1000)}function serialize(mixed_value){var _utf8Size=function(str){var size=0,i=0,l=str.length,code='';for(i=0;i<l;i++){code=str.charCodeAt(i);if(code<0x0080){size+=1}else if(code<0x0800){size+=2}else{size+=3}}return size};var _getType=function(inp){var type=typeof inp,match;var key;if(type==='object'&&!inp){return'null'}if(type==="object"){if(!inp.constructor){return'object'}var cons=inp.constructor.toString();match=cons.match(/(\w+)\(/);if(match){cons=match[1].toLowerCase()}var types=["boolean","number","string","array"];for(key in types){if(cons==types[key]){type=types[key];break}}}return type};var type=_getType(mixed_value);var val,ktype='';switch(type){case"function":val="";break;case"boolean":val="b:"+(mixed_value?"1":"0");break;case"number":val=(Math.round(mixed_value)==mixed_value?"i":"d")+":"+mixed_value;break;case"string":val="s:"+_utf8Size(mixed_value)+":\""+mixed_value+"\"";break;case"array":case"object":val="a";var count=0;var vals="";var okey;var key;for(key in mixed_value){if(mixed_value.hasOwnProperty(key)){ktype=_getType(mixed_value[key]);if(ktype==="function"){continue}okey=(key.match(/^[0-9]+$/)?parseInt(key,10):key);vals+=this.serialize(okey)+this.serialize(mixed_value[key]);count++}}val+=":"+count+":{"+vals+"}";break;case"undefined":default:val="N";break}if(type!=="object"&&type!=="array"){val+=";"}return val}function explode(c,e,a){var f={0:""};if(arguments.length<2||typeof arguments[0]=="undefined"||typeof arguments[1]=="undefined"){return null}if(c===""||c===false||c===null){return false}if(typeof c=="function"||typeof c=="object"||typeof e=="function"||typeof e=="object"){return f}if(c===true){c="1"}if(!a){return e.toString().split(c.toString())}else{var g=e.toString().split(c.toString());var d=g.splice(0,a-1);var b=g.join(c.toString());d.push(b);return d}};function time () { return Math.floor(new Date().getTime() / 1000); } function pretty_date(d){var b=new Date((d||"").replace(/-/g,"/").replace(/[TZ]/g," ")),c=(((new Date()).getTime()-b.getTime())/1000),a=Math.floor(c/86400);if(isNaN(a)||a<0||a>=31){return}return a==0&&(c<60&&"just now"||c<120&&"1 minute ago"||c<3600&&Math.floor(c/60)+" minutes ago"||c<7200&&"1 hour ago"||c<86400&&Math.floor(c/3600)+" hours ago")||a==1&&"Yesterday"||a<7&&a+" days ago"||a<31&&Math.ceil(a/7)+" weeks ago"}if(typeof jQuery!="undefined"){jQuery.fn.pretty_date=function(){return this.each(function(){var a=pretty_date(this.title);if(a){jQuery(this).text(a)}})}}; function number_format(f,c,h,e){f=(f+"").replace(/[^0-9+\-Ee.]/g,"");var b=!isFinite(+f)?0:+f,a=!isFinite(+c)?0:Math.abs(c),j=(typeof e==="undefined")?",":e,d=(typeof h==="undefined")?".":h,i="",g=function(o,m){var l=Math.pow(10,m);return""+Math.round(o*l)/l};i=(a?g(b,a):""+Math.round(b)).split(".");if(i[0].length>3){i[0]=i[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,j)}if((i[1]||"").length<a){i[1]=i[1]||"";i[1]+=new Array(a-i[1].length+1).join("0")}return i.join(d)};
function valid_url(str) { var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
return regexp.test(str); }



function rand(min,max){var argc=arguments.length;if(argc===0){min=0;max=2147483647}else if(argc===1){throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');}return Math.floor(Math.random()*(max-min+1))+min}

/* Modernizr 2.0.4 (Custom Build) | MIT & BSD
 * Contains: fontface | backgroundsize | borderimage | borderradius | boxshadow | flexbox | hsla | multiplebgs | opacity | rgba | textshadow | cssanimations | generatedcontent | cssgradients | cssreflections | csstransforms | csstransforms3d | csstransitions | applicationcache | canvas | canvastext | hashchange | history | audio | input | inputtypes | localstorage | sessionstorage | websockets | websqldatabase | geolocation | inlinesvg | svg | svgclippaths | webgl | iepp | cssclasses | teststyles | testprop | testallprops | hasevent | prefixes | domprefixes | load
 */
;window.Modernizr=function(a,b,c){function H(){e.input=function(a){for(var b=0,c=a.length;b<c;b++)t[a[b]]=a[b]in l;return t}("autocomplete autofocus list placeholder max min multiple pattern required step".split(" ")),e.inputtypes=function(a){for(var d=0,e,f,h,i=a.length;d<i;d++)l.setAttribute("type",f=a[d]),e=l.type!=="text",e&&(l.value=m,l.style.cssText="position:absolute;visibility:hidden;",/^range$/.test(f)&&l.style.WebkitAppearance!==c?(g.appendChild(l),h=b.defaultView,e=h.getComputedStyle&&h.getComputedStyle(l,null).WebkitAppearance!=="textfield"&&l.offsetHeight!==0,g.removeChild(l)):/^(search|tel)$/.test(f)||(/^(url|email)$/.test(f)?e=l.checkValidity&&l.checkValidity()===!1:/^color$/.test(f)?(g.appendChild(l),g.offsetWidth,e=l.value!=m,g.removeChild(l)):e=l.value!=m)),s[a[d]]=!!e;return s}("search tel url email datetime date month week time datetime-local number range color".split(" "))}function F(a,b){var c=a.charAt(0).toUpperCase()+a.substr(1),d=(a+" "+p.join(c+" ")+c).split(" ");return E(d,b)}function E(a,b){for(var d in a)if(k[a[d]]!==c)return b=="pfx"?a[d]:!0;return!1}function D(a,b){return!!~(""+a).indexOf(b)}function C(a,b){return typeof a===b}function B(a,b){return A(o.join(a+";")+(b||""))}function A(a){k.cssText=a}var d="2.0.4",e={},f=!0,g=b.documentElement,h=b.head||b.getElementsByTagName("head")[0],i="modernizr",j=b.createElement(i),k=j.style,l=b.createElement("input"),m=":)",n=Object.prototype.toString,o=" -webkit- -moz- -o- -ms- -khtml- ".split(" "),p="Webkit Moz O ms Khtml".split(" "),q={svg:"http://www.w3.org/2000/svg"},r={},s={},t={},u=[],v=function(a,c,d,e){var f,h,j,k=b.createElement("div");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:i+(d+1),k.appendChild(j);f=["&shy;","<style>",a,"</style>"].join(""),k.id=i,k.innerHTML+=f,g.appendChild(k),h=c(k,a),k.parentNode.removeChild(k);return!!h},w=function(){function d(d,e){e=e||b.createElement(a[d]||"div"),d="on"+d;var f=d in e;f||(e.setAttribute||(e=b.createElement("div")),e.setAttribute&&e.removeAttribute&&(e.setAttribute(d,""),f=C(e[d],"function"),C(e[d],c)||(e[d]=c),e.removeAttribute(d))),e=null;return f}var a={select:"input",change:"input",submit:"form",reset:"form",error:"img",load:"img",abort:"img"};return d}(),x,y={}.hasOwnProperty,z;!C(y,c)&&!C(y.call,c)?z=function(a,b){return y.call(a,b)}:z=function(a,b){return b in a&&C(a.constructor.prototype[b],c)};var G=function(a,c){var d=a.join(""),f=c.length;v(d,function(a,c){var d=b.styleSheets[b.styleSheets.length-1],g=d.cssRules&&d.cssRules[0]?d.cssRules[0].cssText:d.cssText||"",h=a.childNodes,i={};while(f--)i[h[f].id]=h[f];e.csstransforms3d=i.csstransforms3d.offsetLeft===9,e.generatedcontent=i.generatedcontent.offsetHeight>=1,e.fontface=/src/i.test(g)&&g.indexOf(c.split(" ")[0])===0},f,c)}(['@font-face {font-family:"font";src:url("https://")}',["@media (",o.join("transform-3d),("),i,")","{#csstransforms3d{left:9px;position:absolute}}"].join(""),['#generatedcontent:after{content:"',m,'"}'].join("")],["fontface","csstransforms3d","generatedcontent"]);r.flexbox=function(){function c(a,b,c,d){a.style.cssText=o.join(b+":"+c+";")+(d||"")}function a(a,b,c,d){b+=":",a.style.cssText=(b+o.join(c+";"+b)).slice(0,-b.length)+(d||"")}var d=b.createElement("div"),e=b.createElement("div");a(d,"display","box","width:42px;padding:0;"),c(e,"box-flex","1","width:10px;"),d.appendChild(e),g.appendChild(d);var f=e.offsetWidth===42;d.removeChild(e),g.removeChild(d);return f},r.canvas=function(){var a=b.createElement("canvas");return!!a.getContext&&!!a.getContext("2d")},r.canvastext=function(){return!!e.canvas&&!!C(b.createElement("canvas").getContext("2d").fillText,"function")},r.webgl=function(){return!!a.WebGLRenderingContext},r.geolocation=function(){return!!navigator.geolocation},r.websqldatabase=function(){var b=!!a.openDatabase;return b},r.hashchange=function(){return w("hashchange",a)&&(b.documentMode===c||b.documentMode>7)},r.history=function(){return!!a.history&&!!history.pushState},r.websockets=function(){for(var b=-1,c=p.length;++b<c;)if(a[p[b]+"WebSocket"])return!0;return"WebSocket"in a},r.rgba=function(){A("background-color:rgba(150,255,150,.5)");return D(k.backgroundColor,"rgba")},r.hsla=function(){A("background-color:hsla(120,40%,100%,.5)");return D(k.backgroundColor,"rgba")||D(k.backgroundColor,"hsla")},r.multiplebgs=function(){A("background:url(https://),url(https://),red url(https://)");return/(url\s*\(.*?){3}/.test(k.background)},r.backgroundsize=function(){return F("backgroundSize")},r.borderimage=function(){return F("borderImage")},r.borderradius=function(){return F("borderRadius")},r.boxshadow=function(){return F("boxShadow")},r.textshadow=function(){return b.createElement("div").style.textShadow===""},r.opacity=function(){B("opacity:.55");return/^0.55$/.test(k.opacity)},r.cssanimations=function(){return F("animationName")},r.cssgradients=function(){var a="background-image:",b="gradient(linear,left top,right bottom,from(#9f9),to(white));",c="linear-gradient(left top,#9f9, white);";A((a+o.join(b+a)+o.join(c+a)).slice(0,-a.length));return D(k.backgroundImage,"gradient")},r.cssreflections=function(){return F("boxReflect")},r.csstransforms=function(){return!!E(["transformProperty","WebkitTransform","MozTransform","OTransform","msTransform"])},r.csstransforms3d=function(){var a=!!E(["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"]);a&&"webkitPerspective"in g.style&&(a=e.csstransforms3d);return a},r.csstransitions=function(){return F("transitionProperty")},r.fontface=function(){return e.fontface},r.generatedcontent=function(){return e.generatedcontent},r.audio=function(){var a=b.createElement("audio"),c=!1;try{if(c=!!a.canPlayType)c=new Boolean(c),c.ogg=a.canPlayType('audio/ogg; codecs="vorbis"'),c.mp3=a.canPlayType("audio/mpeg;"),c.wav=a.canPlayType('audio/wav; codecs="1"'),c.m4a=a.canPlayType("audio/x-m4a;")||a.canPlayType("audio/aac;")}catch(d){}return c},r.localstorage=function(){try{return!!localStorage.getItem}catch(a){return!1}},r.sessionstorage=function(){try{return!!sessionStorage.getItem}catch(a){return!1}},r.applicationcache=function(){return!!a.applicationCache},r.svg=function(){return!!b.createElementNS&&!!b.createElementNS(q.svg,"svg").createSVGRect},r.inlinesvg=function(){var a=b.createElement("div");a.innerHTML="<svg/>";return(a.firstChild&&a.firstChild.namespaceURI)==q.svg},r.svgclippaths=function(){return!!b.createElementNS&&/SVG/.test(n.call(b.createElementNS(q.svg,"clipPath")))};for(var I in r)z(r,I)&&(x=I.toLowerCase(),e[x]=r[I](),u.push((e[x]?"":"no-")+x));e.input||H(),A(""),j=l=null,a.attachEvent&&function(){var a=b.createElement("div");a.innerHTML="<elem></elem>";return a.childNodes.length!==1}()&&function(a,b){function s(a){var b=-1;while(++b<g)a.createElement(f[b])}a.iepp=a.iepp||{};var d=a.iepp,e=d.html5elements||"abbr|article|aside|audio|canvas|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",f=e.split("|"),g=f.length,h=new RegExp("(^|\\s)("+e+")","gi"),i=new RegExp("<(/*)("+e+")","gi"),j=/^\s*[\{\}]\s*$/,k=new RegExp("(^|[^\\n]*?\\s)("+e+")([^\\n]*)({[\\n\\w\\W]*?})","gi"),l=b.createDocumentFragment(),m=b.documentElement,n=m.firstChild,o=b.createElement("body"),p=b.createElement("style"),q=/print|all/,r;d.getCSS=function(a,b){if(a+""===c)return"";var e=-1,f=a.length,g,h=[];while(++e<f){g=a[e];if(g.disabled)continue;b=g.media||b,q.test(b)&&h.push(d.getCSS(g.imports,b),g.cssText),b="all"}return h.join("")},d.parseCSS=function(a){var b=[],c;while((c=k.exec(a))!=null)b.push(((j.exec(c[1])?"\n":c[1])+c[2]+c[3]).replace(h,"$1.iepp_$2")+c[4]);return b.join("\n")},d.writeHTML=function(){var a=-1;r=r||b.body;while(++a<g){var c=b.getElementsByTagName(f[a]),d=c.length,e=-1;while(++e<d)c[e].className.indexOf("iepp_")<0&&(c[e].className+=" iepp_"+f[a])}l.appendChild(r),m.appendChild(o),o.className=r.className,o.id=r.id,o.innerHTML=r.innerHTML.replace(i,"<$1font")},d._beforePrint=function(){p.styleSheet.cssText=d.parseCSS(d.getCSS(b.styleSheets,"all")),d.writeHTML()},d.restoreHTML=function(){o.innerHTML="",m.removeChild(o),m.appendChild(r)},d._afterPrint=function(){d.restoreHTML(),p.styleSheet.cssText=""},s(b),s(l);d.disablePP||(n.insertBefore(p,n.firstChild),p.media="print",p.className="iepp-printshim",a.attachEvent("onbeforeprint",d._beforePrint),a.attachEvent("onafterprint",d._afterPrint))}(a,b),e._version=d,e._prefixes=o,e._domPrefixes=p,e.hasEvent=w,e.testProp=function(a){return E([a])},e.testAllProps=F,e.testStyles=v,g.className=g.className.replace(/\bno-js\b/,"")+(f?" js "+u.join(" "):"");return e}(this,this.document),function(a,b,c){function k(a){return!a||a=="loaded"||a=="complete"}function j(){var a=1,b=-1;while(p.length- ++b)if(p[b].s&&!(a=p[b].r))break;a&&g()}function i(a){var c=b.createElement("script"),d;c.src=a.s,c.onreadystatechange=c.onload=function(){!d&&k(c.readyState)&&(d=1,j(),c.onload=c.onreadystatechange=null)},m(function(){d||(d=1,j())},H.errorTimeout),a.e?c.onload():n.parentNode.insertBefore(c,n)}function h(a){var c=b.createElement("link"),d;c.href=a.s,c.rel="stylesheet",c.type="text/css",!a.e&&(w||r)?function a(b){m(function(){if(!d)try{b.sheet.cssRules.length?(d=1,j()):a(b)}catch(c){c.code==1e3||c.message=="security"||c.message=="denied"?(d=1,m(function(){j()},0)):a(b)}},0)}(c):(c.onload=function(){d||(d=1,m(function(){j()},0))},a.e&&c.onload()),m(function(){d||(d=1,j())},H.errorTimeout),!a.e&&n.parentNode.insertBefore(c,n)}function g(){var a=p.shift();q=1,a?a.t?m(function(){a.t=="c"?h(a):i(a)},0):(a(),j()):q=0}function f(a,c,d,e,f,h){function i(){!o&&k(l.readyState)&&(r.r=o=1,!q&&j(),l.onload=l.onreadystatechange=null,m(function(){u.removeChild(l)},0))}var l=b.createElement(a),o=0,r={t:d,s:c,e:h};l.src=l.data=c,!s&&(l.style.display="none"),l.width=l.height="0",a!="object"&&(l.type=d),l.onload=l.onreadystatechange=i,a=="img"?l.onerror=i:a=="script"&&(l.onerror=function(){r.e=r.r=1,g()}),p.splice(e,0,r),u.insertBefore(l,s?null:n),m(function(){o||(u.removeChild(l),r.r=r.e=o=1,j())},H.errorTimeout)}function e(a,b,c){var d=b=="c"?z:y;q=0,b=b||"j",C(a)?f(d,a,b,this.i++,l,c):(p.splice(this.i++,0,a),p.length==1&&g());return this}function d(){var a=H;a.loader={load:e,i:0};return a}var l=b.documentElement,m=a.setTimeout,n=b.getElementsByTagName("script")[0],o={}.toString,p=[],q=0,r="MozAppearance"in l.style,s=r&&!!b.createRange().compareNode,t=r&&!s,u=s?l:n.parentNode,v=a.opera&&o.call(a.opera)=="[object Opera]",w="webkitAppearance"in l.style,x=w&&"async"in b.createElement("script"),y=r?"object":v||x?"img":"script",z=w?"img":y,A=Array.isArray||function(a){return o.call(a)=="[object Array]"},B=function(a){return typeof a=="object"},C=function(a){return typeof a=="string"},D=function(a){return o.call(a)=="[object Function]"},E=[],F={},G,H;H=function(a){function f(a){var b=a.split("!"),c=E.length,d=b.pop(),e=b.length,f={url:d,origUrl:d,prefixes:b},g,h;for(h=0;h<e;h++)g=F[b[h]],g&&(f=g(f));for(h=0;h<c;h++)f=E[h](f);return f}function e(a,b,e,g,h){var i=f(a),j=i.autoCallback;if(!i.bypass){b&&(b=D(b)?b:b[a]||b[g]||b[a.split("/").pop().split("?")[0]]);if(i.instead)return i.instead(a,b,e,g,h);e.load(i.url,i.forceCSS||!i.forceJS&&/css$/.test(i.url)?"c":c,i.noexec),(D(b)||D(j))&&e.load(function(){d(),b&&b(i.origUrl,h,g),j&&j(i.origUrl,h,g)})}}function b(a,b){function c(a){if(C(a))e(a,h,b,0,d);else if(B(a))for(i in a)a.hasOwnProperty(i)&&e(a[i],h,b,i,d)}var d=!!a.test,f=d?a.yep:a.nope,g=a.load||a.both,h=a.callback,i;c(f),c(g),a.complete&&b.load(a.complete)}var g,h,i=this.yepnope.loader;if(C(a))e(a,0,i,0);else if(A(a))for(g=0;g<a.length;g++)h=a[g],C(h)?e(h,0,i,0):A(h)?H(h):B(h)&&b(h,i);else B(a)&&b(a,i)},H.addPrefix=function(a,b){F[a]=b},H.addFilter=function(a){E.push(a)},H.errorTimeout=1e4,b.readyState==null&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",G=function(){b.removeEventListener("DOMContentLoaded",G,0),b.readyState="complete"},0)),a.yepnope=d()}(this,this.document),Modernizr.load=function(){yepnope.apply(window,[].slice.call(arguments,0))};

/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 *
 * RGBA support by Mehdi Kabab <http://pioupioum.fr>
 */

(function(e){e.extend(e.support,{rgba:c()});e.each(["backgroundColor","borderBottomColor","borderLeftColor","borderRightColor","borderTopColor","color","outlineColor"],function(g,f){e.fx.step[f]=function(i){var h=[];if(!i.colorInit){i.start=d(i.elem,f);i.end=b(i.end);i.alphavalue={start:4===i.start.length,end:4===i.end.length};if(!i.alphavalue.start){i.start.push(1)}if(!i.alphavalue.end){i.end.push(1)}if(e.support.rgba&&(!i.alphavalue.start&&i.alphavalue.end)||(i.alphavalue.start&&i.alphavalue.end)||(i.alphavalue.start&&!i.alphavalue.end)){i.colorModel="rgba"}else{i.colorModel="rgb"}i.colorInit=true}h.push(Math.max(Math.min(parseInt((i.pos*(i.end[0]-i.start[0]))+i.start[0]),255),0));h.push(Math.max(Math.min(parseInt((i.pos*(i.end[1]-i.start[1]))+i.start[1]),255),0));h.push(Math.max(Math.min(parseInt((i.pos*(i.end[2]-i.start[2]))+i.start[2]),255),0));if(i.colorModel=="rgba"){h.push(Math.max(Math.min(parseFloat((i.pos*(i.end[3]-i.start[3]))+i.start[3]),1),0).toFixed(2))}i.elem.style[f]=i.colorModel+"("+h.join(",")+")"}});function b(g){var f,i,j="(?:,\\s*((?:1|0)(?:\\.0+)?|(?:0?\\.[0-9]+))\\s*)?\\)",h=new RegExp("rgb(a)?\\(\\s*([0-9]{1,3})\\s*,\\s*([0-9]{1,3})\\s*,\\s*([0-9]{1,3})\\s*"+j),k=new RegExp("rgb(a)?\\(\\s*([0-9]+(?:\\.[0-9]+)?)\\%\\s*,\\s*([0-9]+(?:\\.[0-9]+)?)\\%\\s*,\\s*([0-9]+(?:\\.[0-9]+)?)\\%\\s*"+j);if(g&&g.constructor==Array&&g.length>=3&&g.length<=4){return g}if(f=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(g)){return[parseInt(f[1],16),parseInt(f[2],16),parseInt(f[3],16)]}if(f=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(g)){return[parseInt(f[1]+f[1],16),parseInt(f[2]+f[2],16),parseInt(f[3]+f[3],16)]}if(f=h.exec(g)){i=[parseInt(f[2]),parseInt(f[3]),parseInt(f[4])];if(f[1]&&f[5]){i.push(parseFloat(f[5]))}return i}if(f=k.exec(g)){i=[parseFloat(f[2])*2.55,parseFloat(f[3])*2.55,parseFloat(f[4])*2.55];if(f[1]&&f[5]){i.push(parseFloat(f[5]))}return i}return a[e.trim(g).toLowerCase()]}function d(h,f){var g;do{g=e.curCSS(h,f);if(g!=""&&g!="transparent"||e.nodeName(h,"body")){break}f="backgroundColor"}while(h=h.parentNode);return b(g)}function c(){var h=e("script:first"),g=h.css("color"),f=false;if(/^rgba/.test(g)){f=true}else{try{f=(g!=h.css("color","rgba(0, 0, 0, 0.5)").css("color"));h.css("color",g)}catch(i){}}return f}var a={aqua:[0,255,255],azure:[240,255,255],beige:[245,245,220],black:[0,0,0],blue:[0,0,255],brown:[165,42,42],cyan:[0,255,255],darkblue:[0,0,139],darkcyan:[0,139,139],darkgrey:[169,169,169],darkgreen:[0,100,0],darkkhaki:[189,183,107],darkmagenta:[139,0,139],darkolivegreen:[85,107,47],darkorange:[255,140,0],darkorchid:[153,50,204],darkred:[139,0,0],darksalmon:[233,150,122],darkviolet:[148,0,211],fuchsia:[255,0,255],gold:[255,215,0],green:[0,128,0],indigo:[75,0,130],khaki:[240,230,140],lightblue:[173,216,230],lightcyan:[224,255,255],lightgreen:[144,238,144],lightgrey:[211,211,211],lightpink:[255,182,193],lightyellow:[255,255,224],lime:[0,255,0],magenta:[255,0,255],maroon:[128,0,0],navy:[0,0,128],olive:[128,128,0],orange:[255,165,0],pink:[255,192,203],purple:[128,0,128],violet:[128,0,128],red:[255,0,0],silver:[192,192,192],white:[255,255,255],yellow:[255,255,0],transparent:(e.support.rgba)?[255,255,255,0]:[255,255,255]}})(jQuery);

// ----------------------------------------------------------------------------
// Buzz - A Javascript HTML5 audio library 
// v 1.0.3 beta
// Licensed under the MIT license.
// http://buzz.jaysalvat.com/
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files ( the "Software" ), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
var buzz={defaults:{autoplay:false,duration:5000,formats:[],loop:false,placeholder:"--",preload:"metadata",volume:100},sounds:[],sound:function(b,k){var k=k||{},f=0,j=[],d={},c=buzz.isSupported();this.load=function(){if(!c){return this}this.sound.load();return this};this.play=function(){if(!c){return this}this.sound.play();return this};this.togglePlay=function(){if(!c){return this}if(this.sound.paused){this.sound.play()}else{this.sound.pause()}return this};this.pause=function(){if(!c){return this}this.sound.pause();return this};this.isPaused=function(){if(!c){return null}return this.sound.paused};this.stop=function(){if(!c){return this}this.setTime(0);this.sound.pause();return this};this.isEnded=function(){if(!c){return null}return this.sound.ended};this.loop=function(){this.sound.loop="loop";this.bind("ended.buzzloop",function(){this.currentTime=0;this.play()});return this};this.unloop=function(){this.sound.removeAttribute("loop");this.unbind("ended.buzzloop");return this};this.mute=function(){if(!c){return this}this.sound.muted=true;return this};this.unmute=function(){if(!c){return this}this.sound.muted=false;return this};this.toggleMute=function(){if(!c){return this}this.sound.muted=!this.sound.muted;return this};this.isMuted=function(){if(!c){return null}return this.sound.muted};this.setVolume=function(i){if(!c){return this}if(i<0){i=0}if(i>100){i=100}this.volume=i;this.sound.volume=i/100;return this},this.getVolume=function(){if(!c){return this}return this.volume};this.increaseVolume=function(i){return this.setVolume(this.volume+(i||1))};this.decreaseVolume=function(i){return this.setVolume(this.volume-(i||1))};this.setTime=function(i){if(!c){return this}this.whenReady(function(){this.sound.currentTime=i});return this};this.getTime=function(){if(!c){return null}var i=Math.round(this.sound.currentTime*100)/100;return isNaN(i)?buzz.defaults.placeholder:i};this.setPercent=function(i){if(!c){return this}return this.setTime(buzz.fromPercent(i,this.sound.duration))};this.getPercent=function(){if(!c){return null}var i=Math.round(buzz.toPercent(this.sound.currentTime,this.sound.duration));return isNaN(i)?buzz.defaults.placeholder:i};this.setSpeed=function(i){if(!c){return this}this.sound.playbackRate=i};this.getSpeed=function(){if(!c){return null}return this.sound.playbackRate};this.getDuration=function(){if(!c){return null}var i=Math.round(this.sound.duration*100)/100;return isNaN(i)?buzz.defaults.placeholder:i};this.getPlayed=function(){if(!c){return null}return g(this.sound.played)};this.getBuffered=function(){if(!c){return null}return g(this.sound.buffered)};this.getSeekable=function(){if(!c){return null}return g(this.sound.seekable)};this.getErrorCode=function(){if(c&&this.sound.error){return this.sound.error.code}return 0};this.getErrorMessage=function(){if(!c){return null}switch(this.getErrorCode()){case 1:return"MEDIA_ERR_ABORTED";case 2:return"MEDIA_ERR_NETWORK";case 3:return"MEDIA_ERR_DECODE";case 4:return"MEDIA_ERR_SRC_NOT_SUPPORTED";default:return null}};this.getStateCode=function(){if(!c){return null}return this.sound.readyState};this.getStateMessage=function(){if(!c){return null}switch(this.getStateCode()){case 0:return"HAVE_NOTHING";case 1:return"HAVE_METADATA";case 2:return"HAVE_CURRENT_DATA";case 3:return"HAVE_FUTURE_DATA";case 4:return"HAVE_ENOUGH_DATA";default:return null}};this.getNetworkStateCode=function(){if(!c){return null}return this.sound.networkState};this.getNetworkStateMessage=function(){if(!c){return null}switch(this.getNetworkStateCode()){case 0:return"NETWORK_EMPTY";case 1:return"NETWORK_IDLE";case 2:return"NETWORK_LOADING";case 3:return"NETWORK_NO_SOURCE";default:return null}};this.set=function(i,l){if(!c){return this}this.sound[i]=l;return this};this.get=function(i){if(!c){return null}return i?this.sound[i]:this.sound};this.bind=function(n,q){if(!c){return this}var p=this,n=n.split(" "),r=function(t){q.call(p,t)};for(var l in n){var o=n[l],i=o;o=i.split(".")[0];j.push({idx:i,func:r});this.sound.addEventListener(o,r,true)}return this};this.unbind=function(p){if(!c){return this}var p=p.split(" ");for(var o in p){var l=p[o];type=l.split(".")[0];for(var n in j){var q=j[n].idx.split(".");if(j[n].idx==l||(q[1]&&q[1]==l.replace(".",""))){this.sound.removeEventListener(type,j[n].func,true);delete j[n]}}}return this};this.bindOnce=function(i,n){if(!c){return this}var l=this;d[f++]=false;this.bind(f+i,function(){if(!d[f]){d[f]=true;n.call(l)}l.unbind(f+i)})};this.trigger=function(p){if(!c){return this}var p=p.split(" ");for(var o in p){var l=p[o];for(var n in j){var q=j[n].idx.split(".");if(j[n].idx==l||(q[0]&&q[0]==l.replace(".",""))){j[n].func.apply(this)}}}return this};this.fadeTo=function(r,o,q){if(!c){return this}if(o instanceof Function){q=o;o=buzz.defaults.duration}else{o=o||buzz.defaults.duration}var p=this.volume,l=o/Math.abs(p-r),n=this;this.play();function i(){setTimeout(function(){if(p<r&&n.volume<r){n.setVolume(n.volume+=1);i()}else{if(p>r&&n.volume>r){n.setVolume(n.volume-=1);i()}else{if(q instanceof Function){q.apply(n)}}}},l)}this.whenReady(function(){i()});return this};this.fadeIn=function(i,l){if(!c){return this}return this.setVolume(0).fadeTo(100,i,l)};this.fadeOut=function(i,l){if(!c){return this}return this.fadeTo(0,i,l)};this.fadeWith=function(l,i){if(!c){return this}this.fadeOut(i,function(){this.stop()});if(l instanceof buzz.sound){l.play().fadeIn(i)}return this};this.whenReady=function(l){var i=this;if(this.sound.readyState==0){this.bind("canplay.buzzwhenready",function(){l.call(i)})}else{l.call(i)}};function g(l){var p=[],o=l.length-1;for(var n=0;n<=o;n++){p.push({start:l.start(o),end:l.end(o)})}return p}if(c){for(var e in buzz.defaults){k[e]=k[e]||buzz.defaults[e]}this.sound=document.createElement("audio");if(b instanceof Array){for(var e in b){var a=document.createElement("source");a.src=b[e];this.sound.appendChild(a)}}else{if(k.formats.length){for(var e in k.formats){var a=document.createElement("source");a.src=b+"."+k.formats[e];this.sound.appendChild(a)}}else{this.sound.src=b}}if(k.loop){this.loop()}if(k.autoplay){this.sound.autoplay="autoplay"}if(k.preload===true){this.sound.preload="auto"}else{if(k.preload===false){this.sound.preload="none"}else{this.sound.preload=k.preload}}this.setVolume(k.volume);buzz.sounds.push(this)}},group:function(a){var a=c(a,arguments);this.getSounds=function(){return a};this.add=function(f){var f=c(f,arguments);for(var d in f){for(var e in a){a.push(f[d])}}};this.remove=function(f){var f=c(f,arguments);for(var d in f){for(var e in a){if(a[e]==f[d]){delete a[e];break}}}};this.load=function(){b("load");return this};this.play=function(){b("play");return this};this.togglePlay=function(){b("togglePlay");return this};this.pause=function(d){b("pause",d);return this};this.stop=function(){b("stop");return this};this.mute=function(){b("mute");return this};this.unmute=function(){b("unmute");return this};this.toggleMute=function(){b("toggleMute");return this};this.setVolume=function(d){b("setVolume",d);return this};this.increaseVolume=function(d){b("increaseVolume",d);return this};this.decreaseVolume=function(d){b("decreaseVolume",d);return this};this.loop=function(){b("loop");return this};this.unloop=function(){b("unloop");return this};this.setTime=function(d){b("setTime",d);return this};this.setduration=function(d){b("setduration",d);return this};this.set=function(d,e){b("set",d,e);return this};this.bind=function(d,e){b("bind",d,e);return this};this.unbind=function(d){b("unbind",d);return this};this.bindOnce=function(d,e){b("bindOnce",d,e);return this};this.trigger=function(d){b("trigger",d);return this};this.fade=function(g,f,d,e){b("fade",g,f,d,e);return this};this.fadeIn=function(d,e){b("fadeIn",d,e);return this};this.fadeOut=function(d,e){b("fadeOut",d,e);return this};function b(){var d=c(null,arguments),f=d.shift();for(var e in a){a[e][f].apply(a[e],d)}}function c(e,d){return(e instanceof Array)?e:Array.prototype.slice.call(d)}},all:function(){return new buzz.group(buzz.sounds)},el:document.createElement("audio"),isSupported:function(){return !!this.el.canPlayType},isOGGSupported:function(){return !!this.el.canPlayType&&this.el.canPlayType('audio/ogg; codecs="vorbis"')},isWAVSupported:function(){return !!this.el.canPlayType&&this.el.canPlayType('audio/wav; codecs="1"')},isMP3Supported:function(){return !!this.el.canPlayType&&this.el.canPlayType("audio/mpeg;")},isAACSupported:function(){return !!this.el.canPlayType&&(this.el.canPlayType("audio/x-m4a;")||this.el.canPlayType("audio/aac;"))},toTimer:function(b,a){h=Math.floor(b/3600);h=isNaN(h)?"--":(h>=10)?h:"0"+h;m=a?Math.floor(b/60%60):Math.floor(b/60);m=isNaN(m)?"--":(m>=10)?m:"0"+m;s=Math.floor(b%60);s=isNaN(s)?"--":(b>=10)?s:"0"+s;return a?h+":"+m+":"+s:m+":"+s},fromTimer:function(b){var a=b.toString().split(":");if(a&&a.length==3){b=(parseInt(a[0])*3600)+(parseInt(a[1])*60)+parseInt(a[2])}if(a&&a.length==2){b=(parseInt(a[0])*60)+parseInt(a[1])}return b},toPercent:function(d,c,a){var b=Math.pow(10,a||0);return Math.round(((d*100)/c)*b)/b},fromPercent:function(d,c,a){var b=Math.pow(10,a||0);return Math.round(((c/100)*d)*b)/b}};

// jQuery css bezier animation support -- Jonah Fox
;(function(b){b.path={};var a={rotate:function(f,g){var e=g*3.141592654/180;var h=Math.cos(e),d=Math.sin(e);return[h*f[0]-d*f[1],d*f[0]+h*f[1]]},scale:function(c,d){return[d*c[0],d*c[1]]},add:function(d,c){return[d[0]+c[0],d[1]+c[1]]},minus:function(d,c){return[d[0]-c[0],d[1]-c[1]]}};b.path.bezier=function(g){g.start=b.extend({angle:0,length:0.3333},g.start);g.end=b.extend({angle:0,length:0.3333},g.end);this.p1=[g.start.x,g.start.y];this.p4=[g.end.x,g.end.y];var d=a.minus(this.p4,this.p1);var e=a.scale(d,g.start.length);e=a.rotate(e,g.start.angle);this.p2=a.add(this.p1,e);var c=a.scale(d,-1);var f=a.scale(c,g.end.length);f=a.rotate(f,g.end.angle);this.p3=a.add(this.p4,f);this.f1=function(h){return(h*h*h)};this.f2=function(h){return(3*h*h*(1-h))};this.f3=function(h){return(3*h*(1-h)*(1-h))};this.f4=function(h){return((1-h)*(1-h)*(1-h))};this.css=function(k){var i=this.f1(k),n=this.f2(k),l=this.f3(k),j=this.f4(k);var h=this.p1[0]*i+this.p2[0]*n+this.p3[0]*l+this.p4[0]*j;var m=this.p1[1]*i+this.p2[1]*n+this.p3[1]*l+this.p4[1]*j;return{top:m+"px",left:h+"px"}}};b.path.arc=function(d){for(var c in d){this[c]=d[c]}this.dir=this.dir||1;while(this.start>this.end&&this.dir>0){this.start-=360}while(this.start<this.end&&this.dir<0){this.start+=360}this.css=function(g){var f=this.start*(g)+this.end*(1-(g));f=f*3.1415927/180;var e=Math.sin(f)*this.radius+this.center[0];var h=Math.cos(f)*this.radius+this.center[1];return{top:h+"px",left:e+"px"}}};b.fx.step.path=function(e){var d=e.end.css(1-e.pos);for(var c in d){e.elem.style[c]=d[c]}}})(jQuery);

/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * @author Ariel Flesler
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);

/*
 * jQuery.ajaxComet - Quick long polling tool by Tyler Diaz for ManaHaven
 * 
 * $.ajaxComet.start({
 *     url: "/comet/active_topic.php",
 *     data: {},
 *     delay: 1000,
 *     timeout: 30000,
 *     callback: function(json){
 *         console.log(json);
 *     }
 * });
*/
jQuery.ajaxComet={ajax:{},start:function(b){var a=this;b=jQuery.extend({timeout:30000,data:{},delay:1000,callback:function(){console.log("Callback not found")},error:function(){setTimeout(function(){a.start(b)},b.delay)}},b);a.ajax=$.ajax({type:"GET",url:b.url,data:b.data,cache:false,dataType:"json",timeout:b.timeout,success:function(c){b.callback(c);},error:function(e,c,d){b.error(e,c,d)}});return a.ajax},stop:function(){this.ajax.abort()}};

// Handy functions
$.fn.decrease = function(num, format) {
    if (typeof format == 'undefined') format = false;
    
    if(this.length == 1){
        var attr = $(this).attr('data_amount');
        if (typeof attr !== 'undefined' && attr !== false) {
            if(format){
                this.text(number_format(parseInt(attr)-parseInt(num)));
                $(this).attr('data_amount', parseInt(attr)-parseInt(num));
            } else {
                this.text(parseInt(attr)-parseInt(num));
                $(this).attr('data_amount', parseInt(attr)-parseInt(num));
            }
        } else {
            this.text(parseInt(this.text())-parseInt(num));
        }
    }
};
$.fn.increase = function(num, format) {
    if (typeof format == 'undefined') format = false;
    
    if(this.length == 1){
        var attr = $(this).attr('data_amount');
        if (typeof attr !== 'undefined' && attr !== false) {
            if(format){
                this.text(number_format(parseInt(attr)+parseInt(num)));
                $(this).attr('data_amount', parseInt(attr)+parseInt(num));
            } else {
                this.text(parseInt(attr)+parseInt(num));
                $(this).attr('data_amount', parseInt(attr)+parseInt(num));
            }
        } else {
            this.text(parseInt(this.text())+parseInt(num));
        }
    }
};

$.fn.reload_img = function() {
    if(this.length == 1) this.attr('src', this.attr('src')+'?rnd='+rand(0, 10000));
};

// Streaming content requires ID
$.fn.stream = function(){
	var locate_method = "";
	
	if(this.attr('id') == undefined){
		locate_method = "."+this.attr('class')
	} else {
		locate_method = "#"+this.attr('id')
	}
	
    var new_rand_div = 'reload_'+Math.floor(Math.random()*151);
    this.wrap('<div id="'+new_rand_div+'"></div>');
    
    setInterval(function(){
        $("#"+new_rand_div).load(document.URL+" "+locate_method);                
    }, 400);
};


function redirect(url){ window.location = baseurl+url;}
function percent(num, limit) { return Math.round(((num / limit) * 100)); }

// Smart popups -- Tyler Diaz
var popup = {
    block_ui: false,
    main_data: {},
    show: function(speed, timeout){
        $(".popup_shadow #popup_notice").hide();
        $(".close_box").bind('click', function(){ 
            popup.hide(); 
            return false; 
        });
        
        speed = typeof(speed) != 'undefined' ? speed : 400;
        
        if(typeof(timeout) != 'undefined'){
            setTimeout(function(){
                $(".popup_shadow").css({ zIndex: 4000 }).fadeIn(speed);
            }, timeout);
        } else {
            $(".popup_shadow").fadeIn(speed);
        }
    },
    hide: function(speed, timeout){
        popup.block_ui = false;
        speed = typeof(speed) != 'undefined' ? speed : 400;
        
        if(timeout != 'undefined'){
            setTimeout(function(){
                $(".popup_shadow").fadeOut(speed);
				$(".popup_shadow").css('width', popup.original_width);
            }, timeout);
        } else {
            $(".popup_shadow").fadeOut(speed);
        }
    },
    create_button: function(obj){
        if(typeof(obj.label) != 'undefined'){
			$(".popup_shadow .button_footer ."+obj['class']).html(obj.label);
		}
        
        if(typeof(obj.position) != 'undefined'){
            $(".popup_shadow .button_footer ."+obj['class']).addClass(obj.position);
        } else {
            if(obj['class'] == 'cancel_button'){
                $(".popup_shadow .button_footer ."+obj['class']).addClass('left');
            } else {
                $(".popup_shadow .button_footer ."+obj['class']).addClass('right');
            }
        }
        
        $(".popup_shadow .button_footer ."+obj['class']).unbind('click');
        
        if(typeof(obj.callback) != 'undefined'){
            $(".popup_shadow .button_footer ."+obj['class']).bind('click', obj.callback);
        } else {
            if(typeof(obj.ajax) != 'undefined'){
                $(".popup_shadow .button_footer ."+obj['class']).bind('click', function(){ 
                    if(popup.block_ui === false){
                        popup.original_button_label = $(".popup_shadow .button_footer ."+obj['class']).html();
                        if(obj['class'] == "delete_button"){
                             $(".popup_shadow .button_footer ."+obj['class']).html('<img src="'+baseurl+'images/red_ajax.gif" style="vertical-align:bottom;" alt="loading..." /> loading...');
                        } else {
                             $(".popup_shadow .button_footer ."+obj['class']).html('<img src="'+baseurl+'images/green_ajax.gif" style="vertical-align:bottom;" alt="loading..." /> loading...');
                        }
                        
                        if(typeof obj.ajax.match(/http/) !== 'null') obj.ajax = baseurl+obj.ajax;
                        
                        $.ajax({
                            type: "POST",
                            url: obj.ajax,
                            data: popup.main_data,
                            cache: false,
                            async: true,
                            dataType: "json",
                            success: function(json){
                                $(".popup_shadow .button_footer ."+obj['class']).html(popup.original_button_label);
                                if(typeof(json.error) != 'undefined'){
                                    popup.report_error(json.error, 'error', 4500);
                                } else {
                                    popup.report_success(json.response);
                                }

						        if(typeof(obj.success_callback) != 'undefined') obj.success_callback;
                            },
                            error: function(xhr, status, error){
                                $(".popup_shadow .button_footer ."+obj['class']).html(popup.original_button_label);
                                popup.report_error("<b>Uh-oh, I broke!</b> Please report this to the developers: <br>AJAX error "+error+". Status: "+status);
                            }
                        });
                    }
                    return false; 
                });
            } else {
                $(".popup_shadow .button_footer ."+obj['class']).bind('click', function(){ 
                    popup.hide(); 
                    return false; 
                });
            }
        }
        
        $(".popup_shadow .button_footer ."+obj['class']).show();
    },
    report_error: function(msg, type, time){
        time = typeof(time) != 'undefined' ? time : 25500;
        $(".popup_shadow #popup_notice").addClass('popup_error').html(msg).slideDown(400);
        setTimeout(function(){
            $(".popup_shadow #popup_notice").slideUp(800);
        }, time);
    },
    report_success: function(msg, type, time){
        time = typeof(time) != 'undefined' ? time : 4500;
        $(".popup_shadow #popup_notice").addClass('popup_success').html('<img src="'+baseurl+'images/goodtogo.png" style="vertical-align:middle;"> '+msg).slideDown(400);
        popup.block_ui = true;
        popup.hide(400, time);
    },
    create: function(obj){
        obj = typeof(obj) != 'undefined' ? obj : {};
        obj.data = typeof(obj.data) != 'undefined' ? popup.main_data = obj.data : obj.data = {};
		
		popup.original_width = $(".popup_shadow").css('width');

		if(typeof(obj.width) != 'undefined'){
			$(".popup_shadow").css('width', obj.width);
			var total_padding = 40;
			$(".popup_shadow").css('marginLeft', "-"+parseInt((obj.width+total_padding)/2));
			//$(".popup_shadow").css('top', "-"+parseInt((obj.width+total_padding)/2));
		}
        
        // We want it to be visible to the user first! Lower 15% of viewport
		$(".popup_shadow").css('top', (parseInt($(window).height()*0.15)+parseInt(($('body')[0].scrollTop))));

        if(typeof(obj.title) != 'undefined'){
            $(".popup_shadow .header_box h3").html(obj.title);
        } else {
            $(".popup_shadow .header_box").hide();
        }

        if(typeof(obj.content) != 'undefined'){
            if(typeof(obj.content.ajax) != 'undefined'){
                $(".popup_shadow .popup_content").text('Loading words and personality...').load(obj.content.ajax);
            } else {
                $(".popup_shadow .popup_content").html(obj.content);
            }
        } else {
            $(".popup_shadow .popup_content").html('<strong>Oops!</strong><br><p>A developer forgot to fill me with the notification, be sure to tell someone so I can get fixed please! :)</p>');
        }

		if(typeof(obj.error) != 'undefined') setTimeout(function(){ popup.report_error(obj.error, 'fade'); }, 200);
        if(typeof(obj.success) != 'undefined') setTimeout(function(){ popup.report_success(obj.success, 'fade'); }, 200);

        $(".popup_shadow .button_footer a").hide();

        if(typeof(obj.cancel_button) != 'undefined') obj.cancel_button['class'] = 'cancel_button', popup.create_button(obj.cancel_button);
        if(typeof(obj.confirm_button) != 'undefined') obj.confirm_button['class'] = 'confirm_button',  popup.create_button(obj.confirm_button);
        if(typeof(obj.delete_button) != 'undefined') obj.delete_button['class'] = 'delete_button', popup.create_button(obj.delete_button);
        
        popup.show(300, 100);
    }
}

/**
 * lscache library
 * Copyright (c) 2011, Pamela Fox
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
var local_db=function(){var e="-cacheexpiration";var b=function(){try{return !!localStorage.getItem}catch(f){return false}}();var d=(window.JSON!=null);function a(f){return f+e}function c(){return Math.floor((new Date().getTime())/60000)}return{set:function(m,l,g){if(!b){return}if(typeof l!="string"){if(!d){return}try{l=JSON.stringify(l)}catch(k){return}}try{localStorage.setItem(m,l)}catch(k){if(k.name==="QUOTA_EXCEEDED_ERR"||k.name=="NS_ERROR_DOM_QUOTA_REACHED"){var f,n=[];for(var h=0;h<localStorage.length;h++){f=localStorage.key(h);if(f.indexOf(e)>-1){var o=f.split(e)[0];n.push({key:o,expiration:parseInt(localStorage[f],10)})}}n.sort(function(p,i){return(p.expiration-i.expiration)});for(var h=0,j=Math.min(30,n.length);h<j;h++){localStorage.removeItem(n[h].key);localStorage.removeItem(a(n[h].key))}localStorage.setItem(m,l)}else{return}}if(g){localStorage.setItem(a(m),c()+g)}else{localStorage.removeItem(a(m))}},get:function(f){if(!b){return null}function h(i){if(d){try{var j=JSON.parse(localStorage.getItem(i));return j}catch(k){return localStorage.getItem(i)}}else{return localStorage.getItem(i)}}if(localStorage.getItem(a(f))){var g=parseInt(localStorage.getItem(a(f)),10);if(c()>=g){localStorage.removeItem(f);localStorage.removeItem(a(f));return null}else{return h(f)}}else{if(localStorage.getItem(f)){return h(f)}}return null},remove:function(f){if(!b){return null}localStorage.removeItem(f);localStorage.removeItem(a(f))}}}();