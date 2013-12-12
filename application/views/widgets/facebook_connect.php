<div class="empty_widget">
    <p>Still new? You might know some people already playing who can give you a hand!</p>
    <script src="http://connect.facebook.net/en_US/all.js"></script>
    <script>
        $(document).ready(function(){
            FB.init({ 
                appId:'241609175859888', cookie:true, status:true, xfbml:true 
            });

            FB.Event.subscribe('auth.login', function(response) {
                $("#allies .empty_widget").html($('<img src="/images/ajax/large_ajax.gif" style="margin-top:15px;" />'));

                $.ajax({
                    type: "GET",
                    url: "friends/facebook",
                    dataType: "json",
                    success: function(msg){
                        redirect('friends')
                    }
                });
            });

        });
    </script>
    <div class="subtle_shade">
        <fb:login-button>Connect with my friends</fb:login-button>
    </div>
</div>
