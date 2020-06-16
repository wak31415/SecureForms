<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/materialize.min.js"></script>
<script>
    // Initialize tabs
    $(document).ready(function(){
        $('.tabs').tabs({ swipeable: true });
    });

    // Initialize side navbar
    $(document).ready(function(){
        $('.sidenav').sidenav();
    });

    // Initialize dropdown content
    $(".dropdown-trigger").dropdown({ hover: false, coverTrigger: false });
        
    // Initialize form selector
    $(document).ready(function(){
        $('select').formSelect();
    });

    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.fixed-action-btn');
        var instances = M.FloatingActionButton.init(elems, {});
    });

    $(document).ready(function(){
        $('.modal').modal();
    });

    $(document).ready(function(){
        $('.tooltipped').tooltip();
    });
       
</script>