<div class="wrap arfforms_page">
    <div class="top_bar" style="margin-bottom: 10px;">
	<span class="h2"> <?php echo __('ARForms Add-Ons','arforms-form-builder'); ?></span>
    </div>
	<div id="poststuff" class="">
    	<div id="post-body" >
        	<div class="addon_content">
                <?php do_action('arflite_addon_page_retrieve_notice'); ?>
                <div class="addon_page_desc"> <?php _e('Add more features to ARForms using Add-Ons','arforms-form-builder'); ?></div>
                <div class="addon_page_content">
					<?php
						global $arflitesettingcontroller;
						$arflitesettingcontroller->addons_page();
					?>
                </div>
            </div>
        </div>
    </div>
</div>
