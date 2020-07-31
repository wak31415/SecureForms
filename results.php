<?php
    include("includes/header.php");
    include("includes/navbar.php");
    include("includes/classes/User.php");
    include("includes/classes/Survey.php");
    include("includes/handlers.php");
    include("includes/redirect.php");
    
    $user = new User($con, $userLoggedIn);
    $survey = new Survey($con, $_GET['survey_id']);

    $id = $survey->data['id'];
    $submissions_query = mysqli_query($con,"SELECT * FROM submissions WHERE survey_id='$id'");
?>


<div class="container bg-light">

    <h1>Form Submission Results</h1>

    <a href="form_create.php?survey_id=<?php echo $_GET['survey_id']; ?>" class="btn btn-outline-primary">
        <i class="fa fa-chevron-left" aria-hidden="true"></i> Edit Form
    </a> <br><br>

    <!-- Nav tabs -->
    <ul class="nav nav-pills nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#summary">Summary</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#individual">Individual Responses <span class="badge badge-primary" id="submission-count"></span></a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane container active" id="summary">
        </div>
        <div class="tab-pane container fade" id="individual">
            <div class="table-responsive">
                <table id="submission_table" class="table table-hover"></table>
            </div>
        </div>
    </div>


</div>

<script>

var sec_encrypted = getCookie("privkey")
// password used to encrypt the private key sec
// var privkey_password = getCookie("privkey_password")

var pub = getCookie("pubkey")
// deserialize public key
pub = new sjcl.ecc.elGamal.publicKey(
    sjcl.ecc.curves.c256, 
    sjcl.codec.base64.toBits(pub)
)

// decrypt secret key
var sec = sjcl.decrypt(sessionStorage.privkey_password, sec_encrypted)
// deserialize secret key
sec = new sjcl.ecc.elGamal.secretKey(
    sjcl.ecc.curves.c256,
    sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(sec))
)

var submissions = [];

<?php
    $key = $survey->data['encryption_key'];
    $data = $survey->data['data'];
    echo "var data = '$data'\n";
    echo "var key = '$key'\n";
?>

key = sjcl.decrypt(sec,key);
data = sjcl.decrypt(key, data);
data = JSON.parse(data);

<?php
    while($row = mysqli_fetch_assoc($submissions_query)) {
        $entry = $row['data'];
        echo "submissions.push(sjcl.decrypt(sec,'".$entry."'))\n";
    }
?>

$("#submission-count").text(submissions.length);

var submission_objs = [];
var summary = {};


for(sub of submissions) {
    var sub_split = sub.split("&");
    var obj = {};

    // initialize response object
    for(var d_key in sub_split) {
        var k = Number(sub_split[d_key].split("=")[0]);
        if (data.elements[k].type == "radio"||data.elements[k].type=="checkbox") {
            obj[k] = [];
            if (!(k in summary)) {
                summary[k] = {}
            }
        }
    }

    for(var d_key in sub_split) {
        var k = Number(sub_split[d_key].split("=")[0]);
        var v = sub_split[d_key].split("=")[1];
        if (data.elements[k].type == "radio"||data.elements[k].type=="checkbox") {
            v = Number(v.substr(2));
            obj[k].push(v);
            if (v in summary[k]) {
                summary[k][v] += 1;
            } else {
                summary[k][v] = 1;
            }
        } else {
            obj[k] = unescape(v);
        }
    }
    submission_objs.push(obj);
}

function shuffleArray(array) { 
    for (var i = array.length - 1; i > 0; i--) { 

        // Generate random number  
        var j = Math.floor(Math.random() * (i + 1)); 

        var temp = array[i]; 
        array[i] = array[j]; 
        array[j] = temp; 
    } 

    return array; 
}

// Create Charts for each radio or checkbox question

var row = document.createElement("DIV");
$(row).addClass("row")
.appendTo("#summary");

// const colors = ["#264653","#2a9d8f","#e9c46a","#f4a261","#e76f51"];
const colors = ["#1a535c","#4ecdc4","#ff6b6b","#ffe66d", "#E2DADB", "#14080E","#3F8EFC"]

function createPieChart(question, data) {
    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: question.options,
            datasets: [{
                data: Object.values(data),
                backgroundColor: colors
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'top',
                align: 'start'
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 20,
                    right: 0,
                    left: 0
                }
            },
            title: {
                display: true,
                text: question.question
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        //get the concerned dataset
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        //calculate the total of this data set
                        var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                            return previousValue + currentValue;
                        });
                        //get the current items value
                        var currentValue = dataset.data[tooltipItem.index];
                        //calculate the precentage based on the total and current item, also this does a rough rounding to give a whole number
                        var percentage = Math.floor(((currentValue/total) * 100)+0.5);

                        return percentage + "%  ("+currentValue+")";
                    }
                }
            }
        }
    });
    return chart;
}

function createBarChart(question, data, ctx) {
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: question.options,
            datasets: [{
                data: Object.values(data),
                backgroundColor: colors
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            layout: {
                padding: {
                    top: 10,
                    bottom: 20,
                    right: 10,
                    left: 10
                }
            },
            title: {
                display: true,
                text: question.question
            },
            scales: {
                yAxes: [{
                    type: 'linear',
                    ticks: {
                        min: 0,
                        stepSize: 1
                    },
                    gridLines: {display: false}
                }],
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        //get the concerned dataset
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        //calculate the total of this data set
                        var total = submissions.length;
                        //get the current items value
                        var currentValue = dataset.data[tooltipItem.index];
                        //calculate the precentage based on the total and current item, also this does a rough rounding to give a whole number
                        var percentage = Math.floor(((currentValue/total) * 100)+0.5);

                        return percentage + "%  ("+currentValue+")";
                    }
                }
            }
        }
    });
    return chart;
}

for (q in summary) {
    if (q != 0 && q%2==0) {
        row = document.createElement("DIV");
        $(row).addClass("row")
        .appendTo("#summary");
    }
    var col = document.createElement("DIV");
    $(col).addClass("col-md-6")
    .appendTo(row);

    var container = document.createElement("DIV");
    $(container).addClass("card shadow-sm chart-element-container")
    .appendTo(col);

    var ctx = document.createElement("CANVAS");
    $(ctx).attr("id",q);

    if (data.elements[q].type == "radio") {
        var chart = createPieChart(data.elements[q],summary[q],ctx);
    } else {
        var chart = createBarChart(data.elements[q],summary[q],ctx);
    }
    $(container).append(ctx);
}

function beforePrint () {
    for (const id in Chart.instances) {
        Chart.instances[id].resize();
    }
}

if (window.matchMedia) {
    let mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener((mql) => {
        if (mql.matches) {
            beforePrint();
        }
    })
}

window.onbeforeprint = beforePrint;

// create table

// var submission_table = document.createElement("TABLE");
var header = document.createElement("THEAD");
$(header).addClass("thead-light");
var row = document.createElement("TR");

for(question of data.elements) {
    $(row).append($("<th>").text(question.question));
}

$(header).append(row);
$("#submission_table").append(header);

var tbody = document.createElement("TBODY");
for(sub of submission_objs) {
    var row = document.createElement("TR");
    for (entry in sub) {
        var value;
        if (data.elements[entry].type == "radio"||data.elements[entry].type=="checkbox") {
            value = sub[entry].map(function(x) {return data.elements[entry].options[x]});
            if (data.elements[entry].type=="checkbox") {
                var cell = $("<td>");
                $(cell).appendTo(row);
                for (item of value) {
                    var itemBadge = document.createElement("SPAN");
                    $(itemBadge).addClass("badge badge-secondary")
                    .appendTo(cell)
                    .text(item);
                }
            } else {
                $(row).append($("<td>").text(value));
            }
        } else {
            value = sub[entry];
            $(row).append($("<td>").text(value));
        }
    }
    $(tbody).append(row);
}
$("#submission_table").append(tbody);

</script>