var debug = true,
    apiUrl = "https://dev.api.wheepl.com:5000/api/v1/",
    anonToken = "";

jQuery(document).ready(function($) {

    whplAjax('GET', 'addAnon', whplSaveAnonToken, errorHandler);

});

/*** CALLBACK FUNCTION: DEFAULT SUCCESS HANDLER ***/
function successHandler (result, textStatus, jqXHR) {
    if (debug == true) {
        console.log("ajax call successful");
    }
}

/*** CALLBACK FUNCTION: DEFAULT ERROR HANDLER ***/
function errorHandler (jqXHR, textStatus, errorThrown) {
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

        whplAjax('POST', 'fetchHashtagUrl', whplDisplayCounter, errorHandler, data);
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
function wpAjax (requestType, ajaxUrl, successHandler, errorHandler, data)
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
        success: successHandler,
        error: errorHandler,
        function(response){
            alert('The server responded: ' + response);
        }
    });
}

/*** FUNCTION: WHEEPL AJAX CALL ***/
function whplAjax (requestType, endPoint, successHandler, errorHandler, data)
{
    jQuery.ajax({
        type: requestType,
        url: apiUrl + endPoint,
        contentType: "application/json; charset=utf-8",
        headers: {
            "Authorization": "Basic " + btoa('username' + ":" + 'password')
        },
        data: JSON.stringify(data),
        cache: false,
        beforeSend: function(result) {
            if (debug == true)
                console.log("calling " + endPoint + "...");
        },
        success: successHandler,
        error: errorHandler
    });
}
