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

    // pick up tags as a backup option for hashtags
    $posttags = get_the_tags();
?>

<script type="text/javascript">

    var siteRef = "<?php echo get_option('whpl_siteRef') ?>",
        wpPHashtag = escape("<?php echo $wpPHashtag; ?>"),
        wpSHashtag = escape("<?php echo $wpSHashtag; ?>"),
        tags = <?php echo json_encode($posttags); ?>,
        pTag = tags[0] ? "%23" + tags[0].name.replace(/\s+/g, '') : "",
        sTag = tags[1] ? "%23" + tags[1].name.replace(/\s+/g, '') : "";

    var iframeSrc = whplConf.widgetSrc
             + "?site_ref=" + siteRef
             + "&parent_protocol=" + window.location.protocol + "//"
             + "&parent_host=" + window.location.host
             + "&parent_path=" + window.location.pathname
             + "&wp_phashtag=" + wpPHashtag
             + "&wp_shashtag=" + wpSHashtag
             + "&ptag=" + pTag
             + "&stag=" + sTag;

    /* WIDGET EMBED */
    (function () {
        var whpl = document.createElement('script');
        whpl.type = 'text/javascript';
        whpl.async = true;
        whpl.src = whplConf.widgetSrc + whplConf.embedScript;

        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(whpl);
    })();

</script>