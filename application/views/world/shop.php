<!-- http://manahaven.dev/avatar/preview_item/22877 -->

<div class="sub_world_title">
    <h3 class="left">The Local Market</h3>
    <a href="#" id="back_to_world" class="right">&lsaquo; Back to World</a>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("#merchant_items li a").live('click', function(event){
            window.location.hash = $(this).attr('href');
            if($(".introduction").length > 0){
                $(".introduction").remove();
                $("ul#merchant_items").tabs("div#item_description > div", { history: true });            
            }
        })
    });
</script>
<style type="text/css">
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

#merchant_items {
    overflow:hidden;
    list-style:none;
    float:left;
    width:210px;
    padding:2px;
}

#merchant_items li a {
    background:#3f3f3f;
    overflow:hidden;
    padding:3px;
    border:1px solid #555;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow:inset 0px 1px 6px 0px #1f1f1f;
    -moz-box-shadow:inset 0px 1px 6px 0px #1f1f1f;
    box-shadow:inset 0px 1px 6px 0px #1f1f1f;
    margin:3px;
    float:left;
}
#merchant_items li.deal_of_the_day a {
    background:#463C02;
    overflow:hidden;
    padding:3px;
    border:1px solid #71630A;
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow:inset 0px 1px 6px 0px #1E1A01;
    -moz-box-shadow:inset 0px 1px 6px 0px #1E1A01;
    box-shadow:inset 0px 1px 6px 0px #1E1A01;
    margin:3px;
    float:left;
}
#merchant_items li a:hover {
    background:#0D282F;
    border:1px solid #24444C;
    -webkit-box-shadow:inset 0px 1px 6px 0px #1E1A01;
    -moz-box-shadow:inset 0px 1px 6px 0px #1E1A01;
    box-shadow:inset 0px 1px 6px 0px #1E1A01;
}
#item_description {
    float:right;
    width:275px;
    padding:8px 0 8px 14px;
    border-left:1px solid #444;
    background:rgba(0, 0, 0, 0.6);
    min-height:330px;
}
#item_description > div {
    display:none;
}
#item_description > div.introduction {
    display:block;
}
#merchant_items li a.current {
    background:#CDDFEA;
    border-color:#76B7FD;
    -webkit-box-shadow:inset 0px 1px 6px 0px #3E6B80;
    -moz-box-shadow:inset 0px 1px 6px 0px #3E6B80;
    box-shadow:inset 0px 1px 6px 0px #3E6B80;
}
#merchant_items li h5 {
    padding:3px 3px 4px;
    margin-bottom:2px;
    border-bottom:1px solid #555;
}
.main_thumbnail {
    width:38px;
    height:38px;
}
.preview_bg {
    
}
.toggle_raw_preview, .toggled_raw_preview {
    position:absolute; 
    right:5px; 
    top:5px; 
    display:block; 
    width:18px; 
    height:16px; 
    background:transparent url(/images/eye_view.png)no-repeat center center;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    border:1px solid #ccc;
    opacity:0.3;
}
.toggle_raw_preview:hover {
    opacity:1;
}
.toggled_raw_preview {
    background-color:black;
    opacity:1;
}
</style>
<ul id="merchant_items">
   <li><h5>Today's items</h5></li>
   <?php foreach ($items[0] as $item): ?>
       <li>
           <a href="#<?php echo md5($item['name']) ?>">
            <?php if (isset($item['disposable'])): ?>
                <?php echo image('items/'.$item['thumb'], 'class="main_thumbnail"') ?>
            <?php else: ?>
                <?php echo image('avatar_items/thumbnails/'.$item['thumb'], 'class="main_thumbnail"') ?>
            <?php endif ?>
           </a>
       </li>
   <?php endforeach ?>
    <li><br clear="all" /><br></li>
    <li><h5>Permanent items</h5></li>
    <?php $items[1] = array_reverse($items[1]); ?>
    <?php foreach ($items[1] as $item): ?>
        <li>
            <a href="#<?php echo md5($item['name']) ?>">
             <?php if (isset($item['disposable'])): ?>
                 <?php echo image('items/'.$item['thumb'], 'class="main_thumbnail"') ?>
             <?php else: ?>
                 <?php echo image('avatar_items/thumbnails/'.$item['thumb'], 'class="main_thumbnail"') ?>
             <?php endif ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>
<div id="item_description">
    <div class="introduction">
        Welcome to my shop! :)  
    </div>
    <?php foreach ($items as $item_group): ?>
        <?php foreach ($item_group as $item): ?>
            <div>
            <?php if ( ! isset($item['disposable'])): ?>
                <h4 style="padding:4px 0; border-bottom:2px solid #333; margin-bottom:8px;"><?php echo $item['name'] ?></h4>
                <div style="position:relative; width:140px; height:180px; float:left; float:left; margin-right:15px; background:#85662E url(/images/preview_shadow.png); border:4px solid #62420a; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px;">
                    <a href="#" class="toggle_raw_preview" data_item_id="<?php echo $item['item_id'] ?>"></a>
                    <canvas id="c_<?php echo $item['item_id'] ?>" data_item_id="<?php echo $item['item_id'] ?>" alt="" width="140" height="180" class="preview_bg"></canvas>
                </div>
                Price: <?php echo $item['price'] ?> <?php echo image('coins.png') ?><br>
                Weight: <?php echo $item['weight'] ?>lbs<br>
                <div style="overflow:hidden">
                    <span class="left" style="font-size:12px;">Bonuses:&nbsp;</span> 
                    <?php echo ($item['attack_bonus'] > 0 ? '<div class="c_attack">+'.$item['attack_bonus'].' Attack</div>' : '') ?>
                    <?php echo ($item['agility_bonus'] > 0 ? '<div class="c_speed">+'.$item['agility_bonus'].' Speed</div>' : '') ?>
                    <?php echo ($item['defense_bonus'] > 0 ? '<div class="c_defense">+'.$item['defense_bonus'].' Defense</div>' : '') ?>
                    <?php echo ($item['max_hp_bonus'] > 0 ? '<div class="c_hp">+'.$item['max_hp_bonus'].' HP</div>' : '') ?>
                    <?php echo ($item['exp_bonus'] > 0 ? '<div class="c_exp">+'.$item['exp_bonus'].' EXP</div>' : '') ?>
                </div>
                <br>
                <?php if ($this->system->userdata['gold'] >= $item['price']): ?>
                    <a href="#" class="button purchase_shop_item" data_id="<?php echo $item['shop_item_id'] ?>" data_item_name="<?php echo $item['name'] ?>">Purchase</a>
                <?php else: ?>
                    <div style="font-size:12px; background:#B30005; color:white; padding:4px 8px; text-align:center; float:left; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;">Not enough gold</div>
                <?php endif ?>
            <?php else: ?>
                <h4 style="padding:4px 0; border-bottom:2px solid #333; margin-bottom:8px;"><?php echo $item['name'] ?></h4>
                <div style="width:140px; height:180px; background:#fff url(/images/preview_shine_item.jpg); border:4px solid #0B3E59; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; float:left; margin-right:10px;">
                    <?php echo image('items/large/'.$item['thumb'], 'style="margin:65px 0 0 30px;"') ?>
                </div>
                <p style="font-size:12px; margin-bottom:5px"><b>Effect:</b><br><?php echo $item['description'] ?></p>
                Price: <?php echo $item['price'] ?> <?php echo image('coins.png') ?><br>
                <br>
                <?php if ($this->system->userdata['gold'] >= $item['price']): ?>
                    <a href="#" class="button purchase_shop_item" data_id="<?php echo $item['shop_item_id'] ?>" data_item_name="<?php echo $item['name'] ?>">Purchase</a>
                <?php else: ?>
                    <div style="font-size:12px; background:#B30005; color:white; padding:4px 8px; text-align:center; float:left; -webkit-border-radius: 3px;
                    -moz-border-radius: 3px;
                    border-radius: 3px;">
                        Not enough gold
                    </div>
                <?php endif ?>
            <?php endif ?>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
</div>

<!--[if lte IE 8]> <?php echo script('excanvas.js') ?> <![endif]-->
<script type="text/javascript">
	function merge_images(canvas, items){
	    // Let's start looping through the items
	    for (var item = items.length - 1; item >= 0; item--){
	        console.log('Adding!');
	        canvas.drawImage(items[item], 0, 0);
	    }
	}
	
	var shop_data = {};
	var avatar_items_path = "/images/avatar_items/small_images/"
	
	var preview_cache = local_db.get('preview_shop_item_data');
	if(preview_cache){
	    $.each(preview_cache, function(key, items){
	        parse_item_preview(key, items);
	    });
	} else {
		var item_ids = [];
		$('#item_description div > canvas').each(function(){
		    item_ids.push($(this).attr('data_item_id'));
		});

	    setTimeout(function(){
	        $.ajax({
	            type: "POST",
	            url: "/avatar/preview_shop_items/",
	            data: { items: item_ids },
	            dataType: "json",
	            success: function(json){
	                local_db.set('preview_shop_item_data', json, 5);
	                $.each(json, function(key, items){
	                    parse_item_preview(key, items);
	                });
	            },
	        });
	    }, 200);
	}
	
	function parse_item_preview(key, items){
	    shop_data[key] = {};
	    shop_data[key]['image_buffer'] = [];
	    shop_data[key]['item_images'] = {};
	    shop_data[key]['total_items_to_load'] = items.length;
	    shop_data[key]['items_loaded'] = 0;
	    shop_data[key]['canvas'] = $('#c_'+key)[0].getContext("2d");
	
	    // Let's start looping through the items
	    for (var item = items.length - 1; item >= 0; item--){
	        shop_data[key]['item_images'][item] = new Image();   // Create new img element
	        shop_data[key]['item_images'][item].src = avatar_items_path+items[item]; // Set source path
	        shop_data[key]['item_images'][item].className = key; // Set source path
	    
	        shop_data[key]['image_buffer'].push(shop_data[key]['item_images'][item]);
	    
	        shop_data[key]['item_images'][item].onload = function(){
	            var item_key = this.className;
	            shop_data[item_key]['items_loaded']++;
	            // All images loaded?
	            if(shop_data[item_key]['items_loaded'] >= shop_data[item_key]['total_items_to_load']){
	                var buffer = shop_data[item_key]['image_buffer'];
	                for (var item = shop_data[item_key]['image_buffer'].length - 1; item >= 0; item--){
	                   shop_data[item_key]['canvas'].drawImage(shop_data[item_key]['image_buffer'][item], 0, 0);
	                }
	            }
	        };
	    };
	}
    
</script>