<html>

<header>
    <link href="home.css" type="text/css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/af5a6f7cd6.js" crossorigin="anonymous"></script>
</header>

<body>
<?php

function runQuery($username, $password, $database, $query, $showerror = TRUE) 
{
    $c = oci_connect($username, $password, $database);
    if (!$c) {
        $m = oci_error();
        trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
    }
    
    $s = oci_parse($c, $query);
    if (!$s) {
        $m = oci_error($c);
        trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
    }
    $r = oci_execute($s);
    if (!$r) {
        $m = oci_error($s);
        if ($showerror == TRUE)
        {
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
            echo $query;
        }
    }

    return $s;
}
?>

<div class="header" id="myHeader">
        <span style="font-size:30px;cursor:pointer; float: left; font-family: 'Montserrat'; font-size: 24px;" onclick="openNav('mySidenav')">&#9776; Reports</span>
        <i class="fas fa-sun" style="color: yellow; font-size: 30px;"></i>
        <a href = "home.php">
        <h1 align = "center">Humaarey Bachey</h1>
        </a>
        <span style="font-size:30px;cursor:pointer; float: right; font-family: 'Montserrat'; font-size: 24px;" onclick="openNav('mySidenavR')">Forms &#9776;</span>
    </div>

    <div id="main">
    <div id="mySidenavR" class="sidenav" style = "right: 0;">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav('mySidenavR')">&times;</a>
    <a href="add_student.php" class = "active"><b>Form 1</b><br>Add Student</a>
    <a href="student_accompanying.php"><b>Form 2</b><br>Student Accompanying Form</a>
    <a href="class_assignment.php"><b>Form 3</b><br>Class Assignment Form</a>
    </div>

    <div id="main2">
    <div id="mySidenav" class="sidenav" style = "left: 0;">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav('mySidenav')">&times;</a>
    <a href="home.php" class = "active"><b>Report 1</b><br>Add | Edit | Delete</a>
    <a href="report12.php"><b>Report 2</b><br>Student count per Class</a>
    <a href="report13.php"><b>Report 3</b><br>Dormant Students</a>
    <a href="report14.php"><b>Report 4</b><br>Student - Detailed Information</a>
    <a href="report15.php"><b>Report 5</b><br>Parent - Detailed Information</a>
    </div>

<script>
function openNav(eid) {
  if (eid == "mySidenav") 
  {
    document.getElementById(eid).style.width = "250px";
    document.getElementById("main").style.marginLeft = "250px";
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
  }
  else 
  {
    document.getElementById(eid).style.width = "250px";
    document.getElementById("main2").style.marginRight = "250px";
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
  }
}

function closeNav(eid) {
  document.getElementById(eid).style.width = "0";
  if (eid == "mySidenav") 
  {
    document.getElementById("main").style.marginLeft= "0";
  }
  else 
  {
    document.getElementById("main2").style.marginRight= "0";
  }
  document.body.style.backgroundColor = "white";
}
</script>
    
    <form action="" method="post">
    
    <div class="parent">
        <div class = "formtitle">
            <h2>Dormant Student Information</h2>
        </div>
    
        <div class="left">
          <textarea name = "searchEntry" placeholder="e.g 12" id="styled" rows="1" cols="50"></textarea>

          <select name = "searchBy">
            <option value="Months">Months</option>
            <option value="Years">Years</option>
          </select>
        </div>

        <div class = "submit_part" style="clear:both; display: block; width:fit-content; position: relative; margin: 0 auto; ">
            <input type="submit" name = "submitForSearch" value = "Submit">
        </div>
    </div>
  </form>

  <?php

    if (!empty($_POST["submitForSearch"]))
    {
        $username = "scott";                  // Use your username
        $password = "1234";             // and your password
        $database = "FARJAD";   // and the connect string to connect to your database
        $query_cond = "";

        if ($_POST["searchBy"] == "Months")
        {
            $dec_months = $_POST["searchEntry"] * -1;

            $query_cond = "WHERE ADD_MONTHS(SYSDATE, $dec_months) > ST.LAST_UPDATE_DATE";
        }
        else
        {
            $query_cond = "WHERE ADD_MONTHS(SYSDATE, (-12 * {$_POST['searchEntry']} ) ) > ST.LAST_UPDATE_DATE";
        }

        $query = "SELECT STUDENT_ID, F_NAME||' '||L_NAME AS S_NAME, TO_CHAR(DOB, 'DD/MM/YYYY') AS DOB, CNIC, CLASS_ID, SECTION_ID, LAST_UPDATE_DATE FROM STUDENT ST ".$query_cond;

        echo $query;

        $s = runQuery($username, $password, $database, $query);

        ?>
        
        <div class = "parent">
            <div class = "formtitle">
                <h2>Search results</h2>
            </div>
            
            <?php

                if (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
                {
                    echo "<table id = 'output_table'>\n";
                    $ncols = oci_num_fields($s);
                    echo "<tr>\n";
                    for ($i = 1; $i <= $ncols; ++$i) {
                        $colname = oci_field_name($s, $i);
                        echo "  <th id = 'output_table'>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</th>\n";
                    }
                    echo "</tr>\n";
                    
                    do {
                        echo "<tr id = 'output_table'>\n";
                        foreach ($row as $item) {
                            echo "<td id = 'output_table'>";
                            echo $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"&nbsp;";
                            echo "</td>\n";
                        }

                        echo "</tr>\n";
                    } while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false);

                    echo "</table>\n";
                }
                else
                {
                    ?>
                        <h2 style = "text-align: center;">No results found.</h2>
                    <?php
                }

            ?>
        </div>
    <?php
    }
    ?>

</div>
</div>

    <div class = "footer">
        <h4 style="align-content: center;">Where Kids are #1</h4>
        <div class = "footermessage">
            <h3 style="color: white; font-size: 22px; font-weight: lighter;">Muhammad Farjad Ilyas</h3>
        </div>
    </div>

</body>
</html>