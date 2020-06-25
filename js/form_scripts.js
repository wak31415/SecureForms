function getElementObj(add_btn) {
    var type;
    var element = {}
    if (add_btn.id === "add_checkbox") {
        type = "checkbox";
    } else if (add_btn.id === "add_radio") {
        type = "radio";
    } else {
        type = "text"
    }
    element.type = type;
    element.options = [];
    element.required = false;
    element.random = false;
    element.question = "";
    return element;
}

// editable form fields for form creators
function addFormElementAdmin(element) {
    var container = document.createElement("DIV");
    $(container).appendTo("#form_elements")
    .addClass("card form_element");

    var card_header = document.createElement("DIV");
    $(card_header).appendTo(container)
    .addClass("card-header");

    if(element.type!="text") {
        var card_body = document.createElement("DIV");
        $(card_body).appendTo(container)
        .addClass("card-body");
    }

    var card_footer = document.createElement("DIV");
    $(card_footer).appendTo(container)
    .addClass("card-footer");

    var remove_btn = document.createElement("BUTTON");
    $(remove_btn).text("Remove")
    .addClass("btn btn-danger")
    .click(function() {
        $(this).parent().parent().remove();
        $("#save_status").text("unsaved changes");
        $("#save_status").parent().addClass("alert-danger")
        .removeClass("alert-success");
    });
    $(card_footer).append(remove_btn);

    var el_label = document.createElement("P");
    $(el_label).text(element.type)
    .addClass("type")
    .attr("style","display: none")
    .appendTo(card_header);
    
    $("<input>").attr({"type":"text", "placeholder":"Title"})
    .addClass("form-control form-control-lg form_text")
    .appendTo(card_header)
    .val(element.question)
    .focus();

    // $(container).append(document.createElement("BR"));
    
    switch (element.type) {
        case "checkbox":
        case "radio":
            for(opt of element.options) {
                createOptionField(card_body, opt, element.type);
            }
            createOptionField(card_body,"",element.type);
            
            break;
    
        default:
            break;
    }
    
}

function createOptionField(form_el_container,val,type=false,trigger_field=false) {
    // check if the trigger field is the last option field, only then create
    // a new option field
    if (!trigger_field) {
        trigger_field = $(form_el_container).children(".form_text").first();
    } else if ($(trigger_field).parent()[0] != $(form_el_container).children("div").last()[0]) {
        return;
    }

    var option_container = document.createElement("DIV");
    // if($(trigger_field).hasClass("form_text")) {
    //     $(trigger_field).after(option_container);
    // } else {
    //     $(trigger_field).parent().after(option_container);
    // }
    $(option_container).addClass("input-group")
    .appendTo(form_el_container);
    if(type) {
        var $prepend = $("<div>",{"class":"input-group-prepend"});
        var $prepend_content = $("<div>",{"class":"input-group-text"});
        var $input_type = $("<input>",{"type":type,"disabled":true});
        $input_type.appendTo($prepend_content);
        $prepend_content.appendTo($prepend);
        $prepend.appendTo(option_container);
    }

    var option_field = document.createElement("INPUT");
    var rem_option = document.createElement("BUTTON");
    
    $(option_field).attr({"type":"text","placeholder":"New Option"})
    .addClass("form-control")
    .appendTo(option_container)
    .val(val)
    .on("keypress", function() {createOptionField(form_el_container,"",type,this)})
    .on("blur", function() {
        $(option_field).parent().next("div").children("input").focus();
    });

    var option_container_append = document.createElement("DIV");
    $(option_container_append).addClass("input-group-append")
    .appendTo(option_container);

    $(rem_option).text("X")
    .appendTo(option_container_append)
    .addClass("btn")
    .click(function() {
        $(this).parent().parent().remove();
        $("#save_status").text("unsaved changes");
        $("#save_status").parent().addClass("alert-danger")
        .removeClass("alert-success");
    });

}

function updateFormData() {
    var name_counter = 0;
    var data = {"name":"Placeholder","elements":[], "params":{}}
    $("#form_elements").children("div").each(function(){
        var element = {};
        element.name = (name_counter++).toString();
        element.type = $(this).find(".type").text();
        element.required = false;
        element.question = $(this).find(".form_text").val();
        
        var options = [];
        
        // iterate over options
        $(this).find(".input-group").each(function() {
            var content = $(this).children(".form-control").val();
            if (content != "") {
                options.push(content);
            }
        })

        element.options = options;
        element.random = false;
        data.elements.push(element);
    });
    return data;
}

// add form elements for users to fill out (not editable)
function addFormElement(element) {
    var container = document.createElement("DIV");
    $(container).appendTo("#form_elements")
    .addClass("card form_element");
    
    $("<div>").addClass("card-header form_question")
    .appendTo(container)
    .text(element.question);

    // $(container).append(document.createElement("BR"));
    
    switch (element.type) {
        case "checkbox":
        case "radio":
            for(opt in element.options) {
                var id = element.name + "_" + opt.toString();
                var value = element.options[opt];
                
                $("<div>",{"class":"form-check"}).append(
                    $("<label>",{"class":"form-check-label"}).append(
                        $("<input>",{
                            "type":element.type,
                            "name":element.name,
                            "id":id,
                            "value":id,
                            "class":"form-check-input"})
                    )
                    .append($("<p>").text(value))
                ).appendTo(container);
            }
            
            break;
    
        case "text":
            $("<input>").attr({"type":element.type, "name":element.name})
            .appendTo(container)
            .addClass("form-control");
        default:
            break;
    }
}