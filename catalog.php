<!DOCTYPE HTML>
<head><title>Making a list of tables</title>
<style>
.type {
 width:6em;
 background:#c7f287;
 color:#435a11;
 float:left;
}
.name {
 width:40em;
 background:#e992bc;
 color:#012b12;
}
</style>
</head>
<body>
<?php

echo "<!-- hello world -->\n";
include_once("secrets.php");
echo "<!-- secrets included -->\n";
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
echo "<!-- database connected -->\n";
   $db->query(<<<VTC
    create view total_conflict as
    select a.id2 id1, a.id1 id2, b.ideology ideology2, sum(a.conflict) conflict
    from conflict a, ideology b
    where a.id1 = b.id
    group by id1, id2
VTC
   );
$result = $db->query(<<<IS
 select table_name, "table" type
 from information_schema.tables
 union
 select table_name, "view"
 from information_schema.views
 order by table_name
IS
);
echo "<!-- ".$db->error."-->\n";
while ($row = $result->fetch_assoc()) {
 echo "<div class=\"type\">{$row['type']}</div><div class=\"name\">{$row['table_name']}</div>\n";
}

?>
</body>
</html>

