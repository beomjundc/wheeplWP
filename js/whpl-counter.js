var apiUrl = whplConf.apiUrl,
    debug = whplConf.debug,
    ajaxUser = whplConf.ajaxUser,
    ajaxPass = whplConf.ajaxPass
    anonToken = "";

jQuery(document).ready(function($) {

    whplAjax('GET', 'addAnon', whplSaveAnonToken, whplErrorHandler);

});

/*** CALLBACK FUNCTION: DEFAULT SUCCESS HANDLER ***/
function whplSuccessHandler (result, textStatus, jqXHR) {
    if (debug == true) {
        console.log("ajax call successful");
    }
}

/*** CALLBACK FUNCTION: DEFAULT ERROR HANDLER ***/
function whplErrorHandler (jqXHR, textStatus, errorThrown) {
    if (debug == true) {
        console.log("error in ajax call");
        console.log("-----------------");
        console.log(textStatus);
        console.log(errorThrown);
        console.log(jqXHR.responseJSON.error);
        console.log("-----------------");
    }
}

/*** CALLBACK FUNCTION: SAVE ANON TOKEN ***/
function whplSaveAnonToken (result, textStatus, jqXHR) {
    anonToken = result.data.token;

    jQuery('.whpl-counter').each(function () {
        var data = {"url":jQuery(this).data("whplPostUrl"),
            "siteRef":ajax_object.siteRef,
            "token":anonToken
        }

        whplAjax('POST', 'fetchHashtagUrl', whplDisplayCounter, whplErrorHandler, data);
    });

    if (debug == true) {
        console.log("whplSaveAnonToken successful");
    }
}

/*** CALLBACK FUNCTION: RETRIEVE COUNTER ***/
function whplDisplayCounter (result, textStatus, jqXHR) {
    jQuery('.whpl-counter[data-whpl-post-url="' + result.data.url + '"]').text(result.data.phashtagCommentCount);
    jQuery('.whpl-phashtag[data-whpl-post-url="' + result.data.url + '"]').text(result.data.poHashtag);

    if (debug == true) {
        console.log("whplDisplayCounter successful");
    }
}

/*** FUNCTION: WORDPRESS AJAX CALL ***/
function wpAjax (requestType, ajaxUrl, whplSuccessHandler, whplErrorHandler, data)
{
    jQuery.ajax({
        type: requestType,
        url: ajaxUrl,
        data: data,
        cache: false,
        beforeSend: function(result) {
            if (debug == true)
                console.log("action: " + data.action);
        },
        success: whplSuccessHandler,
        error: whplErrorHandler
        // function(response){
        //     alert('The server responded: ' + response);
        // }
    });
}

/*** FUNCTION: WHEEPL AJAX CALL ***/
function whplAjax (requestType, endPoint, whplSuccessHandler, whplErrorHandler, data)
{
    jQuery.ajax({
        type: requestType,
        url: apiUrl + endPoint,
        contentType: "application/json; charset=utf-8",
        // headers: {
        //     "Authorization": "Basic " + btoa(ajaxUser + ":" + ajaxPass)
        // },
        data: JSON.stringify(data),
        cache: false,
        beforeSend: function(result) {
            if (debug == true)
                console.log("calling " + endPoint + "...");
        },
        success: whplSuccessHandler,
        error: whplErrorHandler
    });
}
