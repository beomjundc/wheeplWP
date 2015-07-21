<!-- WIDGET CONTAINER -->
<div id="whplContainer"></div>
<!-- END OF WIDGET CONTAINER -->

<?php
    function whpl_conf () {
        wp_enqueue_script('whpl-conf-script', plugins_url('/js/whpl-conf.js', __FILE__));
    }

    add_action('admin_enqueue_scripts', 'whpl_conf');
?>

<script type="text/javascript">

    var siteRef = "<?php echo get_option('whpl_siteRef') ?>";
    var iframeSrc = whplConf.widgetSrc
     + "?site_ref=" + siteRef
     + "&parent_protocol=" + window.location.protocol + "//"
     + "&parent_host=" + window.location.host
     + "&parent_path=" + window.location.pathname;

    /* WIDGET EMBED */
    (function () {
        var whpl = document.createElement('script');
        whpl.type = 'text/javascript';
        whpl.async = true;
        whpl.src = whplConf.widgetSrc + whplConf.embedScript;

        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(whpl);
    })();

</script>