<?php
    var_dump(explode("T", "2017-07-04T10:05:14-05:00")[1]);
    var_dump( date("d/m/Y", strtotime(explode("T", "2017-07-04T10:05:14-05:00")[0])) );
    var_dump( substr(explode("T", "2017-07-04T10:05:14-05:00")[1], 0, 5) );
    /* print(SUBSTRING_INDEX("2017-07-04T10:05:14-05:00", "T", -1)); */
?>