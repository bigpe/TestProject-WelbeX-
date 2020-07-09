<?php
$ini = include('configs/config.php'); //Load Configs
require_once('db.php'); $db = new database(); //Load Database
$dataBaseName = $ini['db']['dbname'];
$dataPerPage = $ini['dataPerPage'];
if(isset($_POST['tableData'])) {
    isset($_POST['tableData']['tableBox']['offset'])
        ? $offset = (int)$_POST['tableData']['tableBox']['offset'] * $dataPerPage : $offset = 0;
    isset($_POST['tableData']) ? $tableData = $_POST['tableData'] : $tableData = null;
    $tableName = $tableData['tableBox']['tableName'];
    $columns = $db->getTableFieldsWT($tableName); //Load All Table Fields via Table Name From Config
    $filterData = $tableData['filterBox'];
    for($i = 0; $i < count($filterData); $i++){ //Filter Shielding
        foreach ($filterData[$i] as $t => $k)
            $filterData[$i][$t] = shieldData($filterData[$i][$t]);
    }
    $filterRules = [];
    foreach ($filterData as $line) {
        if(!isset($columns[$line['column']])) { //Column Not Found - Redirect
            tableRender($tableName, $columns, [], 0, true);
            die();
        }
        $columnType = $columns[$line['column']];
        $line['action'] == 'like' ? $l = '%' : $l = null;
        if (!empty($line['query']) || is_numeric($line['query'])) {
            $line['query'] = shieldData($line['query']); //Query Shielding
            $filterRules[] = "{$line['column']} {$line['action']} '$l{$line['query']}$l'";
        }
    }
    $filterRules ? $filterQuery = "WHERE " . join(" AND ", $filterRules) : $filterQuery = null;
    $sortData = $tableData['sortBox'];
    for($i = 0; $i < count($sortData); $i++){ //Sort Shielding
        foreach ($sortData[$i] as $t => $k)
            $sortData[$i][$t] = shieldData($sortData[$i][$t]);
    }
    $sortRules = [];
    foreach ($sortData as $line)
        $sortRules[] = "{$line['column']} {$line['order']}";
    $sortQuery = "ORDER BY " . join(", ", $sortRules);
    $cols = [];
    foreach ($columns as $c => $k)
        $cols[] = $c;
    $cols = implode(', ', $cols);
    $tableData['tableBox']['tableName'] = shieldData($tableData['tableBox']['tableName']);
    $limitQuery = "LIMIT $dataPerPage OFFSET $offset";
    try{
        $dataCount = $db->dbReadSingle("SELECT COUNT(*) FROM $tableName $filterQuery $sortQuery");
        $tableData = $db->dbReadMultiple("SELECT $cols FROM $tableName $filterQuery $sortQuery $limitQuery");
        tableRender($tableName, $columns, $tableData, $dataCount);
    } catch (Exception $e) {
        print($e->getMessage()); //Redirect Query Not Success
        tableRender($tableName, $columns, [], 0, $e->getCode());
    }
    print("SELECT $cols FROM $tableName $filterQuery $sortQuery $limitQuery"); //Resulted Query From Debug
}

function shieldData($variable){
    $variable = addslashes(explode(' ', $variable)[0]); //Delimit && Add Slashes To Avoid Problems
    return ($variable);
}

function tableRender($tableName, $tableFields, $tableData, $dataCount, $error = false){?>
<div class="container" style="margin-bottom: 4rem; margin-top: -1rem" id="mainTable">
    <!--Pagination-->
    <?php global $offset; global $dataPerPage;
    $pageCount = floor($dataCount / $dataPerPage);
    !$tableData ? : !$pageCount ? : print("<nav class='my-3'><ul class='pagination justify-content-center'>");
    for($i = 1; $i <= $dataCount / $dataPerPage; $i++) {
        $a = null;
        if(!$offset && $i == 1)
            $a = 'active disabled';
        print("<li class='page-item $a'><a class='page-link' href='#mainTable' onclick='pagination(this)'>$i</a></li>");
    }
    !$tableData ? : !$pageCount ? : print("</ul></nav>"); ?>
    <!--Pagination-->
    <!--TableResult-->
    <div class="card text-center text-light border-0 shadow">
            <?php global $dataBaseName;
            $tableData ? $color = 'success' : $color = 'danger';
            print("<h4 class='card-header bg-$color text-white shadow'>
                        <i class=\"fas fa-database\"></i> $dataBaseName 
                        <i class=\"fas fa-long-arrow-alt-right\"></i> 
                        <i class=\"fas fa-table\"></i> $tableName <i class=\"fas fa-long-arrow-alt-right\"></i> $dataCount</h4>"); ?>
            <div class="table-responsive table-dark">
                <table class="table table-hover table-dark">
                    <thead>
                    <tr>
                        <?php
                        $error ? $pl = "Error Code - $error <br>(Try to Reset)" : $pl = "Empty";
                        if(!$tableData) print("<tr><th scope='row' class='text-center'>$pl</th></tr>");
                        else {
                            foreach ($tableFields as $c => $k) print("<th scope='col' class='bg-secondary'>$c</th>");
                        }?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($tableData as $tD){
                        print("<tr>");
                        foreach ($tD as $t)
                            print("<td>$t</td>");
                        print("</tr>");
                    }
                    ?>
                    </tbody>
                </table>
            </div>
    </div>
    <!--TableResult-->
    <!--Pagination-->
    <?php global $offset;
    $pageCount = floor($dataCount / $dataPerPage);
    !$tableData ? : !$pageCount ? : print("<nav class='my-3'><ul class='pagination justify-content-center'>");
    for($i = 1; $i <= $dataCount / $dataPerPage; $i++) {
        $a = null;
        if(!$offset && $i == 1)
            $a = 'active disabled';
        print("<li class='page-item $a'><a class='page-link' href='#footer' onclick='pagination(this)'>$i</a></li>");
    }
    !$tableData ? : !$pageCount ? : print("</ul></nav>"); ?>
    <!--Pagination-->
</div>
<?php
}
?>
