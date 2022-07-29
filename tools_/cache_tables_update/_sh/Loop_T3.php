<?php

while (true){
    shell_exec("php \"C:/xampp/htdocs/curadio_stats/tools/cache_tables_update/main.php\" EXTRACT_STAT_DATA");
    sleep(5 * 60 );
}
