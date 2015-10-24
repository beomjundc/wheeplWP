<!-- WIDGET CONTAINER -->
<div id="whplContainer"></div>
<!-- END OF WIDGET CONTAINER -->

<?php
    function whpl_conf () {
        wp_enqueue_script('whpl-conf-script', plugins_url('/js/whpl-conf.js', __FILE__));
    }

    add_action('admin_enqueue_scripts', 'whpl_conf');

    // pick up primary and secondary hashtags from wp db
    $whplPHashtag = get_post_meta($post->ID, 'whpl_pHashtag', true);
    $whplSHashtag = get_post_meta($post->ID, 'whpl_sHashtag', true);
?>

<script type="text/javascript"> 

    var siteRef = "<?php echo get_option('whpl_siteRef') ?>",
        whplPHashtag = "<?php echo $whplPHashtag; ?>",
        whplSHashtag = "<?php echo $whplSHashtag; ?>";

    var iframeSrc = whplConf.widgetSrc
             + "?site_ref=" + siteRef
             + '&parent_domain=' + window.location.protocol + '//' + window.location.host
             + "&parent_path=" + window.location.pathname
             + "&phashtag=" + encodeURIComponent(whplPHashtag)
             + "&shashtag=" + encodeURIComponent(whplSHashtag);

    /* WIDGET EMBED */
    (function () {
        var whpl = document.createElement('script');
        whpl.type = 'text/javascript';
        whpl.async = true;
        whpl.src = whplConf.widgetSrc + whplConf.embedScript;

        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(whpl);
    })();

</script>