                </div>
                <div class="footer_push"></div>
            </div>
        </div>

        <div id="footer">
            <div style="width:780px; margin:0 auto; ">
                <div class="left">
                    <a href="http://manahaven.com/"><?php echo image('footer_logo.png', 'width="140" height="34"') ?></a><br>
                    <div style="font-size:11px; margin-top:-5px;">&nbsp;&copy;2011, All rights reserved</div>
                </div>
                <div class="link_list" style="text-align:right; padding-top:4px">
                    <?php echo anchor('community/#2', 'Report a bug') ?> &bull; 
                    <?php echo anchor('docs/terms_of_service', 'Terms of Service') ?> &bull; 
                    <?php echo anchor('docs/privacy', 'Privacy Policy') ?>
                </div>
            </div>
        </div>
        
        <div class="popup_shadow">
            <a href="#" class="close_box"></a>
            <div class="popup_box">
                <div class="header_box"><h3>Details</h3></div>
                <div id="popup_notice" style="display:none;"></div>
                <div class="popup_content"></div>
                <div class="button_footer">
                    <a href="#" class="confirm_button">Create</a>
                    <a href="#" class="delete_button">Delete</a>
                    <a href="#" class="cancel_button">Cancel</a>
                </div>
            </div>
        </div>
        <!-- 
            Coded & Designed by Tyler Diaz 

            Think Different:
            "Here's to the crazy ones. 
            The misfits. 
            The rebels. 
            The troublemakers. 
            The round pegs in the square holes.
            The ones who see things differently. 
            They’re not fond of rules. And they have no respect for the status quo. 
            You can quote them, disagree with them, glorify or vilify them.
            About the only thing you can’t do is ignore them. 
            Because they change things. They push the human race forward.
            While some see them as the crazy ones, we see genius. 
            Because the people who are crazy enough to think they can change the world, are the ones who do."
        -->
        <? if($this->system->is_staff()): ?>
        <?php 
            $this->output->set_profiler_sections(array(
                'config'  => FALSE,
                'controller_info' => FALSE,
                'get' => FALSE,
                'controller_info' => FALSE,
                'http_headers' => FALSE,
                'uri_string' => FALSE,
                'post' => FALSE
            )); 
            
            $this->output->enable_profiler(true);
        ?>
        <? endif; ?>
        <div id="quick_success" style="position:fixed; width:280px; left:50%; top: 5px; margin-left:-140px; background:green; color:white; text-align:center; padding:4px 5px; -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px; display:none;">
            &#x2713; Your post has been edited!
        </div>
    </body>
</html>
<?php
    if($this->session->userdata('signed_in')):
        $this->db->where('user_id', $this->system->userdata['id'])
             ->update('user_activity', array('last_activity' => time(), 'location' => (isset($location) ? $location : NULL)));
    endif;
?>