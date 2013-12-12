<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        setTimeout(function(){
            popup.create({
                title: "This topic has opened in another window!",
                content: "Another window has opened with this same topic! This window will stop loading any new posts that are made in the topic.",
                cancel_button: { label: 'Close dialog' },
                confirm_button: { label: 'Ok, thanks for letting me know!', ajax: 'home/index', callback: function(){
                    popup.hide(); 
                    return false;
                }}
            }); // end popup
        }, 2000)
    });
</script>
<h3>A couple of Javascript tests and helpers</h3>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<a href="#" id="dialog">Open dialog while scrolled down</a>