//jq = jQuery.noConflict();
let fullLoaderSelector = "#full-overlay";
function load_datepicker(){
    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        endDate: "+0d",
        keyboardNavigation: false
    });
}

function showLoader(selector){
    if(selector == "" || selector == "undefined" || selector == null)
        selector = fullLoaderSelector;
    $(selector).show();
}

function hideLoader(selector){
    if(selector == "" || selector == "undefined" || selector == null)
        selector = fullLoaderSelector;
    $(selector).hide();
}

function notifyAlert(msg,type,title,callback_func){
    var data = {};

    if(title == "" || title == "undefined" || title == null)
        title = "";

    data['title'] = title;
    data['text'] = msg;
    data['type'] = "error";
    if(type != "" && type != "undefined" && type != null)
        data['type'] = type;
    data['confirmButtonText'] = "Ok";
    data['showConfirmButton'] = true;
    data['html'] = true;
    data['closeOnConfirm'] = true;
    data['confirmButtonColor'] = "#41b3f9";
    
    swal(data,function(isConfirm){
        if(typeof callback_func === "function"){
            callback_func(isConfirm);
        }
    });
}

function confirmAlert(msg,type,title,btntext,callback_func,ele){
    var data = {};

    if(title == "" || title == "undefined" || title == null)
        title = "";

    data['title'] = title;

    if(msg == "" || msg == "undefined" || msg == null)
        msg = "Please wait...";

    data['text'] = msg;

    if(type != "" && type != "undefined" && type != null)
        data['type'] = type;
    
    if(btntext == "" || btntext == "undefined" || btntext == null)
        btntext = "Ok";
    
    data['confirmButtonText'] = btntext;
    
    if(callback_func == "" || callback_func == "undefined" || callback_func == null)
        callback_func = "";

    data['showConfirmButton'] = true;
    data['showCancelButton'] = true;
    data['cancelButtonText'] = "Cancel";
    data['html'] = true;
    data['closeOnConfirm'] = true;
    data['confirmButtonColor'] = "#41b3f9";
    
    swal(data, function(isConfirm){
        if(typeof callback_func === "function"){
            callback_func(ele,isConfirm);
        }
    });
}

function confirmDelete(route) {
    showLoader();
    confirmAlert("On confirm record will be deleted.","warning","Are you sure?","Confirm",function(r,i){
        if(i){
            window.location.href = r;
        }
        else{
            hideLoader();
        }
    },route);
};

function showPopup(popup_id,popup_type,options){
    var bg_color = '';
    
    if(popup_id == '' || popup_id == null || popup_id == 'undefined')
    {
        popup_id = "modal-default";
    }
    
    if(options == '' || options == null || options == 'undefined')
    {
        options = null;
    }
    
    bg_color = "bg-info";
    if(popup_type != '' && popup_type != null && popup_type != 'undefined')
    {
        bg_color = popup_type;
    }
    
    var html_str = $("#"+popup_id+" .modal-dialog").html();

    $("#"+popup_id+" .modal-dialog").html(html_str);
    $("#"+popup_id+" .modal-header").addClass(bg_color);
    $("#"+popup_id).addClass("loading").modal(options).on('hidden.bs.modal', function (e) {
        $("#"+popup_id+" .modal-dialog").html(html_str);
        $("#"+popup_id).removeClass("loading");
    });
}

function hidePopup(popup_id){
    if(popup_id == null || popup_id == "" || popup_id == 0)
    {
        popup_id = "modal-default";
    }
    var html_str = $("#"+popup_id+" .modal-dialog").html();
    $("#"+popup_id+" .modal-dialog").html(html_str);
    $("#"+popup_id).removeClass("loading");
    $("#"+popup_id).modal('hide');
}

function getAjax(url,extra_data,popup_id,callback_func){

    if(typeof extra_data !== "object"){
        extra_data = { data: extra_data };
    }

    $.ajax({
        url: url,
        type: "post",
        dataType: "json",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: extra_data,
        success:function(response){
            if(typeof callback_func === "function"){
                callback_func(response);
            }
            else{
                if(popup_id == '' || popup_id == null || popup_id == 'undefined') {
                    popup_id = "modal-default";
                }
                if(response.type=='success'){
                    $("#"+popup_id).removeClass("loading");
                    $('#'+popup_id+' .modal-dialog').html(response.data);
                }
                else{
                    $("#"+popup_id).removeClass("loading");
                    $('#'+popup_id+' .modal-dialog').html(response.message);
                }
            }
        }  
    });
}

