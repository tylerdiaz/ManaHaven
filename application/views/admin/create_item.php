<div style="padding:10px; background:#E7F4E2; color:#2D5B18; -webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px; overflow:hidden">
<h4 style="border-bottom:1px solid #C9DDC0; margin-bottom:5px;">Main item data:</h4>
    <div style="height:100px; width:280px; float:left">
        <ul class="form">
            <li>
                <label for="item_name" style="padding:0; margin:0; text-align:left;">Item name:</label><br>
                <input type="text" name="item_name" class="text_input" id="item_name">
            </li>
            <li>
                <textarea style="font-family:arial; font-size:12px; width:200px; padding:3px; height:40px;" placeholder="Small item Description..."></textarea>
            </li>
        </ul>
    </div>
    <div style="height:100px; width:280px; float:left">
        <ul class="form">
            <li>
                <label for="gender">Gender:</label>
                <select name="gender" id="gender">
                    <option value="option1">Unisex</option>
                    <option value="option2">Male only</option>
                    <option value="option2">Female only</option>
                </select>
            </li>
            <li>
                <label for="class">Class:</label>
                <select name="class" id="class">
                    <option value="option1">All classes</option>
                    <option value="option2">Adventurer only</option>
                    <option value="option2">Archer only</option>
                    <option value="option2">Wizard only</option>
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
                    <option value="option1">1 lbs</option>
                    <option value="option1">2 lbs</option>
                    <option value="option1">3 lbs</option>
                    <option value="option1">4 lbs</option>
                    <option value="option1">5 lbs</option>
                    <option value="option1">10 lbs</option>
                    <option value="option1">15 lbs</option>
                    <option value="option1">20 lbs</option>
                    <option value="option1">30 lbs</option>
                    <option value="option1">50 lbs</option>
                </select>
            </li>
        </ul>
    </div>
</div>
<style type="text/css">
    .piece_list > li {
        background:#f3f3f3;
        padding:5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $("#create_main_item").live('click', function(){
//            $.ajax({
//                type: "POST",
//                url: "/admin/create_base_item",
//                data: {
//                    
//                },
//                dataType: "json",
//                success: function(msg){
//                    $(this).fadeOut(1000, function(){
//                        $("#sub_data_canaster").animate({opacity: 1}, 1000);
//                    });
//                }
//            });
//            return false;
            $(this).fadeOut(1000, function(){
                $("#sub_data_canaster").animate({opacity: 1}, 1000);
            });
        });
        
        var current_row = 0;
        var current_column = 0;
        var preview_animation;
        
        $("#avatar_preview").live('click', function(){
            if(current_row > 8) current_row = 0;
            current_row++;
            
            preview_animation = setInterval(function(){
                if(current_column > 8){
                    clearInterval(preview_animation);
                    current_column = 0;
                    $("#avatar_preview").css({ backgroundPosition:  "0px 0px" });
                } else {
                    $("#avatar_preview").css({ backgroundPosition:  (current_column*90)+"px "+(current_row*90)+"px" });
                    current_column++;                    
                }
            }, 200);
            
        })
        
        $('.recolored_item').change(function() {
            $(this).parent().find('ul.colors').slideToggle(300);
        });
        
    });
</script>
<style type="text/css">
#avatar_preview {
    width:90px; height:90px; background:url(/images/avatars/sprites/4.gif); border:2px solid #CEFFFE; cursor:pointer; margin:5px; -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    float:left;
}
#avatar_preview:hover { border:2px solid cyan }
</style>
<div style="opacity:0.1;" id="sub_data_canaster">
    <div style="float:left; width:240px;">
        <div style="" id="avatar_preview"></div>
    </div>
    <div style="float:left; width:340px;">
        <ul class="form piece_list">
            <li>
                <label for="file_image">Image:</label>
                <input type="file" name="file_image" value="" id="file_image"><br clear="all" />
                <label for="piece_layer">Layer:</label>
                <select name="piece_layer" id="piece_layer">
                    <?php foreach ($layers as $layer): ?>
                        <option value="<?php echo $layer['id'] ?>"><?php echo $layer['name'] ?></option>
                    <?php endforeach ?>
                </select><br clear="all" />
                <label for="gender_piece">Gender:</label>
                <select name="gender_piece" id="gender_piece">
                    <option value="unisex">Unisex piece</option>
                    <option value="male">Male piece</option>
                    <option value="female">Female piece</option>
                </select><br clear="all" />
                <label for="gender_piece">Recolor:</label>
                <input type="checkbox" class="recolored_item">
                <br clear="all" />
                <style type="text/css">
                .colors {
                    list-style:none; margin:3px 0 3px 58px;
                    display:none;
                }
                .colors li div {
                    width:16px; border:1px solid black; height:16px; 
                     float:left; margin-right:4px;
                }
                </style>
                <ul class="colors">
                    <li>
                        <div style="background:red"></div>
                        <input type="checkbox" name="" value="" id="">
                        <a href="#">preview</a>
                    </li>
                    <li>
                        <div style="background:blue"></div>
                        <input type="checkbox" name="" value="" id="">
                        <a href="#">preview</a>
                    </li>
                    <li>
                        <div style="background:yellow"></div>
                        <input type="checkbox" name="" value="" id="">
                        <a href="#">preview</a>
                    </li>
                    <li>
                        <div style="background:green"></div>
                        <input type="checkbox" name="" value="" id="">
                        <a href="#">preview</a>
                    </li>
                    <li>
                        <div style="background:gray"></div>
                        <input type="checkbox" name="" value="" id="">
                        <a href="#">preview</a>
                    </li>
                </ul>
                
            </li>
        </ul>
        <br>
        <a href="#" class="button mini">+ Add another piece</a>
    </div>
</div>
<a href="#" class="button huge" id="create_main_item" style="position:relative; top:-70px; left:180px;">Create base item</a>
<br clear="all" />
<div style="padding:10px; background:#E7F4E2; color:#2D5B18; -webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px; overflow:hidden; clear:both; margin-top:25px;">
    <a href="#" class="button right">Install item</a>
</div>