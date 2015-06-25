<div class="wrap">
    <?php echo "<h2>" . __( 'WHEEPL SETTINGS' ) . "</h2>"; ?>

    <form id="adminSettingsForm" name="admin_settings_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">    
        <?php echo "<h3>" . __( 'GENERAL' ) . "</h3>"; ?>

        <?php echo "<h4>" . __( 'admin username: ' ) . "</h4>"; ?>
        <p style="font-weight: bold; color:#4dcebc;"><?php _e( get_option('whpl_admin') ); ?></p>

        <?php echo "<h4>" . __( 'site reference: ' ) . "</h4>"; ?>
        <p style="font-weight: bold; color:#4dcebc;"><?php _e( get_option('whpl_siteRef') ); ?></p>
        <p><?php _e( 'site reference is a unique identifier that wheepl uses to identify your blog.' ); ?></p>
        
        <hr />

        <?php echo "<h3>" . __( 'SUPPORT' ) . "</h3>"; ?>

        <?php echo "<h4>" . __( 'contact us: ' ) . "</h4>"; ?>
        <p><?php _e( 'breaking your head over something? if you are having any issues or need help troubleshooting the wheepl widget on wordpress, ' ); ?><a href="mailto:support@wheepl.com?subject=WHEEPL WIDGET SUPPORT REQUEST">contact wheepl support</a>.</p>
    </form>
</div>