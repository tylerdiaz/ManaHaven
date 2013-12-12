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
    <h4>Pixel Shack</h4>
    <ul class="category_list">
        <li><?php echo anchor('pixelshack/dashboard/info', 'Pixel Shack Home') ?></li>
        <li><?php echo anchor('pixelshack/layer', 'Modify Layers') ?></li>
        <li><?php echo anchor('pixelshack/item/create', 'Create item') ?></li>
    </ul>

    <div style="background:#B56A6D; color:white; font-family:Helvetica; font-weight:bold; margin:25px 5px 5px; padding:5px 5px; text-align:center; font-size:12px; -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px; line-height:1.3" id="motto"></div>
</div>
<div class="forum_content">
    <div>Loading data...</div>
</div>

<?php echo script('jquery.form.js') ?>
<script type="text/javascript">
$(document).ready(function(){
    
    var main_item_id = 0;
    
	$("#create_main_item").live('click', function(){
	    var main_btn = $(this);
	    main_btn.text('Creating item...').animate({opacity: 0.6}, 200);
        $("#create_main_item_form").ajaxSubmit({
            dataType: "json",
            success: function(json){
                if(json.success == true){
            		main_btn.fadeOut(300, function(){
            			$("#sub_data_canaster").animate({opacity: 1}, 300);
            		});
                    main_item_id = json.item_id;
                    alert(json.item_id);
                    $(".piece_list > li:last-child").attr('item_piece_id', json.start_piece_id);
                }
            }
        }); 

        return false;
	});
	
	var current_row = 0;
	var current_column = 0;
	var preview_animation;
	
	$("#bulk_recolor_item").live('click', function(){
	   $(".bulk_colors").slideToggle(400);
	   $(".recolored_item").fadeToggle(400);
	   $("label[for='recolor_item']").fadeToggle(400);
	})
	
	$(".piece_list > li").live('click', function(){
	    // Hide others when this one is clicked...
	    
	    $(this).css({ borderColor: "#61BEE8" })
               .find('.save_changes')
               .slideDown();
	
	    $('.piece_list > li').not($(this))
	                         .css({ borderColor: "#f3f3f3" })
	                         .find('.save_changes')
	                         .slideUp();
	})
	
	$("#avatar_preview").live('click', function(){
		if(typeof(preview_animation) != 'null'){
			clearInterval(preview_animation);
		}
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
        return false;
		
	})
	
	$('.recolored_item').live('click', function() {
		$(this).parent().find('ul.colors').slideToggle(300);
	});
	
	$("#add_another_piece").live('click', function(){
	    $.ajax({
	        type: "POST",
	        url: "/pixelshack/item/new_item_part/"+main_item_id,
	        dataType: "json",
	        success: function(json){
	            $(".piece_list").append($('#skeleton').clone().css({display: "block"}).attr('item_piece_id', json.item_part_id));
	            console.log(json);
	        }
	    });
	   return false;
	});
	
	$("#save_changes_item_piece").live('click', function(){
	    var form_target = $(this).parent().parent();
	    form_target.ajaxSubmit({
	        url: "/pixelshack/item/create_item_part/"+form_target.parent().attr('item_piece_id')+"/"+main_item_id+"/",
	        dataType: "json",
	        data: form_target.serializeArray(),
	        success: function(json){
                console.log(json);
                form_target.parent().find(".quick_show").css({'background': "#ffa url("+baseurl+'uploads/'+json.data.item_id+'/'+json.data.image_path+")"});
                form_target.parent().css({ borderColor: "#f3f3f3" }).find('.save_changes').slideUp();
	        },
	        error: function(){
	            alert('Oops, something went wrong. Make sure your uploading a valid image!')
	        }
	    })
                     
        return false;
	});
	
	$("#install_complete_item").live('click', function(){
        $(this).html('<img src="/images/ajax/posting_ajax.gif" alt=""> Installing... this may take a couple of minutes').animate({ opacity: 0.5 })
        var bulk_recolors = new Array();

        $.each($("input[name='bulk_color[]']:checked"), function() {
            bulk_recolors.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: "/pixelshack/item/install_item/"+main_item_id,
            data: {
                bulk_recolor_item: $("#bulk_recolor_item").is(':checked'),
                bulk_recolor_data: bulk_recolors
            },
            timeout: 240000,
            dataType: "json",
            success: function(json){
                console.log(json);
                popup.create({
                    title: "Item created!",
                    content: "Congratulations, your item has been created! We will redirect you in a sec back to the pixel shack. :D",
                    confirm_button: { label: "Redirect me now &rsaquo;", callback: function(){
                        redirect('/pixelshack/dashboard');
                    }}
                });
                
                setTimeout(function(){
                    //redirect('/pixelshack/dashboard')
                }, 3500);
            }
        });
        
        return false;
	});
	
	$("#delete_item_piece").live('click', function(){
	    var main_parent = $(this).parent().parent().parent();
        if(confirm("This item will be permanently deleted, are you sure?")){
            main_parent.fadeOut(300, function(){
                $.ajax({
                    type: "POST",
                    url: "/pixelshack/item/delete_item_part/"+main_parent.attr('item_piece_id'),
                    dataType: "json"
                });
            });
        }
	})
});
</script>
