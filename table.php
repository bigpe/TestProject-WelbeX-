<?php
$ini = include('configs/config.php'); //Load Configs
require_once('db.php'); $db = new database(); //Load Database
if(isset($_POST['tableData'])) {
    isset($_POST['tableData']) ? $tableData = $_POST['tableData'] : $tableData = null;
    $tableName = $tableData['tableBox']['tableName'];
    $columns = $db->getTableFieldsWT($tableName); //Load All Table Fields via Table Name From Config
    $filterData = $tableData['filterBox'];
    $filterRules = [];
    foreach ($filterData as $line) {
        $columnType = $columns[$line['column']];
        $line['action'] == 'like' ? $l = '%' : $l = null;
        if (!empty($line['query']) || is_numeric($line['query'])) {
            $line['query'] = addslashes($line['query']); //Query Slash Shielding
            $filterRules[] = "{$line['column']} {$line['action']} '$l{$line['query']}$l'";
        }
    }
    $filterRules ? $filterQuery = "WHERE " . join(" AND ", $filterRules) : $filterQuery = null;
    $sortData = $tableData['sortBox'];
    $sortRules = [];
    foreach ($sortData as $line)
        $sortRules[] = "{$line['column']} {$line['order']}";
    $sortQuery = "ORDER BY " . join(", ", $sortRules);
    $cols = [];
    foreach ($columns as $c => $k)
        $cols[] = $c;
    $cols = implode(', ', $cols);
    $tableData = $db->dbReadMultiple("SELECT $cols FROM $tableName $filterQuery $sortQuery");
    tableRender($tableName, $columns, $tableData);
    print("SELECT $cols FROM $tableName $filterQuery $sortQuery");
    $db->getTables();
}

function tableRender($tableName, $tableFields, $tableData){?>
<div class="container" style="margin-bottom: 4rem; margin-top: -1rem" id="mainTable">
    <div class="card text-center text-light border-0 shadow">
            <?php
            print("<h4 class='card-header bg-danger text-white shadow'><i class=\"fas fa-table\"></i> $tableName</h4>"); ?>
            <div class="table-responsive table-dark">
                <table class="table table-hover table-dark">
                    <thead>
                    <tr>
                        <?php foreach($tableFields as $c => $k) print("<th scope='col'>$c</th>"); ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tableData as $tD){
                        print("<tr>");
                        foreach ($tD as $t)
                            print("<td>$t</td>");
                        print("</tr>");
                    }?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<?php
}
?>
