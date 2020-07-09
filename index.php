<?php require_once ('db.php'); $db = new database(); //Load Database
$tables = $db->getTables(); //Load Tables
$columns = $db->getTableFields(isset($_POST['tableName']) ? $_POST['tableName'] : $tables[0]); //Load Fields
$columnsCount = count($columns); ?>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS only -->
    <link href="css/bootstrap.css" type="text/css" rel="stylesheet">
    <link href="css/default.css" type="text/css" rel="stylesheet">
    <!-- JS, Popper.js, jQuery, Particled.js, FontwAsesome.js, Bootstrap.js -->
    <script src="js/jQuery.js"></script>
    <script src="js/particles.js"></script>
    <script src="js/FontAwesome.js" data-auto-replace-svg="nest"></script>
    <script src="js/Popper.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script src="js/Bootstrap.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
            crossorigin="anonymous"></script>
    <script src="js/default.js" type="text/javascript"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip() //ToolTips Load
        })
    </script>
</head>
<div id="particles-js"></div>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark flex-column justify-content-center py-2">
        <button type="button" class="btn btn-outline-light">#SPA Table for WelbeX</button>
    </nav>
</header>
<div class="container position-relative py-2">
    <div class="jumbotron jumbotron-fluid px-0 py-0 my-3 text-black mb-0 text-center bg-transparent">
    <!--Main Wrap-->
    </div>
    <div class="jumbotron jumbotron-fluid px-3 py-3 my-3 rounded text-white mb-0 text-center bg-transparent">
        <form id="tableBox_Form" class="mb-n1">
            <h4 class="card-header bg-dark"><i class="fas fa-scroll"></i> Table</h4>
            <div id="tableBox">
                <div class="input-group mb-1" id="tableBox_Main">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01"><i class="fas fa-table"></i></label>
                    </div>
                    <select class="custom-select text-center text tableBox" id="inputGroupSelect01" name="tableName"
                            onchange="refreshPage(this, 'tableBox')">
                        <?php foreach ($tables as $t) //Load All Tables
                            print("<option>$t</option>"); ?>
                    </select>
                </div>
            </div>
            <?php print("<script>loadAppendLimit('tableBox')</script>"); //Initialize Box?>
        </form>
        <form id="sortBox_Form">
            <h4 class="card-header bg-dark"><i class="fas fa-sort"></i> Sort</h4>
            <div id="sortBox">
                <div class="input-group mb-1" id="sortBox_Main">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01"><i class="fas fa-columns"></i></label>
                    </div>
                    <select class="custom-select text-center text sortBox" id="inputGroupSelect01" name="column">
                        <?php foreach ($columns as $c) //Load All Columns
                            print("<option>$c</option>"); ?>
                    </select>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01"><i class="fas fa-wrench"></i></label>
                    </div>
                    <select class="custom-select text-center text" id="inputGroupSelect01" name="order">
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </div>
            </div>
            <?php print("<script>loadAppendLimit('sortBox', '$columnsCount')</script>"); //Set Limit To Append Blocks ?>
            <div class="btn btn-outline-light my-3 btn-block border-0" id="addNewSort" append_to="sortBox"
                 onclick="appendHashedNode(this)"><i class="fas fa-plus-square"></i></div>
        </form>
        <form id="filterBox_Form">
            <h4 class="card-header bg-dark"><i class="fas fa-filter"></i> Filter</h4>
            <div id="filterBox">
                <div class="input-group mb-1" id="filterBox_Main">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01"><i class="fas fa-columns"></i></label>
                    </div>
                    <select class="custom-select text-center text filterBox" id="inputGroupSelect01" name="column">
                        <?php foreach ($columns as $c) //Load All Columns
                            print("<option>$c</option>"); ?>
                    </select>
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01"><i class="fas fa-mortar-pestle"></i></label>
                    </div>
                    <select class="custom-select text-center text" id="inputGroupSelect01" name="action">
                        <option>=</option>
                        <option><</option>
                        <option>></option>
                        <option value="like">Contain</option>
                    </select>
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-vial"></i></div>
                    </div>
                    <input class="form-control text-center" type="text" placeholder="Filter Query" name="query">
                </div>
            </div>
            <?php print("<script>loadAppendLimit('filterBox', '$columnsCount')</script>"); //Set Limit To Append Blocks?>
            <div class="input-group-prepend">
                <div class="btn btn-outline-light my-3 btn-block border-0" append_to="filterBox"
                     onclick="appendHashedNode(this)"><i class="fas fa-plus-square"></i></div>
            </div>
            <div class="input-group-prepend">
                <button type="submit" class="btn btn-success my-1 mx-2 shadow btn-block" id="process"><i class="fas fa-check"></i> Execute</button>
                <div class="btn btn-danger my-1 shadow btn-block mx-2" id="reset"
                     onclick="refreshPage(document.getElementsByClassName('tableBox')[0], 'tableBox', true)"><i class="fas fa-sync-alt"></i> Reset</div>
            </div>
        </form>
    </div>
</div>
<div class="container" style="margin-bottom: 4rem; margin-top: -1rem" id="mainTable">
</div>
<footer id='footer' class="py-2 bg-dark fixed-bottom">
    <div class="container text-center">
        <a class="badge" href="https://vk.com/bigpe" target="_blank">
            <button type="button" class="btn btn-outline-primary rounded-pill" data-toggle="tooltip" data-placement="top" title="Vkontakte">
                <i class="fab fa-vk"></i>
            </button>
        </a>
        <a class="badge" href="https://t-do.ru/bigpebro" target="_blank">
            <button type="button" class="btn btn-outline-info rounded-pill" data-toggle="tooltip" data-placement="top" title="Telegram">
                <i class="fab fa-telegram-plane"></i>
            </button>
        </a>
        <a class="badge" href="mailto:bigpewm@gmail.com?subject=About Cooperation">
            <button type="button" class="btn btn-outline-light rounded-pill" data-toggle="tooltip" data-placement="top" title="Mail">
                <i class="fas fa-envelope"></i>
            </button>
        </a>
        <a class="badge" href="https://github.com/bigpe/" target="_blank">
            <button type="button" class="btn btn-outline-light rounded-pill" data-toggle="tooltip" data-placement="top" title="Github">
                <i class="fab fa-github-alt"></i>
            </button>
        </a>
    </div>
</footer>
<script type="text/javascript">
    particlesJS.load('particles-js', '/configs/particles.json', function() {}); //Load Particles
</script>
