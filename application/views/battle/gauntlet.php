<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $("#background_shade").fadeIn(3000, function(){
            $("#gameplay_epicenter").addClass('drop_shadow')
        });
        $("#background_shade").click(function(){
            $(this).fadeOut(1000, function(){
                $("#gameplay_epicenter").removeClass('drop_shadow')
            });
        });
    });
</script>
<style type="text/css">
    #background_shade {
        z-index:99; 
        background:rgba(0, 0, 0, 0.4); 
        width:100%; 
        height:100%; 
        position:absolute; 
        top:0; 
        left:0; 
        display:none
    }
    .drop_shadow {
        -webkit-box-shadow: 0px 1px 6px 1px rgba(0, 0, 0, 0.6);
        -moz-box-shadow: 0px 1px 6px 1px rgba(0, 0, 0, 0.6);
        box-shadow: 0px 1px 6px 1px rgba(0, 0, 0, 0.6);
    }
    #gameplay_epicenter {
        z-index:100; 
        background:white; 

        -webkit-border-radius: 8px; 
        -moz-border-radius: 8px; 
        border-radius: 8px;  
        width:765px; 
        position:absolute; 
        margin:10px 0 0 0; 
        border:2px solid #fff; 
    }
</style>
<div id="background_shade">
    

</div>
<div id="gameplay_epicenter">
    <div style="width:135px; float:left; background:#222; height:260px; -moz-border-radius-topleft: 6px;
    -moz-border-radius-topright: 0px;
    -moz-border-radius-bottomright: 0px;
    -moz-border-radius-bottomleft: 6px;
    -webkit-border-radius: 6px 0px 0px 6px;
    border-radius: 6px 0px 0px 6px;">
        
    </div>
    <div style="width:495px; float:left; background:green url(/images/backgrounds/bg_placeholder.jpg); height:260px;">
        
    </div>
    <div style="width:135px; float:left; background:#fff; height:260px; -moz-border-radius-topleft: 0px;
    -moz-border-radius-topright: 6px;
    -moz-border-radius-bottomright: 6px;
    -moz-border-radius-bottomleft: 0px;
    -webkit-border-radius: 0px 6px 6px 0px;
    border-radius: 0px 6px 6px 0px;">
        
    </div>
</div>
<div>
    
</div>