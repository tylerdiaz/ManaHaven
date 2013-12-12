<style type="text/css">
    .form { list-style:none }
    .form li { margin:3px 0; }
    .form li label { font-size:13px; color:#555; font-weight:bold; float:left; width:70px; margin-right:10px; text-align:right; }
    .form li .text_input { border:1px solid #bbb; padding:2px 4px; font-size:13px; width:200px; }
    .form li .text_input:focus { border:1px solid #86A751; 
    -webkit-box-shadow: 0px 0px 0px 2px #daebb5;
    -moz-box-shadow: 0px 0px 0px 2px #daebb5;
    box-shadow: 0px 0px 0px 2px #daebb5;
    outline:none;
     }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        // If we're looking at a category, assume we want to create one in there!
        if(typeof highlighted_category !== 'undefined' && highlighted_category.length > 0){
            $("#"+highlighted_category+"_select").attr('selected', 'selected');
        }
    });
</script>
<ul class="form">
    <li>
        <label for="">Title:</label>
        <input type="text" name="" placeholder="What is the topic about?" class="text_input" value="" id="topic_title">
        <select id="category" name="category">
            <option value="">Choose a category...</option>
            <?php foreach ($forums as $forum): ?>
            <option id="f<?php echo $forum['id'] ?>_select" value="<?php echo $forum['id'] ?>"><?php echo $forum['name'] ?></option>
            <?php endforeach ?>
        </select>
    </li>
    <li>
        <label for="" style="margin-top:7px">Message:</label>
        <textarea class="post_textfield" id="topic_message" style="width:350px; height:140px; margin:7px 0;"></textarea>
    </li>
</ul>