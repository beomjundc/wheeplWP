var debug = true,
    apiUrl = "https://dev.api.wheepl.com:5000/api/v1/",
    anonToken = "",
    data = {};

jQuery(document).ready(function($) {

    whplAjax('GET', 'addAnon', whplSaveAnonToken, errorHandler);

    // ON CLICK: login user for admin management
    $('#adminSubmit').click(function () {
        var formData = {},
            parameters = {"action":"whpl_post_admin",
                "url":"http://dauph.no-ip.biz:9001/wp-admin/options-general.php?page=wheepl-options",
                "token":anonToken};

        $("#adminLoginForm").serializeArray().map(function(x){formData[x.name] = x.value;}); // generate data object by serializing form values

        data = $.extend({}, parameters, formData); // add parameters and form data

        whplAjax('POST', 'blogAdminInit', whplPostAdmin, errorHandler, data); // ajax call to post data to wheepl
    });

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

    // error alert handler
    errorAlert(jqXHR.responseJSON.error);
}

/*** CALLBACK FUNCTION: SAVE ANON TOKEN ***/
function whplSaveAnonToken (result, textStatus, jqXHR) {
    anonToken = result.data.token;

    if (debug == true) {
        console.log("whplSaveAnonToken successful");
    }
}

/*** CALLBACK FUNCTION: POST ADMIN DATA TO WORDPRESS ***/
function whplPostAdmin (result, textStatus, jqXHR) {
    wpAjax('POST', ajax_object.ajaxUrl, whplRedirectUrl, errorHandler, data);

    if (debug == true) {
        console.log("whplPostAdmin successful");
    }
}

/*** CALLBACK FUNCTION: RE-DIRECT USER ON SUCCESSFUL POST ***/
function whplRedirectUrl (result, textStatus, jqXHR) {
    window.location.href = "/wp-admin/options-general.php";

    if (debug == true) {
        console.log("whplRedirectUrl successful");
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

/*** FUNCTION: ERROR ALERT ***/
function errorAlert (msg) {
    if (msg == "user is not an admin") {
        var alertMsg = "user is not an admin.";

        $('.error-msg').empty();
        $('.error-msg').append(alertMsg);
    }
    else if (msg == "username or password not valid") {
        var alertMsg = "<strong>username</strong> or <strong>password</strong> is not valid.";

        $('.error-msg').empty();
        $('.error-msg').append(alertMsg);
    }
    else if (msg == "siteRef or siteKey not valid") {
        var alertMsg = "<strong>site reference</strong> or <strong>site key</strong> is not valid.";

        $('.error-msg').empty();
        $('.error-msg').append(alertMsg);
    }
    else if (msg == "incorrect siteKey") {
        var alertMsg = "the sitekey is incorrect.";

        $('.error-msg').empty();
        $('.error-msg').append(alertMsg);
}
