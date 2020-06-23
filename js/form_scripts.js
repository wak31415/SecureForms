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

function addFormElement(element) {
    var container = document.createElement("DIV");
    $(container).appendTo("#form_elements")
    .addClass("form_element")
    .addClass("animate__fadeOutUp");

    var remove_btn = document.createElement("BUTTON");
    remove_btn.innerText = "x Remove Element";
    remove_btn.class = "remove_element";
    $(remove_btn).click(function() {$(this).parent().remove();});
    $(container).append(remove_btn);
    $(container).append(document.createElement("BR"));

    var el_label = document.createElement("P");
    $(el_label).text(element.type)
    .addClass("type")
    .appendTo(container);
    
    $("<input>").attr({"type":"text", "placeholder":"Title"})
    .addClass("form_text")
    .appendTo(container)
    .val(element.question)
    .focus();

    // $(container).append(document.createElement("BR"));
    
    switch (element.type) {
        case "checkbox":
        case "radio":
            for(opt of element.options) {
                createOptionField(container, opt);
            }
            createOptionField(container,"");
            
            break;
    
        default:
            break;
    }
    
}

function createOptionField(form_el_container,val,trigger_field=false) {
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
    $(option_container).addClass("option_container")
    .appendTo(form_el_container);

    var option_field = document.createElement("INPUT");
    var rem_option = document.createElement("BUTTON");
    
    $(option_field).attr({"type":"text", "class":"option_field"})
    .appendTo(option_container)
    .val(val)
    .on("keypress", function() {createOptionField(form_el_container,"",this)})
    .on("blur", function() {
        $(option_field).parent().next("div").children("input").focus();
    });

    $(rem_option).text("X")
    .appendTo(option_container)
    .click(function() {$(this).parent().remove()});

}

function updateFormData() {
    var name_counter = 0;
    var data = {"elements":[], "params":{}}
    $("#form_elements").children("div").each(function(){
        var element = {};
        element.name = (name_counter++).toString();
        element.type = $(this).children(".type").text();
        element.required = false;
        element.question = $(this).children(".form_text").val();
        
        var options = [];
        
        // iterate over options
        $(this).children(".option_container").each(function() {
            var content = $(this).children(".option_field").val();
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