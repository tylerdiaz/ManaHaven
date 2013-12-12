<div style="background:#333; color:#ccc; overflow:hidden; ">
    
<div class="grid_4" style="font-size:18px; text-align:center; padding:155px 0; color:#777; text-shadow:1px 1px 0 #000">
    Artwork and words coming soon...
</div>    
<script>
$(document).ready(function()
{
    var timers = {};
	$('#signup_username, #email, #signup_beta_key').bind('keyup blur', function() 
	{
	    var this_id = $(this).attr("id"),
	    	this_value = $(this).val();
	    	    
	    if(this_value.length > 1){
	        clearTimeout(timers[this_id]);
    	
    	    timers[this_id] = setTimeout(function()
    		{		    		
                $("#"+this_id).removeClass('bad_to_go').removeClass('good_to_go');
                $.ajax({
                  	type: "POST",
                  	url: baseurl+"auth/signup_verify/",
                  	data: "type="+this_id+"&value="+this_value,
                  	cache: false,
                  	timeout: 2000,
                  	dataType: "json",
                  	success:function(data)
                  	{
                  		if(data.response == "success"){
                  			$("#"+this_id).addClass('good_to_go').removeClass('bad_to_go');
                  		} else {
                  			$("#"+this_id).addClass('bad_to_go').removeClass('good_to_go');
                  		}
                	}
                });
            }, 400);
	    } else {
	        $("#"+this_id).removeClass('good_to_go').removeClass('bad_to_go');
	    }
	});

	$('#signup_password').bind('keyup blur', function() 
	{
		var this_id = $(this).attr("id"),
			this_value = $(this).val();
		
	    if(this_value.length > 1)
	    {
		    clearTimeout(pw_timer);
		    var pw_timer = setTimeout(function()
		    {
			    if(this_value.length > 5) {
				    $("#"+this_id).addClass('good_to_go').removeClass('bad_to_go');
			    } else {
				    $("#"+this_id).addClass('bad_to_go').removeClass('good_to_go');
			    }
		    }, 600);
		} else {
    	    $("#"+this_id).removeClass('good_to_go').removeClass('bad_to_go');
    	}
	});	
	
	$("#signup_button").live('click', function(){
	   $.ajax({
	       type: "POST",
	       url: baseurl+"auth/signup",
	       data: $("#signup_form_bones").serialize(),
	       cache: false,
	       async: true,
	       dataType: "json",
	       success: function(json){
	           if(json.errors != undefined){
	               alert(json.errors);
               	   return false;
	           } else {
	               $("#signup_step1").fadeOut(400, function(){
	                   $("#signup_step2").fadeIn(300);
	                   $("#signup_block #large_header").text('Last step, we promise!');
	                   $("#signup_block #sub_header").text('Customize your adventurer.');
	               });
	               // redirect('home/index/new_user');
	           }
	       },
	       error: function(xhr, status, error){
	           alert("AJAX error "+error+". Status: "+status);
           	   return false;
	       }
	   });
	   return false;
	})
});
</script>

<style type="text/css" media="screen">
    #signup_form li {
        list-style:none;
        margin:7px 0;
    }
    #signup_form li label {
        width:70px;
        float:left;
        text-align:right;
        color:#CCA05C;
        font-size:13px;
        padding:4px 5px 0 0;
        cursor:pointer;
    }
    #signup_username, #email, #signup_password, #signup_beta_key {
        border:2px solid #AF9266;
        padding:4px 5px;
        font-size:15px;
        width:170px;
        background:#eee;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
    }
    #signup_username:focus, #email:focus, #signup_password:focus, #signup_beta_key:focus {
        border-color:#E5C798;
        background-color:#fff;
        outline:none;
    }
    #signup_beta_key {
        background:#eee url(<?php echo site_url('images/key.png') ?>) no-repeat 6px 4px;
        padding-left:28px;
        color:#958C19;
        font-weight:bold;
        
    }
    #signup_button, #complete_button {
        float:right;
        margin-top:5px;
        border:1px solid #CC9810;
        font-size:15px;
        font-weight:bold;
        font-family:Helvetica;
        padding:7px 0;
        width:175px;
        text-align:center;
        background: #fceabb; /* Old browsers */
        background: -moz-linear-gradient(top, #fceabb 0%, #fccd4d 50%, #f8b500 51%, #fbdf93 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fceabb), color-stop(50%,#fccd4d), color-stop(51%,#f8b500), color-stop(100%,#fbdf93)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #fceabb 0%,#fccd4d 50%,#f8b500 51%,#fbdf93 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #fceabb 0%,#fccd4d 50%,#f8b500 51%,#fbdf93 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #fceabb 0%,#fccd4d 50%,#f8b500 51%,#fbdf93 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fceabb', endColorstr='#fbdf93',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #fceabb 0%,#fccd4d 50%,#f8b500 51%,#fbdf93 100%); /* W3C */
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        color:#754607;
        text-shadow:1px 1px 0 #EFE4BD;
        -webkit-box-shadow: inset 0px 0px 4px 0px #ffffff;
        -moz-box-shadow: inset 0px 0px 4px 0px #ffffff;
        box-shadow: inset 0px 0px 4px 0px #ffffff;
        margin-bottom:10px;
    }
    #signup_button:hover, #complete_button:hover {
        background: #fdf1d1; /* Old browsers */
        background: -moz-linear-gradient(top, #fdf1d1 0%, #fddd85 50%, #ffcf4b 51%, #fce9b4 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fdf1d1), color-stop(50%,#fddd85), color-stop(51%,#ffcf4b), color-stop(100%,#fce9b4)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #fdf1d1 0%,#fddd85 50%,#ffcf4b 51%,#fce9b4 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #fdf1d1 0%,#fddd85 50%,#ffcf4b 51%,#fce9b4 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #fdf1d1 0%,#fddd85 50%,#ffcf4b 51%,#fce9b4 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fdf1d1', endColorstr='#fce9b4',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #fdf1d1 0%,#fddd85 50%,#ffcf4b 51%,#fce9b4 100%); /* W3C */
        cursor:pointer;
    }
    #signup_button:active, #complete_button:active {
        background: #c99000; /* Old browsers */
        background: -moz-linear-gradient(top, #c99000 1%, #e5ca87 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#c99000), color-stop(100%,#e5ca87)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #c99000 1%,#e5ca87 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #c99000 1%,#e5ca87 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #c99000 1%,#e5ca87 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c99000', endColorstr='#e5ca87',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #c99000 1%,#e5ca87 100%); /* W3C */
    }
    #signup_block {
        padding:20px 15px 25px 10px; background: -moz-linear-gradient(top, rgba(0,0,0,0.19) 0%, rgba(0,0,0,0.45) 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,0,0,0.19)), color-stop(100%,rgba(0,0,0,0.45))); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, rgba(0,0,0,0.19) 0%,rgba(0,0,0,0.45) 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, rgba(0,0,0,0.19) 0%,rgba(0,0,0,0.45) 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, rgba(0,0,0,0.19) 0%,rgba(0,0,0,0.45) 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#30000000', endColorstr='#73000000',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, rgba(0,0,0,0.19) 0%,rgba(0,0,0,0.45) 100%); /* W3C */ margin:0 0 0 -15px;
        min-height:300px;
    }
    #signup_form li .bad_to_go {
        background-color:#FDF0DA;
        border-color:#D20006;
    }
    #signup_form li .good_to_go {
        background-color:#F0F8EC;
        border-color:green;
    }
</style>
<div class="grid_2" id="signup_block">
    <h3 style="padding:0 10px 0; line-height:1.4" id="large_header">Join the adventure!</h3>
    <h5 style="border-bottom:1px solid #666; padding:0 10px 10px; margin-bottom:10px; font-weight:normal;" id="sub_header">Free to play forever. Start playing instantly.</h5>
    <div id="signup_step1" style="display:none">
        <form id="signup_form_bones">
            <ul id="signup_form">
                <li>
                    <label for="signup_username">Username:</label>
                    <input type="text" name="username" id="signup_username">
                </li>
                <li>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">
                </li>
                <li>
                    <label for="signup_password">Password:</label>
                    <input type="password" name="password" id="signup_password">
                </li>
                <li>
                    <label for="signup_beta_key">Beta Key:</label>
                    <input type="text" name="key" id="signup_beta_key">
                </li>
                <li>
                    <button type="submit" id="signup_button">Start my character &rsaquo;</button>
                    <span style="float:right; font-size:12px; width:175px; color:#888">By signing up you agree to our terms of service & private policy</span>
                </li>
            </ul>
        </form>        
    </div>
    <style type="text/css" media="screen">
        #classes { 
            margin-top:5px;
            overflow:hidden; 
            background: rgb(69,72,77); /* Old browsers */
            background: -moz-linear-gradient(top, rgba(69,72,77,1) 0%, rgba(0,0,0,1) 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(69,72,77,1)), color-stop(100%,rgba(0,0,0,1))); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, rgba(69,72,77,1) 0%,rgba(0,0,0,1) 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, rgba(69,72,77,1) 0%,rgba(0,0,0,1) 100%); /* Opera11.10+ */
            background: -ms-linear-gradient(top, rgba(69,72,77,1) 0%,rgba(0,0,0,1) 100%); /* IE10+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#45484d', endColorstr='#000000',GradientType=0 ); /* IE6-9 */
            background: linear-gradient(top, rgba(69,72,77,1) 0%,rgba(0,0,0,1) 100%); /* W3C */
            -webkit-box-shadow: 0px 2px 3px 0px #000000;
            -moz-box-shadow: 0px 2px 3px 0px #000000;
            box-shadow: 0px 2px 3px 0px #000000;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
        }
        #classes li {
            float:left;
            margin:2px 3px 2px 2px;
        }
        #classes li a {
            padding:2px 3px;
            display:block;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
        }
        #classes li a:hover {
            background: -moz-linear-gradient(top, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,255,0.15)), color-stop(70%,rgba(255,255,255,0))); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, rgba(255,255,255,0.15) 0%,rgba(255,255,255,0) 70%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, rgba(255,255,255,0.15) 0%,rgba(255,255,255,0) 70%); /* Opera11.10+ */
            background: -ms-linear-gradient(top, rgba(255,255,255,0.15) 0%,rgba(255,255,255,0) 70%); /* IE10+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#26ffffff', endColorstr='#00ffffff',GradientType=0 ); /* IE6-9 */
            background: linear-gradient(top, rgba(255,255,255,0.15) 0%,rgba(255,255,255,0) 70%); /* W3C */
        }
       #classes li a.current {
            background: -moz-linear-gradient(top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0) 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,0,0,0.65)), color-stop(100%,rgba(0,0,0,0))); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, rgba(0,0,0,0.65) 0%,rgba(0,0,0,0) 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, rgba(0,0,0,0.65) 0%,rgba(0,0,0,0) 100%); /* Opera11.10+ */
            background: -ms-linear-gradient(top, rgba(0,0,0,0.65) 0%,rgba(0,0,0,0) 100%); /* IE10+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a6000000', endColorstr='#00000000',GradientType=0 ); /* IE6-9 */
            background: linear-gradient(top, rgba(0,0,0,0.65) 0%,rgba(0,0,0,0) 100%); /* W3C */
        }
        #traits {
            margin:10px 35px;
        }
        #traits li {
            float:left;
            padding:0 10px;
            text-align:center;
            border-right:1px solid #555;
            border-left:1px solid #000;
        }
        #traits li:last-child { border-right:none }
        #traits li:first-child { border-left:none }
        #traits li h6 { border-bottom:1px dotted #666; }
        #traits li h4 { color:#eee; font-size:16px; text-shadow:1px 1px 0 #000; }
        #chosen_subtitle {
            margin-top:10px;
        }
        #chosen_class {
            font-family:Georgia;
            text-transform:uppercase;
            font-weight:normal;
        }
        #chosen_subtitle, #chosen_class {
            text-align:center;
        }
        
        .character_traits li {
            float:left;
            width:105px;
            text-align:center;
            padding:4px 6px;
            height:17px;
            font-size:12px;
            background:orange;
            margin:3px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
        }
    </style>
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function(){
            $("#classes").tabs("div#class_contents > div");
            
            $("#gender_female, #gender_male").change(function(){
                var gender = $(this).val();
                $("#classes").children('li').each(function(){
                    var type_class = $(this).attr('id');
                    $(this).children('a').children('img').attr('src', baseurl+'images/signup/'+gender+'/'+type_class+'.png');
                });
            });
        });
    </script>
    <div id="signup_step2" style="display:block">
        <div style="text-align:center;">I am a... <input type="radio" name="gender" value="male" id="gender_male" checked="yes" /> <label for="gender_male">Male</label> <input type="radio" name="gender" value="female" id="gender_female" /> <label for="gender_female">Female</label><br></div>
        <ul id="classes">
            <li id="archer"><a href="#" class="active"><?php echo image('signup/male/archer.png') ?></a></li>
            <li id="warrior"><a href="#"><?php echo image('signup/male/warrior.png') ?></a></li>
            <li id="wizard"><a href="#"><?php echo image('signup/male/wizard.png') ?></a></li>
        </ul>
        <h5 id="chosen_subtitle">I want to be a...</h5>
        <div id="class_contents">
            <div>
                <h3 id="chosen_class">Adventurer</h3>
                <ul class="character_traits">
                    <li style="background:#724F11">Very strong</li>
                    <li style="background:#69542D">Has high HP</li>
                    <li style="background:#7E6E50">Good for beginners</li>
                    <li style="background:#8F8672">Not very fast</li>
                </ul>
            </div>
            <div>
                <h3 id="chosen_class">Archer</h3>
                <ul class="character_traits">
                    <li style="background:#2E6D0D">Very fast</li>
                    <li style="background:#386326; font-size:11px; line-height:17px">Shoots air monsters</li>
                    <li style="background:#497848">Heals with time</li>
                    <li style="background:#73896A">Low Defense</li>
                </ul>
            </div>
            <div>
                <h3 id="chosen_class">Wizard</h3>
                <ul class="character_traits">
                    <li style="background:#115369">Very strong spells</li>
                    <li style="background:#274E60; font-size:11px; line-height:17px">Attacks air monsters</li>
                    <li style="background:#3D6477">Can heal others</li>
                    <li style="background:#6A8087">Not a lot of HP</li>
                </ul>
            </div>
        </div>
        <button type="submit" id="complete_button" style="margin:20px 40px 0 0">Start playing &rsaquo;</button>
    </div>
</div>

</div>
