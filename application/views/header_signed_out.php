<html>
    <head>
        <head>
            <meta http-equiv="Content-type" content="text/html; charset=utf-8">
            <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
            <title>ManaHaven - Home</title>
            <?php echo stylesheet('main.css') ?>
            <?php echo script('jquery.js') ?>
            <script> var baseurl = "<?php echo base_url(); ?>"; </script>
            <?php echo script('main.js') ?>
        </head>
        <body id="{page_body}">
            <div class="structure">
                <div class="header">
                    <div id="user_bar">
                        <span class="left">
                            <form action="<?=site_url('auth/signin')?>" id="signin" method="post" accept-charset="utf-8">
                                <label for="username">Username:</label>
                                <input type="text" name="username" id="username" />
                                <label for="password">Password:</label>
                                <input type="password" name="password" id="password" />
                                <input type="submit" value="Sign in" id="submit_signin" />
                            </form>
                        </span>
                        <!-- <a href="#" class="right" id="my_inventory">My inventory</a> -->
                        <div class="right" style="font-size:13px;">
                            <?php echo anchor('auth/recover_password', 'Lost your password?'); ?>
                        </div>
                    </div>
                    <?php if($this->session->flashdata('errors')): ?>
                        <div style="background:#F08843; color:#1B1501; position:absolute; top:32px; width:410px; padding:4px 9px; text-align:center; -webkit-border-radius: 4px;
                        -moz-border-radius: 4px;
                        border-radius: 4px; border:1px solid #F4BD95; -webkit-box-shadow: 0px 1px 3px 1px rgba(0, 0, 0, 0.3);
                        -moz-box-shadow: 0px 1px 3px 1px rgba(0, 0, 0, 0.3);
                        box-shadow: 0px 1px 3px 1px rgba(0, 0, 0, 0.3);">
                            <?php echo $this->session->flashdata('errors') ?>
                        </div>                        
                    <?php endif ?>
                    <h1 id="logo"><a href="#">ManaHaven</a></h1>
                </div>
                <ul id="navigation">
                    <li id="nav_home"><a href="#">Home</a></li>
                </ul>
                <div id="body">
                        <style type="text/css" media="screen">
                        /*
                         * Estimate if it's the first time the user visits, if so: Fade out the top user 
                         * bar a bit to not distract them from the epicenter content. ~ Ty
                        */
                            #signin label {
                                font-size:13px;
                                color:#BCB09E;
                            }
                            #username, #password {
                                width:100px;
                                margin:0 10px 0 3px;
                                border:1px solid #B0A592;
                                background:#8E826C;
                                padding:1px 2px;
                                -webkit-border-radius: 3px;
                                -moz-border-radius: 3px;
                                border-radius: 3px;
                                color:#E1DED8;
                            }

                            #submit_signin {
                                border:none;
                                padding:2px 7px;
                                border:1px solid #C9C1B3;
                                -webkit-border-radius: 3px;
                                -moz-border-radius: 3px;
                                border-radius: 3px;
                                background: #dbd4c4; /* Old browsers */
                                background: -moz-linear-gradient(top, #dbd4c4 1%, #c1b5a0 100%); /* FF3.6+ */
                                background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#dbd4c4), color-stop(100%,#c1b5a0)); /* Chrome,Safari4+ */
                                background: -webkit-linear-gradient(top, #dbd4c4 1%,#c1b5a0 100%); /* Chrome10+,Safari5.1+ */
                                background: -o-linear-gradient(top, #dbd4c4 1%,#c1b5a0 100%); /* Opera11.10+ */
                                background: -ms-linear-gradient(top, #dbd4c4 1%,#c1b5a0 100%); /* IE10+ */
                                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#dbd4c4', endColorstr='#c1b5a0',GradientType=0 ); /* IE6-9 */
                                background: linear-gradient(top, #dbd4c4 1%,#c1b5a0 100%); /* W3C */
                                text-shadow:1px 1px 0 #D4CDBF;
                            }
                            #submit_signin:hover {
                                background: #e8dfca; /* Old browsers */
                                background: -moz-linear-gradient(top, #e8dfca 1%, #d8c7aa 100%); /* FF3.6+ */
                                background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#e8dfca), color-stop(100%,#d8c7aa)); /* Chrome,Safari4+ */
                                background: -webkit-linear-gradient(top, #e8dfca 1%,#d8c7aa 100%); /* Chrome10+,Safari5.1+ */
                                background: -o-linear-gradient(top, #e8dfca 1%,#d8c7aa 100%); /* Opera11.10+ */
                                background: -ms-linear-gradient(top, #e8dfca 1%,#d8c7aa 100%); /* IE10+ */
                                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e8dfca', endColorstr='#d8c7aa',GradientType=0 ); /* IE6-9 */
                                background: linear-gradient(top, #e8dfca 1%,#d8c7aa 100%); /* W3C */
                                cursor:pointer;
                            }
                            #submit_signin:active {
                                background: #9e8f7e; /* Old browsers */
                                background: -moz-linear-gradient(top, #9e8f7e 1%, #b2a48e 13%, #c6c2b3 99%); /* FF3.6+ */
                                background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#9e8f7e), color-stop(13%,#b2a48e), color-stop(99%,#c6c2b3)); /* Chrome,Safari4+ */
                                background: -webkit-linear-gradient(top, #9e8f7e 1%,#b2a48e 13%,#c6c2b3 99%); /* Chrome10+,Safari5.1+ */
                                background: -o-linear-gradient(top, #9e8f7e 1%,#b2a48e 13%,#c6c2b3 99%); /* Opera11.10+ */
                                background: -ms-linear-gradient(top, #9e8f7e 1%,#b2a48e 13%,#c6c2b3 99%); /* IE10+ */
                                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9e8f7e', endColorstr='#c6c2b3',GradientType=0 ); /* IE6-9 */
                                background: linear-gradient(top, #9e8f7e 1%,#b2a48e 13%,#c6c2b3 99%); /* W3C */
                            }
                        </style>
                    