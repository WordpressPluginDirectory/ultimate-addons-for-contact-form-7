<?php

// Do not access directly

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 
 
 function uacf7_telegram_active_status_callback($form_id) {
 
  $telegram = uacf7_get_form_option( $form_id, 'telegram' );
   
    $uacf7_telegram_bot_username = isset($telegram['uacf7_telegram_bot_username']) ? $telegram['uacf7_telegram_bot_username']: ''; 
    $uacf7_telegram_bot_name = isset($telegram['uacf7_telegram_bot_name']) ? $telegram['uacf7_telegram_bot_name']: ''; 
    
    ?>

    <div class="bot_title_and_status">
      <h1><?php $uacf7_telegram_bot_name ?></h1>
      <div class="bot_status">
        <div class="check_bot online" style="display:none;">
          <strong class="status" style="background-color: #037c09; color: #ffffff; padding: 6px 10px; border-radius: 3px;">Bot is Online</strong>
         <div class="bot_info">
          <code class="bot_name">  </code>
          <code class="bot_username"> </code>
         </div>
        </div>

        <div class="check_bot offline">
            <strong class="status" style="background-color: #df0c0c; color: #ffffff; padding: 6px 10px; border-radius: 3px;">Bot is Offline</strong>
          </div>
        </div>

    </div> 
<?php

 }



