<div class="wrap">
    <?php echo "<h2>" . __( 'WHEEPL ADMIN REGISTRATION' ) . "</h2>"; ?>

    <form id="adminLoginForm" name="admin_init_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <?php echo "<h4>" . __( 'admin user login' ) . "</h4>"; ?>

        <p><?php _e( 'admin username: ' ); ?><input type="text" name="username" value="<?php if (isset($username)) {echo $username;} ?>" size="15"></p>
        <p><?php _e( 'admin password: ' ); ?><input type="password" name="password" value="<?php if (isset($password)) {echo $password;} ?>" size="15"></p>
        
        <hr />
        
        <?php echo "<h4>" . __( 'site information' ) . "</h4>"; ?>
        
        <p><?php _e( 'site reference: ' ); ?></p>
        <p><input type="text" name="siteRef" value="<?php if (isset($site_ref)) {echo $site_ref;} ?>" size="30"><?php _e(" i.e. wheepllaboratory" ); ?></p>
        <p><?php _e( 'site key (provided to you upon admin activation): ' ); ?></p>
        <p><input type="text" name="siteKey" value="<?php if (isset($site_key)) {echo $site_key;} ?>" size="30"></p>

        <p class="error-msg" style="color:#ff6057;"></p>

        <p class="submit">
            <input id="adminSubmit" type="button" name="submit" value="<?php _e( 'submit' ) ?>" />
        </p>
    </form>
</div>