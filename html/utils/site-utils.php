<?php
    function redirectTo($lien) {
        ?><script>
            window.onload = function() {
                window.location.href = "<?php echo($lien);?>";
            };
        </script><?php
        die();
    }
?>