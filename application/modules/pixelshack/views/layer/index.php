<div class="clearfix" style="padding:4px">
    <h3 class="left">Avatar layers</h3>
    <a href="#" class="action_link right" id="create_layer">Create a layer</a>
</div>
<?php echo script('jquery_ui.js') ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#layers").sortable({ 
            opacity: 0.6, 
            cursor: 'move', 
            update: function(event, ui) {
                //$(ui.item)
                //console.log($(this).sortable("serialize"))
                $.ajax({
                    type: "GET",
                    url: "/pixelshack/layer/reorganize?"+$("#layers").sortable("serialize"),
                    dataType: "json",
                    success: function(json){
                        console.log(json);
                    }
                });
            }								  
        });
        
        $("#create_layer").live('click', function(){
            popup.create({
                title: "Create a new layer",
                content: '<ul class="form"><li><label for="">Layer:</label><input type="text" name="" class="text_input" placeholder="What is the label of this layer?" id="layer_name"></ul>',
                cancel_button: { label: 'Cancel' },
                confirm_button: { 
                    label: 'Create layer', 
                    callback: function(){
                        $.ajax({
                            type: "POST",
                            data: { name: $("#layer_name").val() },
                            url: "/pixelshack/layer/create",
                            dataType: "json",
                            success: function(json){
                                popup.report_success('Layer created! Hang tight while we refresh this page...');
                                setTimeout(function(){
                                    redirect('/pixelshack/dashboard');
                                }, 2000);
                            }
                        });
                    }   
                }
            });
        });
        
        $(".create_layer").live('click', function(){
            var parent_li = $(this).parent();

            popup.create({
                title: "Create a new layer below ["+parent_li.children('span').text()+"]",
                content: '<ul class="form"><li><label for="">Layer:</label><input type="text" name="" class="text_input" placeholder="What is the label of this layer?" id="layer_name"></ul>',
                cancel_button: { label: 'Cancel' },
                confirm_button: { 
                    label: 'Create layer', 
                    callback: function(){
                        $.ajax({
                            type: "POST",
                            data: { name: $("#layer_name").val() },
                            url: "/pixelshack/layer/create/"+parent_li.attr('data_id'),
                            dataType: "json",
                            success: function(json){
                                popup.report_success('Layer created! Hang tight while we refresh this page...');
                                setTimeout(function(){
                                    redirect('/pixelshack/dashboard');
                                }, 2000);
                            }
                        });
                    }   
                }
            });
        });
        
        $(".rename_layer").live('click', function(){
            var parent_li = $(this).parent();

            popup.create({
                
                title: "Rename the \""+parent_li.children('span').text()+"\" layer",
                content: '<ul class="form"><li><label for="layer_name">Name:</label><input type="text" class="text_input" value="'+parent_li.find('.left').text()+'" id="layer_name"></ul>',
                cancel_button: { label: 'Cancel' },
                confirm_button: { 
                    label: 'Rename layer', 
                    callback: function(){
                        $.ajax({
                            type: "POST",
                            data: { name: $("#layer_name").val() },
                            url: "/pixelshack/layer/rename/"+parent_li.attr('data_id'),
                            dataType: "json",
                            success: function(json){
                                popup.report_success('Layer renamed! Hang tight while we refresh this page...');
                                setTimeout(function(){
                                    redirect('/pixelshack/dashboard');
                                }, 2000);
                            }
                        });
                    }   
                }
            });
        })
    });
</script>
<style type="text/css">
#layers li {
    list-style:none;
    display: block;
    padding:7px 5px;
    border-bottom:2px solid #ddd;
    position:relative;
    cursor:move;
}
#layers li:hover .create_layer, #layers li:hover .rename_layer {
    display:block;
}
#layers li:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}
#layers li:nth-child(2n+1) {
    background:#f8f8f8;
}
#layers li:hover {
    background:#ffa;
}
.create_layer {
    position:absolute;
    background:#E2F3DA;
    top:29px;
    right:0;
    display:block;
    float:right;
    font-size:11px;
    font-weight:bold;
    padding:1px 5px;
    -webkit-border-radius: 12px;
    -moz-border-radius: 12px;
    border-radius: 12px;
    z-index:9;
    color:#5F9546;
    display:none;
}
.rename_layer {
    position:absolute;
    background:#F1E4D6;
    top:29px;
    right:85px;
    display:block;
    float:right;
    font-size:11px;
    font-weight:bold;
    padding:1px 5px;
    -webkit-border-radius: 12px;
    -moz-border-radius: 12px;
    border-radius: 12px;
    z-index:9;
    color:#8D723E;
    display:none;
}
</style>
<ul id="layers">
    <?php foreach ($layers as $layer): ?>
        <li id="recordsArray_<?php echo $layer['id'] ?>" data_id="<?php echo $layer['order'] ?>">
            <span class="left"><?php echo $layer['name'] ?></span>
            <a href="#" class="right rename_layer">Rename</a>
            <a href="#" class="right create_layer">Create Layer &darr;</a>
            <?php echo anchor('/pixelshack/layer/delete/'.$layer['order'], 'Delete', 'class="right"') ?>
        </li>
    <?php endforeach; ?>
</ul>