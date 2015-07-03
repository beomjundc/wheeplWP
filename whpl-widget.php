<!-- WIDGET CONTAINER -->
<div id="whplContainer"></div>
<!-- END OF WIDGET CONTAINER -->

<script type="text/javascript">

    var siteRef = "<?php echo get_option('whpl_siteRef') ?>";
    var iframeSrc = "http://dev.widget.wheepl.com"
     + "?site_ref=" + siteRef
     + "&parent_protocol=" + window.location.protocol + "//"
     + "&parent_host=" + window.location.host
     + "&parent_path=" + window.location.pathname;

    /* WIDGET EMBED */
    (function () {
        var whpl = document.createElement('script'); whpl.type = 'text/javascript';
        whpl.async = true;
        whpl.src = 'http://dev.widget.wheepl.com/js/whpl-embd.min.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(whpl);
    })();

</script>