<div id="fb-root"></div>
<div class="grid_x activity_feed" style="outline:1px solid transparent; height:420px">
    
    <!-- Parse notifications -->
    <?php foreach ($notifications as $notification_key => $noticiation): ?>
        <div class="quick_notice"><?php echo $noticiation ?></div>
    <?php endforeach ?>

    <style type="text/css" media="screen">
        #character_block {
            background-color: #1f1f1f;
            background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(31, 31, 31, 1.00)), to(rgba(42, 42, 42, 1.00)));
            background-image: -webkit-linear-gradient(top, rgba(31, 31, 31, 1.00), rgba(42, 42, 42, 1.00));
            background-image: -moz-linear-gradient(top, rgba(31, 31, 31, 1.00), rgba(42, 42, 42, 1.00));
            background-image: -o-linear-gradient(top, rgba(31, 31, 31, 1.00), rgba(42, 42, 42, 1.00));
            background-image: -ms-linear-gradient(top, rgba(31, 31, 31, 1.00), rgba(42, 42, 42, 1.00));
            background-image: linear-gradient(top, rgba(31, 31, 31, 1.00), rgba(42, 42, 42, 1.00));
            filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#1f1f1f', EndColorStr='#2a2a2a');
            border-radius:7px;
            height:180px;
            margin-top:5px;
            color:#ddd;
            border:3px solid #ccc;
        }
        #character_avatar {
            float:left;
        }
        #large_level_progress {
            margin-top:10px;
            font-size:12px;
        }
        #progress_bar_container {
            border:1px solid #7FB03B; 
            background:#070D00; 
            height:35px; 
            margin-top:2px; 
            -webkit-box-shadow: 0px 0px 0px 3px #444444;
            -moz-box-shadow: 0px 0px 0px 3px #444444;
            box-shadow: 0px 0px 0px 3px #444444;
        }
        #progress_bar_container div {
            background:green; width:54%; height:100%; background: #bcca6c; /* Old browsers */
            background: -moz-linear-gradient(top,  #bcca6c 0%, #96bc3c 50%, #7cb111 51%, #a1c545 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bcca6c), color-stop(50%,#96bc3c), color-stop(51%,#7cb111), color-stop(100%,#a1c545)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #bcca6c 0%,#96bc3c 50%,#7cb111 51%,#a1c545 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #bcca6c 0%,#96bc3c 50%,#7cb111 51%,#a1c545 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #bcca6c 0%,#96bc3c 50%,#7cb111 51%,#a1c545 100%); /* IE10+ */
            background: linear-gradient(top,  #bcca6c 0%,#96bc3c 50%,#7cb111 51%,#a1c545 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bcca6c', endColorstr='#a1c545',GradientType=0 ); /* IE6-9 */
        }
    </style>
    
    <div id="character_block">
        <img src="/avatar/quick_preview" height="180" width="140" alt="" id="character_avatar">
        <div style="padding:15px 10px 10px 0; float:left; width:310px">
            <h4 style="border-bottom:1px solid #777">Welcome back, <?php echo $user['username'] ?>!</h4>
            <div id="large_level_progress">
                <div class="clearfix">
                    <strong class="left">Level <?php echo $user['level'] ?></strong>
                    <span class="right"><?php echo $user['exp'] ?>/<?php echo $user['next_level_exp'] ?> (<?php echo percent($user['exp'], $user['next_level_exp']) ?>%)</span>
                </div>
                <div id="progress_bar_container">
                    <div style="width:<?php echo percent($user['exp'], $user['next_level_exp']) ?>%;">
                        
                    </div>
                </div>
            </div>
            
            <div style="margin:27px 5px 0;">
                <div class="right">
                    <?php echo anchor('world', 'Enter the World &rsaquo;', 'class="button"'); ?>
                </div>
            </div>
        </div>
    </div>
    
    <div style="background:#222; padding:10px 15px; border-radius:6px; -moz-border-radius:4px; -webkit-border-radius:4px; margin:5px 0; overflow:hidden">
        <h4 style="color:#aaa; width:110px; float:left;">Time left to the epic update:</h4>
        <div style="background:#111; float:right; width:290px; margin-left:10px; padding:10px; color:#fff;">
            <div id="time_left" style="font-size:17px;">loading time...</div>
        </div>
        <br clear="all" />
        <div style="border-top:2px solid #444; font-size:12px; margin-top:5px; padding-top:5px; color:#aaa; text-align:center;">
            We will have an estimated 6-10 hour downtime to set up the updates.
        </div>
    </div>

    <div style="margin-top:10px; background:#F4F9E7; color:#597231; padding:10px 13px; border:1px solid #C9D3B3; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px;">
        In the past 24 hours, we've had over: <br>
        <ul style="margin-left:20px">
            <li>{total_battles} battles</li>
            <li>{total_posts} topic posts</li>
        </ul>
        <div style="border-top:1px solid #D0DDB7; color:#819860; font-size:11px; margin-top:15px; padding:5px 5px 0;">Psst... this is just Pixeltweak experimenting with some features. It might vanish/be-replaced next time you give us a visit.</div>
    </div>
</div>
<style type="text/css" media="screen">
#active_topics {
    background:url(/global/styles/images/dashboard/active_topics.jpg)no-repeat left top;
    padding-top:40px;
}
#my_buddies {
    background:url(/global/styles/images/dashboard/my_buddies.png)no-repeat left top;
    padding:44px 0 0 6px;
    margin-bottom:25px;
}
</style>
<script type="text/javascript">
    /* http://keith-wood.name/countdown.html
       Countdown for jQuery v1.5.9.
       Written by Keith Wood (kbwood{at}iinet.com.au) January 2008.
       Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and 
       MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses. 
       Please attribute the author if you use it. */
    (function($){function Countdown(){this.regional=[];this.regional['']={labels:['Years','Months','Weeks','Days','Hours','Minutes','Seconds'],labels1:['Year','Month','Week','Day','Hour','Minute','Second'],compactLabels:['y','m','w','d'],whichLabels:null,timeSeparator:':',isRTL:false};this._defaults={until:null,since:null,timezone:null,serverSync:null,format:'dHMS',layout:'',compact:false,significant:0,description:'',expiryUrl:'',expiryText:'',alwaysExpire:false,onExpiry:null,onTick:null,tickInterval:1};$.extend(this._defaults,this.regional['']);this._serverSyncs=[]}var w='countdown';var Y=0;var O=1;var W=2;var D=3;var H=4;var M=5;var S=6;$.extend(Countdown.prototype,{markerClassName:'hasCountdown',_timer:setInterval(function(){$.countdown._updateTargets()},980),_timerTargets:[],setDefaults:function(a){this._resetExtraLabels(this._defaults,a);extendRemove(this._defaults,a||{})},UTCDate:function(a,b,c,e,f,g,h,i){if(typeof b=='object'&&b.constructor==Date){i=b.getMilliseconds();h=b.getSeconds();g=b.getMinutes();f=b.getHours();e=b.getDate();c=b.getMonth();b=b.getFullYear()}var d=new Date();d.setUTCFullYear(b);d.setUTCDate(1);d.setUTCMonth(c||0);d.setUTCDate(e||1);d.setUTCHours(f||0);d.setUTCMinutes((g||0)-(Math.abs(a)<30?a*60:a));d.setUTCSeconds(h||0);d.setUTCMilliseconds(i||0);return d},periodsToSeconds:function(a){return a[0]*31557600+a[1]*2629800+a[2]*604800+a[3]*86400+a[4]*3600+a[5]*60+a[6]},_settingsCountdown:function(a,b){if(!b){return $.countdown._defaults}var c=$.data(a,w);return(b=='all'?c.options:c.options[b])},_attachCountdown:function(a,b){var c=$(a);if(c.hasClass(this.markerClassName)){return}c.addClass(this.markerClassName);var d={options:$.extend({},b),_periods:[0,0,0,0,0,0,0]};$.data(a,w,d);this._changeCountdown(a)},_addTarget:function(a){if(!this._hasTarget(a)){this._timerTargets.push(a)}},_hasTarget:function(a){return($.inArray(a,this._timerTargets)>-1)},_removeTarget:function(b){this._timerTargets=$.map(this._timerTargets,function(a){return(a==b?null:a)})},_updateTargets:function(){for(var i=this._timerTargets.length-1;i>=0;i--){this._updateCountdown(this._timerTargets[i])}},_updateCountdown:function(a,b){var c=$(a);b=b||$.data(a,w);if(!b){return}c.html(this._generateHTML(b));c[(this._get(b,'isRTL')?'add':'remove')+'Class']('countdown_rtl');var d=this._get(b,'onTick');if(d){var e=b._hold!='lap'?b._periods:this._calculatePeriods(b,b._show,this._get(b,'significant'),new Date());var f=this._get(b,'tickInterval');if(f==1||this.periodsToSeconds(e)%f==0){d.apply(a,[e])}}var g=b._hold!='pause'&&(b._since?b._now.getTime()<b._since.getTime():b._now.getTime()>=b._until.getTime());if(g&&!b._expiring){b._expiring=true;if(this._hasTarget(a)||this._get(b,'alwaysExpire')){this._removeTarget(a);var h=this._get(b,'onExpiry');if(h){h.apply(a,[])}var i=this._get(b,'expiryText');if(i){var j=this._get(b,'layout');b.options.layout=i;this._updateCountdown(a,b);b.options.layout=j}var k=this._get(b,'expiryUrl');if(k){window.location=k}}b._expiring=false}else if(b._hold=='pause'){this._removeTarget(a)}$.data(a,w,b)},_changeCountdown:function(a,b,c){b=b||{};if(typeof b=='string'){var d=b;b={};b[d]=c}var e=$.data(a,w);if(e){this._resetExtraLabels(e.options,b);extendRemove(e.options,b);this._adjustSettings(a,e);$.data(a,w,e);var f=new Date();if((e._since&&e._since<f)||(e._until&&e._until>f)){this._addTarget(a)}this._updateCountdown(a,e)}},_resetExtraLabels:function(a,b){var c=false;for(var n in b){if(n!='whichLabels'&&n.match(/[Ll]abels/)){c=true;break}}if(c){for(var n in a){if(n.match(/[Ll]abels[0-9]/)){a[n]=null}}}},_adjustSettings:function(a,b){var c;var d=this._get(b,'serverSync');var e=0;var f=null;for(var i=0;i<this._serverSyncs.length;i++){if(this._serverSyncs[i][0]==d){f=this._serverSyncs[i][1];break}}if(f!=null){e=(d?f:0);c=new Date()}else{var g=(d?d.apply(a,[]):null);c=new Date();e=(g?c.getTime()-g.getTime():0);this._serverSyncs.push([d,e])}var h=this._get(b,'timezone');h=(h==null?-c.getTimezoneOffset():h);b._since=this._get(b,'since');if(b._since!=null){b._since=this.UTCDate(h,this._determineTime(b._since,null));if(b._since&&e){b._since.setMilliseconds(b._since.getMilliseconds()+e)}}b._until=this.UTCDate(h,this._determineTime(this._get(b,'until'),c));if(e){b._until.setMilliseconds(b._until.getMilliseconds()+e)}b._show=this._determineShow(b)},_destroyCountdown:function(a){var b=$(a);if(!b.hasClass(this.markerClassName)){return}this._removeTarget(a);b.removeClass(this.markerClassName).empty();$.removeData(a,w)},_pauseCountdown:function(a){this._hold(a,'pause')},_lapCountdown:function(a){this._hold(a,'lap')},_resumeCountdown:function(a){this._hold(a,null)},_hold:function(a,b){var c=$.data(a,w);if(c){if(c._hold=='pause'&&!b){c._periods=c._savePeriods;var d=(c._since?'-':'+');c[c._since?'_since':'_until']=this._determineTime(d+c._periods[0]+'y'+d+c._periods[1]+'o'+d+c._periods[2]+'w'+d+c._periods[3]+'d'+d+c._periods[4]+'h'+d+c._periods[5]+'m'+d+c._periods[6]+'s');this._addTarget(a)}c._hold=b;c._savePeriods=(b=='pause'?c._periods:null);$.data(a,w,c);this._updateCountdown(a,c)}},_getTimesCountdown:function(a){var b=$.data(a,w);return(!b?null:(!b._hold?b._periods:this._calculatePeriods(b,b._show,this._get(b,'significant'),new Date())))},_get:function(a,b){return(a.options[b]!=null?a.options[b]:$.countdown._defaults[b])},_determineTime:function(k,l){var m=function(a){var b=new Date();b.setTime(b.getTime()+a*1000);return b};var n=function(a){a=a.toLowerCase();var b=new Date();var c=b.getFullYear();var d=b.getMonth();var e=b.getDate();var f=b.getHours();var g=b.getMinutes();var h=b.getSeconds();var i=/([+-]?[0-9]+)\s*(s|m|h|d|w|o|y)?/g;var j=i.exec(a);while(j){switch(j[2]||'s'){case's':h+=parseInt(j[1],10);break;case'm':g+=parseInt(j[1],10);break;case'h':f+=parseInt(j[1],10);break;case'd':e+=parseInt(j[1],10);break;case'w':e+=parseInt(j[1],10)*7;break;case'o':d+=parseInt(j[1],10);e=Math.min(e,$.countdown._getDaysInMonth(c,d));break;case'y':c+=parseInt(j[1],10);e=Math.min(e,$.countdown._getDaysInMonth(c,d));break}j=i.exec(a)}return new Date(c,d,e,f,g,h,0)};var o=(k==null?l:(typeof k=='string'?n(k):(typeof k=='number'?m(k):k)));if(o)o.setMilliseconds(0);return o},_getDaysInMonth:function(a,b){return 32-new Date(a,b,32).getDate()},_normalLabels:function(a){return a},_generateHTML:function(c){var d=this._get(c,'significant');c._periods=(c._hold?c._periods:this._calculatePeriods(c,c._show,d,new Date()));var e=false;var f=0;var g=d;var h=$.extend({},c._show);for(var i=Y;i<=S;i++){e|=(c._show[i]=='?'&&c._periods[i]>0);h[i]=(c._show[i]=='?'&&!e?null:c._show[i]);f+=(h[i]?1:0);g-=(c._periods[i]>0?1:0)}var j=[false,false,false,false,false,false,false];for(var i=S;i>=Y;i--){if(c._show[i]){if(c._periods[i]){j[i]=true}else{j[i]=g>0;g--}}}var k=this._get(c,'compact');var l=this._get(c,'layout');var m=(k?this._get(c,'compactLabels'):this._get(c,'labels'));var n=this._get(c,'whichLabels')||this._normalLabels;var o=this._get(c,'timeSeparator');var p=this._get(c,'description')||'';var q=function(a){var b=$.countdown._get(c,'compactLabels'+n(c._periods[a]));return(h[a]?c._periods[a]+(b?b[a]:m[a])+' ':'')};var r=function(a){var b=$.countdown._get(c,'labels'+n(c._periods[a]));return((!d&&h[a])||(d&&j[a])?'<span class="countdown_section"><span class="countdown_amount">'+c._periods[a]+'</span><br/>'+(b?b[a]:m[a])+'</span>':'')};return(l?this._buildLayout(c,h,l,k,d,j):((k?'<span class="countdown_row countdown_amount'+(c._hold?' countdown_holding':'')+'">'+q(Y)+q(O)+q(W)+q(D)+(h[H]?this._minDigits(c._periods[H],2):'')+(h[M]?(h[H]?o:'')+this._minDigits(c._periods[M],2):'')+(h[S]?(h[H]||h[M]?o:'')+this._minDigits(c._periods[S],2):''):'<span class="countdown_row countdown_show'+(d||f)+(c._hold?' countdown_holding':'')+'">'+r(Y)+r(O)+r(W)+r(D)+r(H)+r(M)+r(S))+'</span>'+(p?'<span class="countdown_row countdown_descr">'+p+'</span>':'')))},_buildLayout:function(c,d,e,f,g,h){var j=this._get(c,(f?'compactLabels':'labels'));var k=this._get(c,'whichLabels')||this._normalLabels;var l=function(a){return($.countdown._get(c,(f?'compactLabels':'labels')+k(c._periods[a]))||j)[a]};var m=function(a,b){return Math.floor(a/b)%10};var o={desc:this._get(c,'description'),sep:this._get(c,'timeSeparator'),yl:l(Y),yn:c._periods[Y],ynn:this._minDigits(c._periods[Y],2),ynnn:this._minDigits(c._periods[Y],3),y1:m(c._periods[Y],1),y10:m(c._periods[Y],10),y100:m(c._periods[Y],100),y1000:m(c._periods[Y],1000),ol:l(O),on:c._periods[O],onn:this._minDigits(c._periods[O],2),onnn:this._minDigits(c._periods[O],3),o1:m(c._periods[O],1),o10:m(c._periods[O],10),o100:m(c._periods[O],100),o1000:m(c._periods[O],1000),wl:l(W),wn:c._periods[W],wnn:this._minDigits(c._periods[W],2),wnnn:this._minDigits(c._periods[W],3),w1:m(c._periods[W],1),w10:m(c._periods[W],10),w100:m(c._periods[W],100),w1000:m(c._periods[W],1000),dl:l(D),dn:c._periods[D],dnn:this._minDigits(c._periods[D],2),dnnn:this._minDigits(c._periods[D],3),d1:m(c._periods[D],1),d10:m(c._periods[D],10),d100:m(c._periods[D],100),d1000:m(c._periods[D],1000),hl:l(H),hn:c._periods[H],hnn:this._minDigits(c._periods[H],2),hnnn:this._minDigits(c._periods[H],3),h1:m(c._periods[H],1),h10:m(c._periods[H],10),h100:m(c._periods[H],100),h1000:m(c._periods[H],1000),ml:l(M),mn:c._periods[M],mnn:this._minDigits(c._periods[M],2),mnnn:this._minDigits(c._periods[M],3),m1:m(c._periods[M],1),m10:m(c._periods[M],10),m100:m(c._periods[M],100),m1000:m(c._periods[M],1000),sl:l(S),sn:c._periods[S],snn:this._minDigits(c._periods[S],2),snnn:this._minDigits(c._periods[S],3),s1:m(c._periods[S],1),s10:m(c._periods[S],10),s100:m(c._periods[S],100),s1000:m(c._periods[S],1000)};var p=e;for(var i=Y;i<=S;i++){var q='yowdhms'.charAt(i);var r=new RegExp('\\{'+q+'<\\}(.*)\\{'+q+'>\\}','g');p=p.replace(r,((!g&&d[i])||(g&&h[i])?'$1':''))}$.each(o,function(n,v){var a=new RegExp('\\{'+n+'\\}','g');p=p.replace(a,v)});return p},_minDigits:function(a,b){a=''+a;if(a.length>=b){return a}a='0000000000'+a;return a.substr(a.length-b)},_determineShow:function(a){var b=this._get(a,'format');var c=[];c[Y]=(b.match('y')?'?':(b.match('Y')?'!':null));c[O]=(b.match('o')?'?':(b.match('O')?'!':null));c[W]=(b.match('w')?'?':(b.match('W')?'!':null));c[D]=(b.match('d')?'?':(b.match('D')?'!':null));c[H]=(b.match('h')?'?':(b.match('H')?'!':null));c[M]=(b.match('m')?'?':(b.match('M')?'!':null));c[S]=(b.match('s')?'?':(b.match('S')?'!':null));return c},_calculatePeriods:function(c,d,e,f){c._now=f;c._now.setMilliseconds(0);var g=new Date(c._now.getTime());if(c._since){if(f.getTime()<c._since.getTime()){c._now=f=g}else{f=c._since}}else{g.setTime(c._until.getTime());if(f.getTime()>c._until.getTime()){c._now=f=g}}var h=[0,0,0,0,0,0,0];if(d[Y]||d[O]){var i=$.countdown._getDaysInMonth(f.getFullYear(),f.getMonth());var j=$.countdown._getDaysInMonth(g.getFullYear(),g.getMonth());var k=(g.getDate()==f.getDate()||(g.getDate()>=Math.min(i,j)&&f.getDate()>=Math.min(i,j)));var l=function(a){return(a.getHours()*60+a.getMinutes())*60+a.getSeconds()};var m=Math.max(0,(g.getFullYear()-f.getFullYear())*12+g.getMonth()-f.getMonth()+((g.getDate()<f.getDate()&&!k)||(k&&l(g)<l(f))?-1:0));h[Y]=(d[Y]?Math.floor(m/12):0);h[O]=(d[O]?m-h[Y]*12:0);f=new Date(f.getTime());var n=(f.getDate()==i);var o=$.countdown._getDaysInMonth(f.getFullYear()+h[Y],f.getMonth()+h[O]);if(f.getDate()>o){f.setDate(o)}f.setFullYear(f.getFullYear()+h[Y]);f.setMonth(f.getMonth()+h[O]);if(n){f.setDate(o)}}var p=Math.floor((g.getTime()-f.getTime())/1000);var q=function(a,b){h[a]=(d[a]?Math.floor(p/b):0);p-=h[a]*b};q(W,604800);q(D,86400);q(H,3600);q(M,60);q(S,1);if(p>0&&!c._since){var r=[1,12,4.3482,7,24,60,60];var s=S;var t=1;for(var u=S;u>=Y;u--){if(d[u]){if(h[s]>=t){h[s]=0;p=1}if(p>0){h[u]++;p=0;s=u;t=1}}t*=r[u]}}if(e){for(var u=Y;u<=S;u++){if(e&&h[u]){e--}else if(!e){h[u]=0}}}return h}});function extendRemove(a,b){$.extend(a,b);for(var c in b){if(b[c]==null){a[c]=null}}return a}$.fn.countdown=function(a){var b=Array.prototype.slice.call(arguments,1);if(a=='getTimes'||a=='settings'){return $.countdown['_'+a+'Countdown'].apply($.countdown,[this[0]].concat(b))}return this.each(function(){if(typeof a=='string'){$.countdown['_'+a+'Countdown'].apply($.countdown,[this].concat(b))}else{$.countdown._attachCountdown(this,a)}})};$.countdown=new Countdown()})(jQuery);
    
    <?php
        date_default_timezone_set('UTC');
        $total_timer = (mktime(0, 0, 0, 3, 25, 2012));
        $start_timer = time();
    ?>

    $(document).ready(function(){
        
        var total_seconds = <?php echo $total_timer ?>;
        var total_time = 0;
        
        start_skill_timer(<?php echo $total_timer ?>, <?php echo $start_timer ?>);
        
        function start_skill_timer(param_total_time, bonus_start_time) {
            if (typeof bonus_start_time == 'undefined') bonus_start_time = 0;
            total_time = param_total_time;
            var skill_time_left = new Date();
        
            skill_time_left.setSeconds(skill_time_left.getSeconds() + (total_time - bonus_start_time));
            $('#time_left').countdown({
                until: skill_time_left,
                onExpiry: skillShow,
                onTick: skillCount
            });
        }
        
        function skillShow(){
            redirect('/');
        }
        
        function skillCount(periods) {
            var zero = 0;
            if(periods[6] > 9) zero = "";
            $("#time_left").html(periods[3]+" days, "+periods[4]+" hours, "+periods[5]+":"+zero+""+periods[6]+" left!");
        }
    
    });
        

</script>
<div class="grid_y widget_sidebar">
    <div class="widget clearfix" id="my_buddies">
        <?php echo anchor('friends', 'View All &raquo;', 'class="right action_link" style="margin-top:-36px;"') ?>

        <style type="text/css">
            .friend_thumbnail {
                margin:3px -4px 3px 0;
                display:block;
                float:left;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-transition: all 400ms ease;
                -moz-transition: all 400ms ease;
                -o-transition: all 400ms ease;
                transition: all 400ms ease;
            }
            .friend_thumbnail:hover {
                background:#A7D8FD;
                display:block;
                float:left;
                opacity:1;
            }
            .friend_offline { opacity:0.4; }
        </style>

        <?php if (count($my_friends) > 0): ?>
            <?php foreach ($my_friends as $friend): ?>
                <?php echo anchor('profile/'.urlencode($friend['username']), image('avatars/thumbnails/'.$friend['user_id'].'.gif', 'class="feed_icon"'), 'title="'.$friend['username'].'" class="friend_thumbnail '.($friend['last_activity'] > time()-900 ? 'friend_online' : 'friend_offline').'"') ?>
            <?php endforeach ?>
        <?php elseif ($this->system->userdata['facebook_id'] == 0): ?>
            <?php $this->load->view('widgets/facebook_connect'); ?>
        <?php else: ?>
            <div class="empty_widget">
                <p>You haven't added any friends just yet!</p>
                <?php echo anchor('friends', 'Add a friend', 'class="button"') ?>
            </div>
        <?php endif ?>
    </div>
    <div class="clearfix" id="active_topics">
        <ul class="link_list small_text">
            <?php foreach($latest_topics as $key => $topic): ?>
                <li id="feed_topic_<?php echo $key ?>">
                    <?php echo anchor('community/topic/'.$topic['topic_id'].'/'.(floor(($topic['total_replies']-1)/12)*12).'#'.$topic['last_post'], $topic['title']) ?><br />
                    <span class="topic_data"><?php echo image('mini_chat.gif')?> <?php echo $topic['total_replies'] ?> &mdash; <?php echo image('mini_watch.gif')?> <?php echo human_time($topic['last_post_time']) ?> by <?php echo $topic['last_post_by'] ?></span>
                </li>
            <? endforeach;?>
        </ul>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        
        var topic_posts_memory = {}, latest_topic_stream;
        
        function reformat_live_topic(topic_number, json_data){
            var feed_target = $("#feed_topic_"+topic_number);
            feed_target.find('a').html(json_data['topic_title']).attr('href', json_data['link_location'])
            feed_target.find('.topic_data').html('<img src="/images/mini_chat.gif" alt=""> '+json_data['total_replies']+' &mdash; <img src="/images/mini_watch.gif" alt=""> '+json_data['timestamp']+' by '+json_data['last_poster']);
            
            if(typeof topic_posts_memory[json_data['topic_title']] == "undefined" || topic_posts_memory[json_data['topic_title']] != json_data['total_replies']){
                // if timestamp is greater
                if(json_data['raw_timestamp'] > topic_posts_memory[json_data['topic_title']]){
                    feed_target.animate({ backgroundColor: "#ffb"}, 200);
                    setTimeout(function(){
                        feed_target.animate({ backgroundColor: "#fff"}, 500);
                    }, 300)
                }
                topic_posts_memory[json_data['topic_title']] = json_data['total_replies'];
            }
        }
        
        function start_loading_stream(){
            latest_topic_stream = setInterval(function(){
                $.ajax({
                    type: "GET",
                    url: "/home/get_latest_topics/4",
                    data: { json: 1 },
                    cache: false,
                    async: true,
                    dataType: "json",
                    timeout: 1500,
                    success: function(json_topics){
                        var topic_i = 0;
                        for (var i=0; i < json_topics.length; i++) {
                            setTimeout(function(){
                                reformat_live_topic(topic_i, json_topics[topic_i]);
                                topic_i++;       
                            }, i*400)
                        };
                    }
                });
            }, 10000);
        }
        
        start_loading_stream();
        
    });
</script>