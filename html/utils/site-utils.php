<?php
    function redirectTo($lien) {
        ?><script>
            window.onload = function() {
                window.location.href = <?php $lien?>;
            };
        </script><?php
    }
?>