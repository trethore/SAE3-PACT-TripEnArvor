<?php 
    function printInConsole($text){
        ?>
        <script>
            window.onload = function() {
                console.log(<?php echo $text; ?>);
            };
        </script>
        <?php
    }
?>