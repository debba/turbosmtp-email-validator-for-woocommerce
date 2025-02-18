<div class="tsev-forms-logo">
    <img src="<?php echo plugins_url( '/admin/img/ts-logo.svg', TURBOSMTP_EMAIL_VALIDATOR_PATH ); ?>" alt="">
    <div class="tsev-account-status">
        <div><?php _e( "Account connected", "turbosmtp-email-validator" ); ?></div>
        <div class="tsev-account-connected"></div>
    </div>
</div>
<div class="tsev-account-info">
                <span class="flex-grow-1 truncate"
                      title="<?php echo esc_html( $user_info['email'] ); ?>"><strong><?php echo esc_html( $user_info['email'] ); ?></strong>
                </span>
    <span>
                    <a id="turbosmtp-disconnect" class="tsev-account-disconnect"
                       title="<?php esc_html_e( "Disconnect account", "turbosmtp-email-validator" ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path
                                    d="M280 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 240c0 13.3 10.7 24 24 24s24-10.7 24-24l0-240zM134.2 107.3c10.7-7.9 12.9-22.9 5.1-33.6s-22.9-12.9-33.6-5.1C46.5 112.3 8 182.7 8 262C8 394.6 115.5 502 248 502s240-107.5 240-240c0-79.3-38.5-149.7-97.8-193.3c-10.7-7.9-25.7-5.6-33.6 5.1s-5.6 25.7 5.1 33.6c47.5 35 78.2 91.2 78.2 154.7c0 106-86 192-192 192S56 368 56 262c0-63.4 30.7-119.7 78.2-154.7z"></path></svg>
                    </a>
                </span>
</div>
<hr class="tsev-hr-separator">
