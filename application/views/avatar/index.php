<script type="text/javascript">

    $(document).ready(function(){
        
        var max_weight = <?php echo $this->system->userdata['max_weight'] ?>, current_weight = <?php echo $total_weight ?>,
            avatar_gender = <?php echo (strtolower($this->system->userdata['gender']) == "male" ? 1 : 0) ?>,
            avatar_items_path = "/images/avatar_items/small_images/";
            canvas = $('#avatar_display')[0].getContext("2d"),
            image_buffer = {};


        function main_equip_item(e){
    		var url = $(this).attr('href');
    		var obj = $(this);
    		var check_weight = obj.parent().parent().hasClass('items');

            if(Modernizr.canvas){
                $.getJSON(url+'/ajax/?json=1', function(json){
                    merge_images(json, false);
                    equip_item(obj, check_weight);
        		});             
            } else {
        		$.get(url+'/ajax/'+Math.random(), function(json){
        			var img		= new Image();
        			var src		= "/avatar/preview/"+Math.random()+'/0/1';
        			img.onLoad	= avatar_assign_url(src, url, obj, check_weight);
        			img.src		= src;
        		});
            }

    		e.preventDefault();
        }

        function merge_images(items, completed, first_time_loading){
            first_time_loading = (typeof first_time_loading == 'undefined' ? false : first_time_loading)
            var item_images = {};
            var total_items_to_load = items.length;
            var items_loaded = 0;

            // Clear the canvas
            if(completed === true) canvas.clearRect(0, 0, 140, 180);
            if(completed === true && first_time_loading === true) $("#saving_avatar").fadeOut(200);

            // Let's start looping through the items
            for (var item = items.length - 1; item >= 0; item--){
                if(completed === true){
                    canvas.drawImage(image_buffer[item], 0, 0);
                } else {
                    item_images[item] = new Image();   // Create new img element
                    item_images[item].src = avatar_items_path+items[item]; // Set source path

                    image_buffer[item] = item_images[item];

                    item_images[item].onload = function(){
                        items_loaded++;
                        // All images loaded?
                        if(items_loaded >= total_items_to_load) merge_images(items, true, first_time_loading);
                    };
                }
            };
        }

        function bind_tooltips(e){
            $(".items .equip_item").tooltip({
                offset: [10, 4],
                position: "bottom center",
                onBeforeShow: function(){
                    
                    if( ! $("#hide_tooltips").is(':checked')){
                        var item_attr = item_data[$(this.getTrigger()).attr('data_id')];
                        var item_html = '<div class="arrow_up"></div>'+'<div class="clearfix"><h4>'+item_attr.name+'</h4> <span class="common right"></span></div><div class="stats">';
                        var total_bonuses = 0;
                        item_html += 'Weight: <strong>'+item_attr.weight+' lb'+(item_attr.weight == 1 ? '' : 's')+'</strong><br> <span class="left">Bonuses:</span>';

                        $.each(item_attr.attr, function(attribute, value){
                            if(value > 0){
                                item_html += '<div class="c_'+attribute.toLowerCase()+'">'+(value > 0 ? '+' : '-')+''+value+' '+attribute+'</div>';
                                total_bonuses++;
                            }
                        })

                        if(total_bonuses == 0) item_html += "<em>&nbsp;none...</em>";
                        item_html += '</div>';
                        $(".tooltip").html(item_html);
                    } else {
                        return false;
                    }
                },
                effect: 'slide',
                predelay: 200,
                slideInSpeed: 150,
                slideOutSpeed: 300
            });
        }

        function over_encumbered_item(e){
            alert('This item is too heavy to equip!');
            e.preventDefault();
        }

        function equip_item(obj, check_weight){
            var toEquip = !obj.hasClass('equipped');

            if(check_weight === true){
                obj.parent().children('a.equipped').each(function(){
                    $("#carrying_weight").decrease(item_data[$(this).attr('data_id')]['weight']);
                    current_weight -= item_data[$(this).attr('data_id')]['weight'];
                    $(this).removeClass('equipped');
                })
            } else {
                obj.parent().children('a').removeClass('equipped');
            }

        	if( ! toEquip){
        		obj.removeClass('equipped');
        	} else {
        		obj.addClass('equipped');
                if(check_weight === true) $("#carrying_weight").increase(item_data[obj.attr('data_id')]['weight'])
                current_weight += item_data[obj.attr('data_id')]['weight'];
        	}

            var over_encumbered_items = $(".over_encumbered");
            for (var i = over_encumbered_items.length - 1; i >= 0; i--){
                var this_obj = $(over_encumbered_items[i]);

                if(parseInt(this_obj.attr('data_weight'))+current_weight <= max_weight){
                    this_obj.removeClass('over_encumbered').unbind('click').bind('click', main_equip_item);
                }
            };
            
            $(".items .equip_item").not('.equipped').filter(function(){
                return ((parseInt($(this).attr("data_weight"))+current_weight) > max_weight);
            }).addClass('over_encumbered').unbind('click').bind('click', over_encumbered_item);
        }

        function avatar_assign_url(src,href,obj, check_weight){
        	$("#avatar_preview_img").attr('src',src);
        	setTimeout(function() {
        	    $("#avatar_preview_img").animate({ 
        	        borderBottomColor: "#FDCF77", 
        	        borderLeftColor: "#FDCF77", 
        	        borderRightColor: "#FDCF77", 
        	        borderTopColor: "#FDCF77" 
        	    }, 200).animate({ 
        	        borderBottomColor: "white", 
        	        borderLeftColor: "white", 
        	        borderRightColor: "white", 
        	        borderTopColor: "white" 
        	    }, 1000);
        	}, 500);

        	equip_item(obj, check_weight);
        }


        
        
        if(Modernizr.canvas){
            $("#saving_avatar").fadeIn(200);
        } else {
            $("#avatar_display").remove();
            $("#saving_avatar").after($('<img src="<?php echo site_url('avatar/preview/'.time().'/'.($double_img_size ? '0/1' : '0')) ?>" id="avatar_preview_img" width="140" height="180" />'))
        }
                
        $.getJSON(baseurl+"avatar/preview/0/0/1?json=1", function(json){
            merge_images(json, false, true);
        });
        
        $(".items .equip_item, .appearance .hair .equip_item").bind('click', main_equip_item);
        
        $(".appearance div:not(.hair) .equip_item").click(function(e){
    		var url = $(this).attr('href');
    		var obj = $(this);
            var equipped_obj_parent = obj.parent().find('.equipped');
            
            if(equipped_obj_parent.not(this).length || (equipped_obj_parent.not(this).length == 0 && equipped_obj_parent.length == 0)){
                if(Modernizr.canvas){
                    $.get(url+'/ajax/'+Math.random(), function(){
                        $.getJSON(baseurl+"avatar/preview/0/0/1?json=1", function(json){
                            merge_images(json, false);
                            equip_item(obj, false);
                        });
            		});             
                } else {
            		$.get(url+'/ajax/'+Math.random(), function(json){
            			var img		= new Image();
            			var src		= "/avatar/preview/"+Math.random()+'/0/1';
            			img.onLoad	= avatar_assign_url(src, url, obj, false);
            			img.src		= src;
            		});                
                }
            } else {
                // You cannot deequip a base item!
            }
    		
    		e.preventDefault();
    	});

        $(".usable_item").bind('click', function(){
            var item_obj = $(this);
            popup.create({
                title: "Are you sure you'd like to use this item?",
                content: "<p>Just to be sure, are you certain you'd like to use this item?</p>",
                cancel_button: { label: 'Close' },
                confirm_button: { label: 'Use item &rsaquo;', callback: function(){
                    $.ajax({
                        type: "POST",
                        url: item_obj.attr('href'),
                        dataType: "json",
                        success: function(json){
                            if(typeof(json.error) != 'undefined'){
                                popup.report_error(json.error, 'error', 4500);
                            } else {
                                var amount_obj = item_obj.find('span');
                                if(amount_obj.length > 0){
                                    amount_obj.html('x'+(parseInt(amount_obj.html().substr(1))-1));
                                    popup.report_success(json.response);
                                } else {
                                    popup.report_success(json.response);
                                    item_obj.fadeOut(100);
                                }
                            }
                        },
                        error: function(xhr, status, error){
                            $(".popup_shadow .button_footer ."+obj['class']).html(popup.original_button_label);
                            popup.report_error("<b>Uh-oh, I broke!</b> Please report this to the developers: <br>AJAX error "+error+". Status: "+status);
                        }
                    });
                    return false;
                }}
            }); // end popup
            return false;
        });
        
    	$("#save_avatar").click(function(e){
    		e.preventDefault();
            $("#saving_avatar").fadeIn(400);

    		$.get($(this).attr('href'),{},function(){
                $("#avatar_box img.left").animate({ opacity:1 }).attr('src', '/images/avatars/<?php echo $this->session->userdata('id') ?>.gif?'+Math.random());
                $("#saving_avatar").fadeOut(400);
                $(".success").slideToggle(500, function(){
                    setTimeout(function() { 
                        $(".success").slideToggle(1200); 
                    }, 1500);
                });
                
    		    setTimeout(function(){
                    $("#avatar_box img.left").animate({ opacity:0.3 });
    		    }, 2500);
    		});
    		
    		return false;
    	});

        $("#avatar_box img.left").animate({ opacity:0.3 }, 1500);        
        $("ul.tabs").tabs("div.tabs_content > div");
        
        
        bind_tooltips();
        
        $("#swap_gender").bind('click', function(){
            var gender_link = $(this);
            $.get(gender_link.attr('href'), function(){
                if(avatar_gender == 1){
                     avatar_gender = 0;
                     $("#gender_select").text('Female');
                     $("#swap_gender").text('Change to Male');
                } else {
                    avatar_gender = 1;
                    $("#gender_select").text('Male');
                    $("#swap_gender").text('Change to Female');
                }
                if(Modernizr.canvas){
                    $.getJSON(baseurl+"avatar/preview/0/0/1?json=1", function(json){
                        merge_images(json, false);
                        equip_item(obj, false);
                    });
                } else {
        			var img		= new Image();
        			var src		= "/avatar/preview/"+Math.random()+'/0/1';
        			img.onLoad	= avatar_assign_url(src, url, obj, false);
        			img.src		= src;
                }
            });
            return false;
        });
        
        var regenerate_cache = setInterval(function(){
            $.get("/avatar/update_cache_keys");
        }, 240000) // 4 minutes
        
        
        // Check which are too heavy to equip!
        $(".items .equip_item").not('.equipped').filter(function(){
            return ((parseInt($(this).attr("data_weight"))+current_weight) > max_weight);
        }).addClass('over_encumbered').unbind('click').bind('click', over_encumbered_item);
    });
</script>

<style type="text/css" media="screen">
.c_defense, .c_energy, .c_exp, .c_hp, .c_attack, .c_speed {
    padding:1px 4px;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    color:white;
    line-height:1.1;
    margin:1px;
    float:left;
}
.c_defense { background:#073C73; }
.c_energy { background:#9FAAE4; color:#0F1D64; }
.c_exp { background:#6B6C06; }
.c_hp { background:#EBCDCE; color:#6E0003; }
.c_attack { background:#7E4F13; }
.c_speed { background:#41790D; }

    #avatar_preview_img {
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        border:2px solid white;
        margin-bottom:6px;
    }
    
    a.equip_item:hover { background:#E7EDDB; }
    a.equip_item img, a.equip_item { float:left; }
    a.usable_item img, a.usable_item { float:left; }
    a.unusable_item img, a.unusable_item { float:left; }
    a.equip_item, a.usable_item, a.unusable_item {
        padding:5px;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        margin:3px;
        border:1px solid #fff;
        position:relative;
    }
    a.equip_item span, a.usable_item span, a.unusable_item span {
        position:absolute;
        bottom:3px;
        right:3px;
        background:black;
        line-height:1;
        padding:2px 3px;
        font-size:12px;
        color:white;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        opacity:0.6;
        -webkit-transition: all 300ms linear;
        -moz-transition: all 300ms linear;
        -o-transition: all 300ms linear;
        transition: all 300ms linear;
    }
    a.equip_item:hover { text-decoration:none; }
    a.equip_item:hover span { opacity:0.1; }
    a.unusable_item { background:#ccc; border:1px solid #999; opacity:0.4;}
    .items a.equipped {
        background:#D6E7B5 !important;
        border:1px solid #C2D5A2;
    }
    .appearance a.equipped {
        background:#D3DCDF !important;
        border:1px solid #C4CDD0;
    }
    a.usable_item:hover {
        background:#ffa;
        border:1px solid #ee8;
    }
    a.unusable_item:hover {
        text-decoration:none;
        cursor:default;
    }
    .tabs { list-style:none; overflow:hidden; background:#231C0B; padding:3px 0 10px; -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
    -moz-border-radius-bottomright: 0px;
    -moz-border-radius-bottomleft: 0px;
    -webkit-border-radius: 5px 5px 0px 0px;
    border-radius: 5px 5px 0px 0px; }
    .tabs li a { float:left; background:#867656; color:white; display:block; padding:6px 21px; font-size:15px; margin:0 2px 0 3px;
        -moz-border-radius-topleft: 6px;
        -moz-border-radius-topright: 6px;
        -moz-border-radius-bottomright: 0px;
        -moz-border-radius-bottomleft: 0px;
        -webkit-border-radius: 6px 6px 0px 0px;
        border-radius: 6px 6px 0px 0px; 
    }
    .tabs li a.current {
        background:#564321;
        
    }
    .tabs_content {
        background:#564321;
        overflow:hidden;
        margin:-10px 0 0;
        padding:6px;
        -moz-border-radius-topleft: 0px;
        -moz-border-radius-topright: 6px;
        -moz-border-radius-bottomright: 6px;
        -moz-border-radius-bottomleft: 6px;
        -webkit-border-radius: 0px 6px 6px 6px;
        border-radius: 0px 6px 6px 6px;
    }
    .tabs_content > div {
        background:white;
        overflow:hidden;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        padding:4px;
    }
    .quick_notice {
        font-size:12px;
        color:#eee;
        line-height:32px;
        margin-right:7px;
        opacity:0;
    }
    .tooltip {
    	display:none;
    	background:black;
    	font-size:12px;
    	height:70px;
    	width:160px;
    	padding:25px;
    	color:#fff;	
        display:none;
    	background:black;
    	font-size:12px;
    	width:165px;
        padding:5px;
    	-webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-box-shadow: 0px 1px 4px 0px rgba(0, 0, 0, 0.5);
        -moz-box-shadow: 0px 1px 4px 0px rgba(0, 0, 0, 0.5);
        box-shadow: 0px 1px 4px 0px rgba(0, 0, 0, 0.5);
    }
    .tooltip h4 {
        float:left;
        color:#ccc;
        margin:4px 0 0 2px;
    }
    .stats {
        border-top:1px solid #222;
        padding-top:2px;
        margin-top:2px;
        color:#999 ;
        font-size:11px;
    }
    .common {
        display:block;
        background:#444;
        border:1px solid #666;
        width:9px;
        height:9px;
        margin:7px 2px 0 0;
    	-webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
    }
    div.arrow_up {
        width:0px; 
        height:0px; 
        border-left:5px solid transparent;  /* left arrow slant */
        border-right:5px solid transparent; /* right arrow slant */
        border-bottom:5px solid #000; /* bottom, add background color here */
        margin:-10px 0 0 75px;
        font-size:0px;
        line-height:0;
    }
    #avatar_display { margin-bottom:10px }
    .over_encumbered_arrow {
        position:absolute;
        left:0;
        top:30px;
        display:none;
    }
    a.over_encumbered {
        background-color: #e1e1e1;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgba(225, 225, 225, 1.00)), to(rgba(242, 242, 242, 1.00)));
        background-image: -webkit-linear-gradient(top, rgba(225, 225, 225, 1.00), rgba(242, 242, 242, 1.00));
        background-image: -moz-linear-gradient(top, rgba(225, 225, 225, 1.00), rgba(242, 242, 242, 1.00));
        background-image: -o-linear-gradient(top, rgba(225, 225, 225, 1.00), rgba(242, 242, 242, 1.00));
        background-image: -ms-linear-gradient(top, rgba(225, 225, 225, 1.00), rgba(242, 242, 242, 1.00));
        background-image: linear-gradient(top, rgba(225, 225, 225, 1.00), rgba(242, 242, 242, 1.00));
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#e1e1e1', EndColorStr='#f2f2f2');
        border-color: #ccc;
        opacity: 0.7;
    }
    a.over_encumbered .over_encumbered_arrow {
        display:block;
    }
</style>
<div style="position:relative; float:left; width:150px">
    <div style="z-index:999; position:absolute; top:0; left:0; width:140px; height:180px; background:rgba(255, 255, 255, 0.9) url(/images/ajax/signup_ajax.gif)no-repeat center center; opacity:1; display:none" id="saving_avatar">
        
    </div>
    <canvas id="avatar_display" width="140" height="180"></canvas>
    <?php echo anchor('avatar/save?token={scrty_token}', 'Save changes', 'class="button" id="save_avatar"') ?>
    <br><br>
    <span style="font-size:12px; color:#777;">Your avatar is a <b id="gender_select"><?php echo ucfirst($this->system->userdata['gender']); ?></b></span><br>
    <?php if (strtolower($this->system->userdata['gender']) == "male"): ?>
        <?php echo anchor('avatar/swap_gender/', 'Change to Female', 'id="swap_gender"') ?>
    <?php else: ?>
        <?php echo anchor('avatar/swap_gender/', 'Change to Male', 'id="swap_gender"') ?>
    <?php endif ?>
    <br><br>
    <input type="checkbox" id="hide_tooltips" /> <label for="hide_tooltips" style="color:#777; font-size:12px;">Disable item Tooltips</label>
</div>

<div style="position:relative; float:left; width:620px">
    <div class="success" style="background:#D4EBB6; color:#305106; margin:2px 0; padding:5px 8px; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; display:none;">
        <?php echo image('success.png') ?> Your avatar has been saved!
    </div>
    <ul class="tabs">
        <li><a href="#">Items</a></li>
        <li><a href="#">Appearance</a></li>
        <li><a href="#">Usable items</a></li>
        <li class="right" style="margin-right:5px; font-size:18px; line-height:32px; color:#D1CCBC"><?php echo image('small_bag.png', 'title="Weight"') ?> <span id="carrying_weight"><?php echo $total_weight ?></span>/<?php echo $this->system->userdata['max_weight'] ?></li>
    </ul>
    <div class="tabs_content">
        <div class="items">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $tab => $category): ?>
                    <?php foreach ($category as $order => $items): ?>
                        <span>
                            <?php foreach ($items as $item): ?>
                                <a href="<?php echo site_url('avatar/equip/'.$item['id']) ?>" class="stack equip_item <?php echo $item['equipped'] ?>" title="Loading..." data_id="<?=$item['id']?>" data_weight="<?php echo $item['weight'] ?>">
                                  <?php echo image('avatar_items/thumbnails/'.$item['thumb'], 'width="38" height="38"') ?>
                                    <?php echo ($item['num'] > 1 ? '<span>x'.$item['num'].'</span>' : '') ?>
                                    <?php echo image('over_encumbered_arrow.png', 'width="16" height="16" class="over_encumbered_arrow"') ?>
                                </a>
                            <?php endforeach ?>
                        </span>
                    <?php endforeach ?>
                <?php endforeach ?>
            <?php else: ?>
                <strong>Your inventory is empty!</strong>
            <?php endif ?>
        </div>
        <div class="appearance">
            <?php if (count($appearance) > 0): ?>
                <?php foreach ($appearance as $tab => $category): ?>
                    <?php foreach ($category as $order => $items): ?>
                        <div style="float:left; width:70px; border-right:2px solid #ccc; padding:17px 10px; text-align:right; color:#666; margin-bottom:5px;">
                            <h4><?php echo (substr($order, 5) == "Mid" ? "Hair" : substr($order, 5)) ?></h4>
                        </div>
                        <div style="float:left; width:450px; padding:0 5px; margin-bottom:5px;" class="<?php echo strtolower(substr($order, 5) == "Mid" ? "Hair" : substr($order, 5)) ?>">
                            <?php foreach ($items as $item): ?>
                                <a href="<?php echo site_url('avatar/equip/'.$item['id']) ?>" class="stack equip_item <?php echo $item['equipped'] ?>">
                                    <?php echo image('avatar_items/thumbnails/'.$item['thumb'], 'width="38" height="38"') ?>
                                </a>
                            <?php endforeach ?>
                        </div>
                    <?php endforeach ?>
                <?php endforeach ?>
            <?php else: ?>    
                <strong></strong>
            <?php endif ?>        
        </div>
        <div class="usable_items">
            <?php if (count($usable_items) > 0): ?>
                <?php foreach ($usable_items as $item): ?>
                    <a href="<?php echo ($item['usable'] ? site_url('avatar/use_item/'.$item['item_id']) : '#') ?>" class="stack <?php echo ($item['usable'] ? 'usable_item' : 'unusable_item') ?>" title="<?php echo $item['name'] ?>" data_id="<?=$item['item_id']?>">
                      <?php echo image('items/'.$item['thumb'], 'width="38" height="38"') ?>
                        <?php echo ($item['num'] > 1 ? '<span>x'.$item['num'].'</span>' : '') ?>
                    </a>
                <?php endforeach ?>
            <?php else: ?>
                <strong>You own no usable items! You should go buy some in <?php echo anchor('world', 'the shop'); ?></strong>
            <?php endif ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var item_data = <?php echo json_encode($item_data, JSON_NUMERIC_CHECK) ?>;
</script>
