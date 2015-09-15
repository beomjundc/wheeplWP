<!-- WIDGET CONTAINER -->
<div id="whplContainer"></div>
<!-- END OF WIDGET CONTAINER -->

<?php
    function whpl_conf () {
        wp_enqueue_script('whpl-conf-script', plugins_url('/js/whpl-conf.js', __FILE__));
    }

    add_action('admin_enqueue_scripts', 'whpl_conf');

    // pick up primary and secondary hashtags from wp db
    $wpPHashtag = get_post_meta($post->ID, 'whpl_pHashtag', true);
    $wpSHashtag = get_post_meta($post->ID, 'whpl_sHashtag', true);
?>

<script type="text/javascript">

    var siteRef = "<?php echo get_option('whpl_siteRef') ?>",
        wpPHashtag = "<?php echo $wpPHashtag; ?>",
        wpSHashtag = "<?php echo $wpSHashtag; ?>";

    var iframeSrc = whplConf.widgetSrc
             + "?site_ref=" + siteRef
             + "&parent_protocol=" + window.location.protocol + "//"
             + "&parent_host=" + window.location.host
             + "&parent_path=" + window.location.pathname
             + "&wp_phashtag=" + encodeURIComponent(wpPHashtag)
             + "&wp_shashtag=" + encodeURIComponent(wpSHashtag);

    /* WIDGET EMBED */
    (function () {
        var whpl = document.createElement('script');
        whpl.type = 'text/javascript';
        whpl.async = true;
        whpl.src = whplConf.widgetSrc + whplConf.embedScript;

        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(whpl);
    })();

</script>