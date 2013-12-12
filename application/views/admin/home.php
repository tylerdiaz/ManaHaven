<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("ul.category_list").tabs("div.forum_content > div", { effect: 'ajax' });
        
        var company_motto = [
            'To move fast, stop moving slow',
            'Make something people want',
            'Break stuff',
            'Fail Harder',
            'Something today is better than something perfect tomorrow',
            'Do what you are afraid to do',
            'You miss 100% of the shots you don\'t take',
            'Make someone smile',
            'We are limited by our visions'
        ];
        
        $("#motto").text(company_motto[rand(0, company_motto.length)]);
        
        setInterval(function(){
            $("#motto").text(company_motto[rand(0, company_motto.length)]);
        }, 15000)
    });
</script>

<div style="width:150px; float:left; padding-left:10px; min-height:300px; ">
    <h4>Administration</h4>
    <ul class="category_list">
        <li><?php echo anchor('admin/overview', 'Overview') ?></li>
        <li style="margin-top:6px"><h4>Pixel Area</h4></li>
        <li><?php echo anchor('admin/layers', 'Modify Layers') ?></li>
        <li><?php echo anchor('admin/create_item', 'Create item') ?></li>
    </ul>
    
    <div style="background:#B56A6D; color:white; font-family:Helvetica; font-weight:bold; margin:25px 5px 5px; padding:5px 5px; text-align:center; font-size:12px; -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px; line-height:1.3" id="motto"></div>
</div>
<div class="forum_content">
    <div>Loading data...</div>
</div>