<style type="text/css" media="screen">
    label.text_input {
        float:left;
        width:140px;
        font-weight:bold;
    }
    label {
        font-size:15px;
    }
    .form {
        list-style:none;
    }
    .form li {
        margin:12px 15px;
    }
    #scaffold_name, #nav_label {
        padding:2px 3px;
        border:1px solid #8D9FC4;
        font-size:16px;
        -webkit-box-shadow: 0px 1px 2px #d6d6d6;
        -moz-box-shadow: 0px 1px 2px #d6d6d6;
        box-shadow: 0px 1px 2px #d6d6d6;
    }
    #scaffold_name:focus {
        outline:0;
        border-color:blue;
    }
    #scaffold_clean_name {
        padding:2px 3px;
        border:1px solid #bbb;
        font-size:16px;
        -webkit-box-shadow:inset 0px 1px 2px #ccc;
        -moz-box-shadow:inset 0px 1px 2px #ccc;
        box-shadow:inset 0px 1px 2px #ccc;
        background:#eee;
    }
    button {
        background: #f8ffe8; /* Old browsers */
        background: -moz-linear-gradient(top, #f8ffe8 0%, #e3f5ab 33%, #b7df2d 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f8ffe8), color-stop(33%,#e3f5ab), color-stop(100%,#b7df2d)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f8ffe8', endColorstr='#b7df2d',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #f8ffe8 0%,#e3f5ab 33%,#b7df2d 100%); /* W3C */
        padding:7px 13px;
        font-size:15px;
        font-weight:bold;
        border:1px solid #A3AB5B;
        cursor:pointer;
        -webkit-box-shadow: 0px 1px 4px #ccc;
        -moz-box-shadow: 0px 1px 4px #ccc;
        box-shadow: 0px 1px 4px #ccc;
    }
    button:hover {
        background: #faffef; /* Old browsers */
        background: -moz-linear-gradient(top, #faffef 0%, #edfac4 33%, #d3f167 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#faffef), color-stop(33%,#edfac4), color-stop(100%,#d3f167)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #faffef 0%,#edfac4 33%,#d3f167 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #faffef 0%,#edfac4 33%,#d3f167 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #faffef 0%,#edfac4 33%,#d3f167 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#faffef', endColorstr='#d3f167',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #faffef 0%,#edfac4 33%,#d3f167 100%); /* W3C */
    }
    button:active {
        background: #8ba628; /* Old browsers */
        background: -moz-linear-gradient(top, #8ba628 0%, #bfde62 67%, #d2f482 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#8ba628), color-stop(67%,#bfde62), color-stop(100%,#d2f482)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #8ba628 0%,#bfde62 67%,#d2f482 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #8ba628 0%,#bfde62 67%,#d2f482 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #8ba628 0%,#bfde62 67%,#d2f482 100%); /* IE10+ */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#8ba628', endColorstr='#d2f482',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #8ba628 0%,#bfde62 67%,#d2f482 100%); /* W3C */
    }
    .nav_bonus {display:none;}
</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8">
function ucfirst (str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}
    $(document).ready(function(){
        function success(){
            $("h3").after('<strong style="color:green;">Good news! Your scaffold has been created.</strong>');
            setTimeout(function(){
                $("strong").fadeOut(1000);
            }, 4500)
        }
        
        $("#scaffold_name").keyup(function(e){
            $(this).val(ucfirst($(this).val()));
            $("#scaffold_clean_name").val($(this).val().replace(/ /g,"_").toLowerCase());
        });
        
        $("#navigation").change(function(){
            $(".nav_bonus").fadeToggle(200);
        });
        
        $("#scaffold_create").submit(function(){
            
            var form = $(this);

            var obj_scaffold = {
                scaffold_name: $("#scaffold_name").val(),
                scaffold_clean_name: $("#scaffold_clean_name").val(),
                model: $("#model").val(),
                developer: $("#developer").val(),
                navigation: $("#navigation").val(),
                nav_location: $("#nav_location").val(),
            };

            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: obj_scaffold,
                dataType: "json",
                success: function(msg){
                    success();
                }
            });
            
            return false;
        })
    });
</script>
<h3 style="border-bottom:1px solid #ccc; padding-bottom:10px; margin-bottom:4px">Developer: Create a scaffold</h3>
<form action="<?=site_url('admin/create_scaffold')?>" method="post" accept-charset="utf-8" id="scaffold_create">
    <ul class="form">
        <li>
            <label for="scaffold_name" class="text_input">Scaffold name:</label>
            <input type="text" name="scaffold_name" value="" id="scaffold_name">
            <input type="text" disabled name="scaffold_clean_name" value="" id="scaffold_clean_name">
        </li>
        <li><input type="checkbox" name="model" id="model" checked> <label for="model">Add a model</label></li>
        <li><input type="checkbox" name="developer" id="developer" checked> <label for="developer">Developers only</label></li>
        <li><input type="checkbox" name="navigation" id="navigation"> <label for="navigation">Create CSS navigation code</label></li>
        <li style="margin-left:30px" class="nav_bonus">Add to the <select name="nav_location"><option value="top">Top</option><option value="side">Sidebar</option></select> navigation <br></li>
        </li>
        <li><br><br><button type="submit">Generate Scaffold</button></li>
    </ul>
</form>