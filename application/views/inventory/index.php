<div class="sub_world_title">
    <h3 class="left">My Inventory</h3>
    <a href="#" id="back_to_world" class="right">&lsaquo; Back to World</a>
</div>
<script type="text/javascript">

    function avatar_assign_url(src,href,obj){
    	$("#avatar_preview_img").attr('src',src);
    	$("#avatar_loading").css('display','none');
    	var toEquip = !obj.hasClass('equipped');
    	obj.parent('div').children('a').removeClass('equipped');
    	if(!toEquip){
    		obj.removeClass('equipped');
    	} else {
    		obj.addClass('equipped');
    	}
    }

    $(document).ready(function(){

        $(".equip_item").click(function(e){
    		$("#avatar_loading").css('display','block');
    		var url = $(this).attr('href');
    		var obj = $(this);
    		$.get(url+'/ajax/'+Math.random(), {}, function(){
    			var img		= new Image();
    			var src		= "<?=base_url();?>inventory/preview/"+Math.random();
    			img.onLoad	= avatar_assign_url(src, url, obj);
    			img.src		= src;
    		});
    		e.preventDefault();
    	});

    	$("#save_avatar").click(function(e){
    		e.preventDefault();
    		$.get($(this).attr('href')+'/ajax/'+Math.random(),{},function(){
                $("#avatar_box img.left").animate({ opacity:1 }).attr('src', $("#avatar_preview_img").attr('src'));
    		    setTimeout(function(){
                    $("#avatar_box img.left").animate({ opacity:0.3 });
    		    }, 2500);
    		});
    		return false;
    	});

        $("#avatar_box img.left").animate({ opacity:0.3 }, 1500);
    });
</script>
<style type="text/css" media="screen">
    #avatar_preview_img {
        background:white;
        margin:10px 5px 10px;
        padding:5px 2px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 1px 4px 1px #000000;
        -moz-box-shadow: 1px 4px 1px #000000;
        box-shadow: 1px 4px 1px #000000;
        border:2px solid white;
    }
    
    a.equip_item:hover { background:#444; }
    a.equip_item img, a.equip_item { float:left; }
    a.equip_item {
        padding:5px;
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        margin:3px;
        xborder:1px solid #fff;
    }
    a.equipped {
        background:#345A7C !important;
    }
    .pallet {
        overflow:hidden;
        margin-bottom:6px;
    }
    .pallet li {
        list-style:none;
        margin:1px;
        float:left;
    }
    .pallet li a {
        display:block;
        background:red;
        width:17px;
        height:14px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        border:1px solid transparent;
    }
    .pallet li a:hover {
        border:1px solid white;
        -webkit-box-shadow: 0px 0px 2px 1px #000000;
        -moz-box-shadow: 0px 0px 2px 1px #000000;
        box-shadow: 0px 0px 2px 1px #000000;
    }
</style>
<div class="grid_1">
    <img src="<?php echo site_url('inventory/preview/'.time()) ?>" id="avatar_preview_img" alt="">
    <?php echo anchor('inventory/save', 'Save changes', 'class="button mini" id="save_avatar"') ?>
    <br><br>
    <strong>Skin color:</strong><br>
    <ul class="pallet">
        <li><a href="#" style="background:#F5E7D1"></a></li>
        <li><a href="#" style="background:#E2D5C1"></a></li>
        <li><a href="#" style="background:#E2D0AD"></a></li>
        <li><a href="#" style="background:#C2AF7D"></a></li>
        <li><a href="#" style="background:#4D3B17"></a></li>
    </ul>
    <strong>Eye color:</strong>
    <ul class="pallet">
        <li><a href="#" style="background:#FF665E"></a></li>
        <li><a href="#" style="background:#CEFF5E"></a></li>
        <li><a href="#" style="background:#46A8FF"></a></li>
        <li><a href="#" style="background:#C761FF"></a></li>
        <li><a href="#" style="background:#724C00"></a></li>
    </ul>
</div>

<div class="grid_3" style="margin:10px 0;">
    <?php if (count($items) > 0): ?>
        <?php foreach ($items as $tab => $category): ?>
            <?php foreach ($category as $order => $items): ?>
                <span>
                    <?php foreach ($items as $item): ?>
                        <a href="<?php echo site_url('inventory/equip/'.$item['id']) ?>" class="stack equip_item <?php echo $item['equipped'] ?>">
                            <?php echo image('avatar_items/thumbnails/'.$item['thumb']) ?>
                        </a>
                    <?php endforeach ?>
                </span>
            <?php endforeach ?>
        <?php endforeach ?>
    <?php else: ?>    
        <strong>Sorry, we couldn't find any items in your inventory!</strong>
    <?php endif ?>
</div>
