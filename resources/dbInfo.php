<?php
$conn = mysqli_connect("114.203.87.165", "cw_user", "chdnjsdbwj", "cw_db");

mysqli_query($conn, "set session character_set_connection=euckr;");
mysqli_query($conn, "set session character_set_results=euckr;");
mysqli_query($conn, "set session character_set_client=euckr;");
?>