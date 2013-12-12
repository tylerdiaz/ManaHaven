<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        
        /**
         * @author Alexander Farkas
         * v. 1.22
         */
        (function(b){if(!document.defaultView||!document.defaultView.getComputedStyle){var d=b.curCSS;b.curCSS=function(g,e,h){if(e==="background-position"){e="backgroundPosition"}if(e!=="backgroundPosition"||!g.currentStyle||g.currentStyle[e]){return d.apply(this,arguments)}var f=g.style;if(!h&&f&&f[e]){return f[e]}return d(g,"backgroundPositionX",h)+" "+d(g,"backgroundPositionY",h)}}var c=b.fn.animate;b.fn.animate=function(e){if("background-position" in e){e.backgroundPosition=e["background-position"];delete e["background-position"]}if("backgroundPosition" in e){e.backgroundPosition="("+e.backgroundPosition}return c.apply(this,arguments)};function a(f){f=f.replace(/left|top/g,"0px");f=f.replace(/right|bottom/g,"100%");f=f.replace(/([0-9\.]+)(\s|\)|$)/g,"$1px$2");var e=f.match(/(-?[0-9\.]+)(px|\%|em|pt)\s(-?[0-9\.]+)(px|\%|em|pt)/);return[parseFloat(e[1],10),e[2],parseFloat(e[3],10),e[4]]}b.fx.step.backgroundPosition=function(f){if(!f.bgPosReady){var h=b.curCSS(f.elem,"backgroundPosition");if(!h){h="0px 0px"}h=a(h);f.start=[h[0],h[2]];var e=a(f.end);f.end=[e[0],e[2]];f.unit=[e[1],e[3]];f.bgPosReady=true}var g=[];g[0]=((f.end[0]-f.start[0])*f.pos)+f.start[0]+f.unit[0];g[1]=((f.end[1]-f.start[1])*f.pos)+f.start[1]+f.unit[1];f.elem.style.backgroundPosition=g[0]+" "+g[1]}})(jQuery);
        
        // More "slot machine like" animation
        jQuery.extend( jQuery.easing, {
        	easeInOutQuad: function (x, t, b, c, d) {
        		if ((t/=d/2) < 1) return c/2*t*t + b;
        		return -c/2 * ((--t)*(t-2) - 1) + b;
        	}
        });

        $("#spin_fruit").live('click', function(){
            $("#fruit_sprite img, #fruit_sprite").fadeTo(500, 1);
            $("#spin_fruit").html('<img src="/images/ajax/posting_ajax.gif"> fetching').animate({ opacity: 0.5 }).attr('id', 'redeeming_fruit');
            $("#fruit_sprite").animate({ backgroundPosition:"(-768px 0)"}, 5000, "easeInOutQuad", function(){
                $("#fruit_sprite img").fadeOut(500);
                $("#redeeming_fruit").html('Redeem').animate({ opacity: 1 }).attr('id', 'redeem_fruit');
            });
            
            return false;
        });
        
        $("#redeem_fruit").live('click', function(){
            alert('Tyler says: Sorry, I\'ll finish this feature soon! :)');
            
            $("#daily_fruit").fadeOut(600);
            return false;
        })
    });
</script>

<div id="daily_fruit">
    <h5>Your daily fruit!</h5>
    <div id="fruit_sprite"><?php echo image('slot_pointer.png', 'width="32" height="32"') ?></div>
    <?php echo anchor('#', '&larr; Spin', 'class="button" id="spin_fruit"') ?>
</div>
