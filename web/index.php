<!-- CoreProtect LWI by SimonOrJ. All Rights Reserved. -->
<?php
// Testing script
error_reporting(-1);ini_set('display_errors', 'On');

// Get the configuration variable.
$c = require "config.php";

// Check login status.
require "res/php/login.php";
$login = new Login($c);

if ($login->check() !== true && $c['login']['required']) {
    header("Location: login.php?landing=.%2F");
    exit();
}

/* Psuedocode:
if not logged in
    redirect to 
    exit
*/

// Get the template file and initiate its class.
require "res/php/webtemplate.php";
$template = new WebTemplate($c, $login->username());

// Is the lookup options in the GET request? (Check only via "action")
$gr = !empty($_GET['a']); // idk what "gr" stands for...
?>
<!DOCTYPE html>
<html>
<?php 
// Get the head from template.

$template->head();
?>
<body data-spy="scroll" data-target="#row-pages">

<?php
$template->navbar();
?>
<nav id="scroll-nav" class="navbar navbar-dark bg-inverse navbar-fixed-bottom">
  <div class="container-fluid">
    <ul id="row-pages" class="nav navbar-nav">
      <li class="nav-item"><a class="nav-link" href="#top">Top</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<?php
// Rejected from setup.php
if (!empty($_GET['from']) && $_GET['from'] === "setup.php" && $c['user'][$login->username()]['perm'] !== 0):
?>
<div class="alert alert-info alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Info:</strong> You were redirected from <code>setup.php</code> because you did not have sufficient permission.  Please consult your administrator.</div>
<?php endif;
// If it doesn't have write permission to the ./cache directory
if (!is_writable("./cache/")):?>
<!-- Write alert box -->
<div class="alert alert-warning alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Notice:</strong> The directory <code>./cache/</code> is not writable. Lookup may take marginally longer to process, and autocomplete will not have updated data. Please refer to readme.md for setup information.</div>
<?php endif;?>

<!-- Lookup Form -->
<div id="lookupForm" class="card">
<div class="card-header"><span class="h4 card-title">Make a Lookup</span></div>
<form id="lookup" class="card-block" role="form" method="get" action="./">
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="lServer">Server Name</label>
  <div class="col-sm-10">
    <select class="form-control" id="lServer" name="server">
<?php
$sv = array(scandir("server/"), isset($_GET['server']), "");
for ($i = 2; $i < count($sv[0]); $i++) {
    $sv[2] = substr($sv[0][$i], 0, strlen($sv[0][$i])-4);
    echo "<option";
    if ($sv[1] && $_GET['server'] === $sv[2])
        echo " selected";
    echo ">".$sv[2]."</option>";
}
?>
    </select>
  </div>
</div>
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Actions</div>
  <div class="dtButtons btn-group col-lg-10">
    <label class="btn btn-secondary" for="abl" data-toggle="tooltip" data-placement="top" title="Block manipulation">
      <input type="checkbox" id="abl" name="a[]" value="block"<?php if (!$gr || in_array("block",$_GET['a'])) echo " checked";?>>
      Block
    </label>
    <label class="btn btn-secondary" for="acl" data-toggle="tooltip" data-placement="top" title="Clickable events (e.g. Chest, door, buttons)">
      <input type="checkbox" id="acl" name="a[]" value="click"<?php if ($gr && in_array("click",$_GET['a'])) echo " checked";?>>
      Click
    </label>
    <label class="btn btn-secondary" for="acn" data-toggle="tooltip" data-placement="top" title="Item transaction from containers">
      <input type="checkbox" id="acn" name="a[]" value="container"<?php if ($gr && in_array("container",$_GET['a'])) echo " checked";?>>
      Container
    </label>
    <label class="btn btn-secondary" for="ach">
      <input type="checkbox" id="ach" name="a[]" value="chat"<?php if ($gr && in_array("chat",$_GET['a'])) echo " checked";?>>
      Chat
    </label>
    <label class="btn btn-secondary" for="acm">
      <input type="checkbox" id="acm" name="a[]" value="command"<?php if ($gr && in_array("command",$_GET['a'])) echo " checked";?>>
      Command
    </label>
    <label class="btn btn-secondary" for="akl" data-toggle="tooltip" data-placement="top" title="Mob kills">
      <input type="checkbox" id="akl" name="a[]" value="kill"<?php if ($gr && in_array("kill",$_GET['a'])) echo " checked";?>>
      Kill
    </label>
    <label class="btn btn-secondary" for="ass" data-toggle="tooltip" data-placement="top" title="Player login/logout event">
      <input type="checkbox" id="ass" name="a[]" value="session"<?php if ($gr && in_array("session",$_GET['a'])) echo " checked";?>>
      Session
    </label>
    <label class="btn btn-secondary" for="aus" data-toggle="tooltip" data-placement="top" title="Username change history">
      <input type="checkbox" id="aus" name="a[]" value="username"<?php if ($gr && in_array("username",$_GET['a'])) echo " checked";?>>
      Username
    </label>
  </div>
</div>
<div class="form-group row">
  <div class="col-lg-2 form-control-label">Toggle</div>
  <div class="col-lg-10">
    <button class="btn btn-secondary" type="button" id="rcToggle">Radius/Corners</button>
    <span class="dtButtons btn-group">
      <label class="btn btn-outline-success" for="rbt">
        <input type="radio" id="rbt" name="rollback" value="1"<?php if ($gr && $_GET["rollback"] === "1") echo " checked";?>>
        <span class="glyphicon glyphicon-ok"></span>
      </label>
      <label class="btn btn-secondary active" for="rb">
        <input type="radio" id="rb" name="rollback" value=""<?php if (!$gr || empty($_GET["rollback"])) echo " checked";?>>
        Rollback
      </label>
      <label class="btn btn-outline-secondary" for="rbf">
        <input type="radio" id="rbf" name="rollback" value="0"<?php if ($gr && $_GET["rollback"] === "0") echo " checked";?>>
        <span class="glyphicon glyphicon-minus"></span>
      </label>
    </span>
  </div>
</div>
<div class="form-group row">
    <label class="col-sm-2 form-control-label" for="x1" id="corner1">Center / Corner 1</label>
    <div class="col-lg-4 col-sm-10 groups-line" id="c1">
      <div class="input-group">
        <input class="form-control" type="number" id="x1" name="xyz[]" placeholder="x"<?php if ($gr && isset($_GET["xyz"][0])) echo ' value="'.$_GET["xyz"][0].'"';?>>
          <span class="input-group-btn" style="width:0"></span>
        <input class="form-control" type="number" id="y1" name="xyz[]" placeholder="y"<?php if ($gr && isset($_GET["xyz"][1])) echo ' value="'.$_GET["xyz"][1].'"';?>>
          <span class="input-group-btn" style="width:0"></span>
        <input class="form-control" type="number" id="z1" name="xyz[]" placeholder="z"<?php if ($gr && isset($_GET["xyz"][2])) echo ' value="'.$_GET["xyz"][2].'"';?>>
      </div>
    </div>
    <label class="col-sm-2 form-control-label" for="x2" id="corner2">Radius / Corner 2</label>
    <div class="col-lg-4 col-sm-10" id="c2">
      <div class="input-group">
        <input class="form-control" type="number" id="x2" name="xyz2[]" placeholder="Radius or x"<?php if ($gr && (isset($_GET["xyz"][0]) || isset($_GET["r"]))) echo ' value="'.(isset($_GET["xyz"][0]) ? $_GET["xyz2"][0] : $_GET["r"]).'"';?>>
        <span class="input-group-btn c2" style="width:0"></span>
        <input class="form-control c2" type="number" id="y2" name="xyz2[]" placeholder="y"<?php if ($gr && isset($_GET["xyz2"][1])) echo' value="'.$_GET["xyz2"][1].'"';?>>
        <span class="input-group-btn c2" style="width:0"></span>
        <input class="form-control c2" type="number" id="z2" name="xyz2[]" placeholder="z"<?php if ($gr && isset($_GET["xyz2"][2])) echo ' value="'.$_GET["xyz2"][2].'"';?>>
      </div>
    </div>
</div>
<div class="form-group row">
  <label class="col-xs-2 form-control-label" for="wid">World</label>
  <div class="col-xs-10">
    <input class="form-control autocomplete" data-qftr="world" type="text" id="wid" name="wid" placeholder="world"<?php if ($gr && isset($_GET["wid"])) echo ' value="'.$_GET["wid"].'"';?>>
  </div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="usr">Users</label>
  <div class="col-lg-10">
    <div class="input-group">
      <span class="dtButtons input-group-btn">
        <label class="btn btn-secondary" for="eus">
          <input type="checkbox" id="eus" name="e[]" value="u"<?php if ($gr && in_array("u",$_GET["e"])) echo " checked";?>>
          Exclude
        </label>
      </span>
      <input class="form-control autocomplete" data-qftr="user" type="text" pattern="((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16}))(,\s?((#[a-zA-Z_]+)|([a-zA-Z0-9_]{2,16})))*" id="usr" name="u" placeholder="Separate by single comma(,)"<?php if ($gr && isset($_GET["u"])) echo ' value="'.$_GET["u"].'"';?>>
    </div>
  </div>
</div>
<div class="form-group row">
  <label class="col-lg-2 form-control-label" for="blk">Blocks</label>
  <div class="col-lg-10">
    <div class="input-group">
      <span class="dtButtons input-group-btn">
        <label class="btn btn-secondary" for="ebl">
          <input type="checkbox" id="ebl" name="e[]" value="b"<?php if ($gr && in_array("b",$_GET["e"])) echo " checked";?>>
          Exclude
        </label>
      </span>
      <input class="form-control autocomplete" data-qftr="material" type="text" pattern="([^:]+:[^:,]+)+" id="blk" name="b" placeholder="minecraft:<block> - Separate by single comma(,)"<?php if ($gr && isset($_GET["b"])) echo ' value="'.$_GET["b"].'"';?>>
    </div>
  </div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="kwd">Keyword</label>
  <div class="col-sm-10">
    <input class="form-control" type="text" id="kwd" name="keyword"<?php if ($gr && isset($_GET["keyword"])) echo ' value="'.$_GET["keyword"].'"';?> data-toggle="tooltip" data-placement="top" title='Space [&nbsp;] for AND. Comma [,] for OR. Enclose terms in quotes [""] to escape spaces/commas. Only applies to chat and command.'></div>
</div>
<div class="form-group row">
  <label class="col-sm-2 form-control-label" for="date">Date/Time</label>
  <div class="col-lg-4 col-sm-10 groups-line">
    <div class="input-group">
      <span class="dtButtons input-group-btn">
        <label class="btn btn-secondary" for="trv">
          <input type="checkbox" id="trv" name="asendt"<?php if ($gr && isset($_GET["asendt"]) && $_GET["asendt"] === "on") echo " checked";?>>
          Reverse
        </label>
      </span>
      <input class="form-control" type="datetime-local" id="date" name="t" placeholder="0000-00-00T00:00:00"<?php if ($gr && isset($_GET["t"])) echo ' value="'.$_GET["t"].'"';?>>
    </div>
  </div>
  <input type="hidden" name="unixtime" value="on">
  <label class="col-sm-2 form-control-label" for="lim">Limit</label>
  <div class="col-lg-4 col-sm-10">
    <input class="form-control" type="number" id="lim" name="lim" min="1" placeholder="30"<?php if ($gr && $_GET['lim']) echo ' value="'.$_GET['lim'].'"';?>>
  </div>
</div>
<div class="row">
  <div class="offset-sm-2 col-sm-10">
    <input class="btn btn-secondary" type="submit" id="submitBtn" name="newlookup" value="Make a Lookup">
  </div>
</div>
</form>
</div>
</div>

<!-- Output table -->
<div class="container-fluid">
<table id="output" class="table table-sm table-striped">
  <caption id="genTime"></caption>
  <thead class="thead-inverse">
  <tr id="row-0"><th>#</th><th>Time</th><th>User</th><th>Action</th><th>Coordinates / World</th><th>Block/Item:Data</th><th>Amount</th><th>Rollback</th></tr>
  </thead>
  <tbody id="mainTbl"><?php echo isset($mainTbl)?$mainTbl:'<tr><th scope="row">-</th><td colspan="7">Please submit a lookup.</td></tr>';?></tbody>
</table>
</div>

<!-- Load More form -->
<form class="container" id="loadMore"><!--action=""-->
<div class="row">
  <div class="col-sm-offset-2 col-sm-8 form-group input-group">
    <label class="input-group-addon" for="moreLim">load next </label><input class="form-control" type="number" id="moreLim" name="lim" min="1" placeholder="Broken; will work on v0.9.0" disabled>
  </div>
</div>
<div class="form-group row">
  <div class="col-sm-offset-2 col-sm-8">
    <input class="btn btn-secondary" id="loadMoreBtn" type="submit" value="Load more" disabled>
  </div>
</div>
</form>


<div class="container">
<?php if (!empty($un = $login->username())):?>
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Advanced
    </button>
    <?php if ($c['user'][$un]['perm'] <= 1):?>
    <div class="dropdown-menu" aria-labelledby="Advanced">
      <?php if ($c['user'][$un]['perm'] === 0):?>
      <a class="dropdown-item" href="setup.php">Setup</a>
      <?php endif;?>
      <button id="purgeCache" class="dropdown-item list-group-item-danger">Purge server cache</button>
      <button id="purgeCache" class="dropdown-item list-group-item-danger">Purge all cache</button>
    </div>
    <?php endif;?>
  </div>
  <?php endif;?>
  <p>If you encounter any issues, please open an issue or a ticket on the <a href="https://github.com/SimonOrJ/CoreProtect-Lookup-Web-Interface">GitHub project page</a>.<!-- or the <a href="http://dev.bukkit.org/bukkit-plugins/coreprotect-lwi/">Bukkit plugin project page</a>.--><br>This webserver is running PHP <?php echo phpversion();?>.</p>
</div>

<!-- Copyright Message -->
<div class="container-fluid">
<p>&copy; <?php echo str_replace("%year%", date("Y"),$_index["copyright"]);?> &mdash; CoreProtect LWI version 0.9.0-beta<br>Created by <a href="http://simonorj.com/">SimonOrJ</a>.</p>
</div>

<!-- All the scripting needs -->
<?php
// Unset sensetitive information before sending it to the JS.
unset($c['login']);
unset($c['user']);
?>
<script>
// Quick Styling for JS-enabled browser
document.getElementById("corner1").innerHTML = "Center";
document.getElementById("corner2").innerHTML = "Radius";
document.getElementById("c2").className = "col-lg-4 col-sm-10";
// Default: Radius search
a = document.getElementsByClassName("c2");
for(var i = 0; i < a.length; i++) a[i].style.display = "none";
// Add data-toggle attribute to checkboxes (and radio buttons) with dtButtons class
a = document.getElementsByClassName("dtButtons");
for(var i = 0; i < a.length; i++) a[i].setAttribute("data-toggle","buttons");
document.getElementById("x2").setAttribute("placeholder","Radius");
document.getElementById("date").setAttribute("placeholder","")
document.getElementById("date").setAttribute("type","text");
document.getElementById("date").removeAttribute("name");
document.getElementById("loadMoreBtn").setAttribute("disabled","");
// Get variables from the settings

var $config = <?php echo json_encode($c);?>;
$dateFormat = "<?php echo $c['form']['dateFormat'];?>";
$timeFormat = "<?php echo $c['form']['timeFormat'];?>";
$timeDividor = <?php echo $c['form']['timeDividor'];?>*1000;
/*
$dynmapURL = "<?php echo $c['dynmapURL'];?>";
$dynmapZoom = "<?php echo $c['dynmapZoom'];?>";
$dynmapMapName = "<?php echo $c['dynmapMapName'];?>";
*/
$pageInterval = <?php echo $c['form']['pageInterval'];?>;
$fm = <?php echo $gr?"true":"false";?>;
$PHP_$t = <?php echo ($gr&&$_GET["t"]!=="")?' value="'.$_GET["t"].'"':"false";?>;
</script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js">// JQuery</script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js">// Dropdown</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/tether/1.1.1/js/tether.min.js">// Bootstrap dependency</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js" integrity="sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD" crossorigin="anonymous">// Bootstrap (Alpha!)</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment.min.js">// datetime-picker dependency</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js">// Datetime Picker</script>
<script src="res/js/out-table.js"></script>
<script src="res/js/form-handler.js"></script>
</body>
</html>
