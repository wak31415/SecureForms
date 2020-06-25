<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/classes/Survey.php");
    include("includes/handlers.php");
    
    $user = new User($con, $userLoggedIn);
    $survey = new Survey($con, $_GET['survey_id']);

    $id = $survey->data['id'];
    $submissions_query = mysqli_query($con,"SELECT * FROM submissions WHERE survey_id='$id'");

    
?>


<div class="container bg-light">
<h1>Form Submission Results:</h1>

<div class="table-responsive">
    <table id="submission_table" class="table table-bordered table-striped"></table>
</div>
</div>

<script>

var sec_encrypted = getCookie("privkey")
// password used to encrypt the private key sec
var privkey_password = getCookie("privkey_password")

var pub = getCookie("pubkey")
// deserialize public key
pub = new sjcl.ecc.elGamal.publicKey(
    sjcl.ecc.curves.c256, 
    sjcl.codec.base64.toBits(pub)
)

// decrypt secret key
var sec = sjcl.decrypt(privkey_password, sec_encrypted)
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

var submission_objs = [];

for(sub of submissions) {
    var sub_split = sub.split("&");
    // console.log(sub_split);
    var obj = {};
    // initialize response object
    for(var d_key in sub_split) {
        var k = sub_split[d_key].split("=")[0];
        if (data.elements[k].type == "radio"||data.elements[k].type=="checkbox") {
            obj[k] = [];
        }
    }

    for(var d_key in sub_split) {
        var k = sub_split[d_key].split("=")[0];
        var v = sub_split[d_key].split("=")[1];
        if (data.elements[k].type == "radio"||data.elements[k].type=="checkbox") {
            v = v.substr(2);
            obj[k].push(Number(v));
        } else {
            obj[k] = v.replace("%20"," ");
        }
    }
    submission_objs.push(obj);
}

// create table

// var submission_table = document.createElement("TABLE");
var header = document.createElement("THEAD");
var row = document.createElement("TR");
for(question of data.elements) {
    $(row).append($("<th>").text(question.question));
    // console.log(question.question);
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
        } else {
            value = sub[entry];
        }
        $(row).append($("<td>").text(value));
        // $(row).append()
    }
    $(tbody).append(row);
}
$("#submission_table").append(tbody);


</script>