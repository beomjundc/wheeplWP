<!-- WIDGET CONTAINER -->
<div id="whplContainer"></div>
<!-- END OF WIDGET CONTAINER -->

<script type="text/javascript">

    var siteRef = "<?php echo get_option('whpl_siteRef') ?>";
    var iframeSrc = "https://dev.widget.wheepl.com:5001"
     + "?site_ref=" + siteRef
     + "&parent_protocol=" + window.location.protocol + "//"
     + "&parent_host=" + window.location.host
     + "&parent_path=" + window.location.pathname;

    /* WIDGET EMBED */
    (function () {
        var whpl = document.createElement('script'); whpl.type = 'text/javascript';
        whpl.async = true;
        whpl.src = 'https://dev.widget.wheepl.com:5001/js/whpl-embd.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(whpl);
    })();

</script>