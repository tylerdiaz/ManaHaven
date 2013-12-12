<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        var dropArea = document.getElementById("upload_socket");

        // filesUpload.addEventListener("change", function () {
        //  traverseFiles(this.files);
        // }, false);
        // 
        dropArea.addEventListener("dragleave", function (evt) {
            var target = evt.target;

            if (target && target === dropArea) this.className = "";

            evt.preventDefault();
            evt.stopPropagation();
        }, false);

        dropArea.addEventListener("dragenter", function (evt) {
            this.className = "hover_drop";
            evt.preventDefault();
            evt.stopPropagation();
        }, false);
        
        dropArea.addEventListener("dragover", function (evt) {
            evt.preventDefault();
            evt.stopPropagation();
        }, false);

        dropArea.addEventListener("drop", function (evt) {
            $.each(evt.dataTransfer.files, function(){
                console.log(this)
            });
            this.className = "";
            evt.preventDefault();
            evt.stopPropagation();
        }, false);
        
        var ordered_layers = <?php echo json_encode($ordered_layers) ?>;
        var unordered_layers = <?php echo json_encode($layers) ?>;
        
        function order_layers(order_bool){
            if(order_bool == true){
                var new_options = "";
                $.each(ordered_layers, function(key, layer){
                    new_options += '<option value="'+layer.id+'">'+layer.name+'</option>';
                })
                
                $("#main_layer").html(new_options);
                $(".piece_list > li").each(function(){
                    $(this).find('#piece_layer').html(new_options)
                })
            } else {
                var new_options = "";
                $.each(unordered_layers, function(key, layer){
                    new_options += '<option value="'+layer.id+'">'+layer.name+'</option>';
                })
                
                $(".piece_list > li").each(function(){
                    $(this).find('#piece_layer').html(new_options)
                })
                
                $("#main_layer").html(new_options);
            }
        }
        
        $("#lbn").change(function(){
            order_layers($(this).is(':checked'));
        })
    });
</script>
<style type="text/css" media="screen">
    .hover_drop {
        background:yellow;
    }
</style>
<div style="padding:10px; background:#E7F4E2; color:#2D5B18; -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px; overflow:hidden">
<h4 style="border-bottom:1px solid #C9DDC0; margin-bottom:5px;">Main item data:</h4>
<form action="<?php echo site_url('pixelshack/item/create_base_item') ?>" method="post" accept-charset="utf-8" id="create_main_item_form">
    <div style="height:100px; width:280px; float:left">
        <ul class="form">
            <li>
                <label for="item_name" style="padding:0; margin:0; text-align:left;">Item name:</label><br>
                <input type="text" name="item_name" class="text_input" id="item_name">
            </li>
            <li>
                <textarea style="font-family:arial; font-size:12px; width:200px; padding:3px; height:40px;" placeholder="Small item Description..." name="item_description"></textarea>
            </li>
        </ul>
    </div>
    
    <div style="height:100px; width:280px; float:left">
        <ul class="form">
            <li>
                <label for="gender">Gender:</label>
                <select name="gender" id="gender">
                	<?php foreach($gender as $value => $text){ ?>
                    <option value="<?=$value;?>"><?=$text;?></option>
                    <?php } ?>
                </select>
            </li>
            <li>
                <label for="class">Class:</label>
                <select name="class" id="class">
                	<?php foreach($class as $value => $text){ ?>
                    <option value="<?=$value;?>"><?=$text;?></option>
                    <?php } ?>
                </select>
            </li>
            <li>
                <label for="main_layer">Layer:</label>
                <select name="main_layer" id="main_layer">
                    <?php foreach ($layers as $layer): ?>
                        <option value="<?php echo $layer['id'] ?>"><?php echo $layer['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </li>
            <li>
                <label for="weight">Weight:</label>
                <select name="weight" id="weight">
                	<?php foreach($weight as $value => $text){ ?>
                    <option value="<?=$value;?>"><?=$text;?></option>
                    <?php } ?>
                </select>
            </li>
        </ul>
    </div>
</form>
</div>
<style type="text/css">
    .piece_list > li {
        background:#f3f3f3;
        border:2px solid #f3f3f3;
        padding:5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }
</style>
<style type="text/css">
#avatar_preview {
    width:90px; height:90px; background:url(/images/avatars/sprites/4.gif); 
    border:2px solid #CEFFFE; 
    cursor:pointer; 
    margin:5px; 
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    float:left;
}
#avatar_preview:hover { 
	border:2px solid cyan 
}
.colors, .bulk_colors {
	list-style:none; margin:3px 0 3px 58px;
	display:none;
}
.colors li div, .bulk_colors li div {
	width:16px; border:1px solid black; height:16px; 
	 float:left; margin-right:4px;
}
#image_file {
    width:180px;
}
#delete_item_piece {
    font-size:11px;
    text-transform:uppercase;
    display:block;
    color:red;
    border:1px solid #D29799;
    padding:3px 8px 2px 8px;
    margin:5px 0 0;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
</style>

<div style="opacity:0.1;" id="sub_data_canaster">
    <div style="float:left; width:230px;">
        <div style="" id="avatar_preview"></div>
        <br clear="all" />
        <label for="bulk_recolor_item">Bulk Recolor:</label>
        <input type="checkbox" id="bulk_recolor_item">
        <ul class="bulk_colors">
            <form id="bulk_colors_form">
                <?php foreach($colors as $color){ ?>
                <li>
                    <div style="background:<?=$color;?>"></div>
                    <input type="checkbox" name="bulk_color[]" value="<?=$color;?>" class="bulk_color_checkbox" id="<?=$color;?>" checked>
                </li>
                <?php } ?>
            </form>
        </ul>
    </div>
    <div style="float:left; width:340px;">
        <ul class="form piece_list">
            <li style="display:none" id="skeleton">
                <div style="width:90px; height:90px; margin-left:-98px; position:absolute;" class="quick_show">
                    
                </div>
                <form action="<?php echo site_url('pixelshack/item/create_item_part/') ?>" method="post">
                <label for="file_image">Image:</label>
                <input type="file" name="image_file" id="image_file">
                <br clear="all" />
                <label for="piece_layer">Layer:</label>
                <select name="piece_layer" id="piece_layer">
                    <?php foreach ($layers as $layer): ?>
                        <option value="<?php echo $layer['id'] ?>"><?php echo $layer['name'] ?></option>
                    <?php endforeach ?>
                </select><br clear="all" />
                <label for="gender_piece">Gender:</label>
                <select name="gender_piece" id="gender_piece">
                	<?php foreach($gender as $value => $text){ ?>
                    <option value="<?=$value;?>"><?=$text;?></option>
                    <?php } ?>
                </select>
                <br clear="all" />
                <label for="color_group">Group:</label>
                <select name="color_group" id="color_group">
                    <option value="0">None</option>
                    <option value="1">Group 1</option>
                    <option value="2">Group 2</option>
                </select>
                <br clear="all" />
                <label for="recolor_item">Recolor:</label>
                <input type="checkbox" class="recolored_item" name="recolor_item">
                <br clear="all" />
                <ul class="colors">
                	<?php foreach($colors as $color){ ?>
                        <li>
                            <div style="background:<?=$color;?>"></div>
                            <input type="checkbox" name="color[]" value="<?php echo $color ?>" id="" checked>
                            <a href="#">preview</a>
                        </li>
                    <?php } ?>
                </ul>
                <div class="save_changes clearfix" style="display:none; margin-top:5px; border-top:1px solid #ccc; padding:5px; background:rgba(255, 255, 255, 0.4)">
                    <button class="mini right" id="save_changes_item_piece">Save changes</button>
                    <a href="#" class="left" id="delete_item_piece">Delete</a>
                </div>
                </form>
            </li>
            <li>
                <div style="width:90px; height:90px; margin-left:-98px; position:absolute" class="quick_show">
                    
                </div>
                <form action="<?php echo site_url('pixelshack/item/create_item_part/') ?>" method="post">
                <label for="file_image">Image:</label>
                <input type="file" name="image_file" id="image_file">
                <br clear="all" />
                <label for="piece_layer">Layer:</label>
                <select name="piece_layer" id="piece_layer">
                    <?php foreach ($layers as $layer): ?>
                        <option value="<?php echo $layer['id'] ?>"><?php echo $layer['name'] ?></option>
                    <?php endforeach ?>
                </select><br clear="all" />
                <label for="gender_piece">Gender:</label>
                <select name="gender_piece" id="gender_piece">
                	<?php foreach($gender as $value => $text){ ?>
                        <option value="<?=$value;?>"><?=$text;?></option>
                    <?php } ?>
                </select><br clear="all" />
                <label for="color_group">Group:</label>
                <select name="color_group" id="color_group">
                    <option value="0">None</option>
                    <option value="1">Group 1</option>
                    <option value="2">Group 2</option>
                </select>
                <br clear="all" />
                <label for="recolor_item">Recolor:</label>
                <input type="checkbox" class="recolored_item" name="recolor_item">
                
                <br clear="all" />
                
                <ul class="colors">
                	<?php foreach($colors as $color){ ?>
                        <li>
                            <div style="background:<?=$color;?>"></div>
                            <input type="checkbox" name="color[]" value="<?php echo $color ?>" id="" checked>
                            <a href="#">preview</a>
                        </li>
                    <?php } ?>
                </ul>

                <div class="save_changes clearfix" style="display:none; margin-top:5px; border-top:1px solid #ccc; padding:5px; background:rgba(255, 255, 255, 0.4)">
                    <button class="mini right" id="save_changes_item_piece">Save changes</button>
                    <a href="#" class="left" id="delete_item_piece">Delete</a>
                </div>
                </form>
            </li>
        </ul>
        <br>
        <div style="padding:20px;" id="upload_socket">
            <a href="#" class="button mini" id="add_another_piece">+ Add another piece</a>            
        </div>
    </div>
</div>
<a href="#" class="button huge" id="create_main_item" style="position:relative; top:-70px; left:180px;">Create base item</a>
<br clear="all" />
<div style="padding:10px; background:#E7F4E2; color:#2D5B18; -webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px; overflow:hidden; clear:both; margin-top:25px;">
    <span class="left" style="padding:9px 5px;"><input type="checkbox" id="lbn"> Order layers by name</span>
    <a href="#" class="button right" id="install_complete_item">Install item</a>
</div>