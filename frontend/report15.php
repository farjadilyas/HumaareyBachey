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
            <h2>Detailed Parent Information</h2>
        </div>
    
        <div class="left">
          <textarea name = "searchEntry" type = "text" placeholder="FULL NAME or ID" id="styled" rows="1" cols="50"></textarea>

          <select name = "searchBy">
            <option value="Name">Name</option>
            <option value="SID">Parent ID</option>
          </select>

          <a href = "add_student.php"><button class="add_button" type="button">Add Student</button></a>
        </div>

        <div class = "submit_part" style="clear:both; display: block; width:fit-content; position: relative; margin: 0 auto; ">
            <input type="submit" name = "submitForSearch" value = "Submit">
        </div>
    </div>

  </form>

    <!-- searchEntry: 10043, searchBy: SID, submitForSearch = set-->
    <!--  -->

    <?php

    if (!empty($_POST["submitForSearch"]))
    {
        $username = "scott";                  // Use your username
        $password = "1234";             // and your password
        $database = "FARJAD";   // and the connect string to connect to your database  // and the connect string to connect to your database
        $query_cond = "";

        if ($_POST["searchBy"] == "SID")
        {	
            $query_init = "SELECT P.PARENT_ID, PE.F_NAME||' '||PE.L_NAME AS NAME, PE.CNIC, PE.CONTACT_NO
            FROM PARENT P
            INNER JOIN PERSON PE ON P.CNIC = PE.CNIC
            WHERE P.PARENT_ID = ".$_POST["searchEntry"];
        }
        else
        {
            $query_init = "SELECT P.PARENT_ID, PE.F_NAME||' '||PE.L_NAME AS NAME, PE.CNIC, PE.CONTACT_NO
            FROM PERSON PE
            INNER JOIN PARENT P ON P.CNIC = PE.CNIC
            WHERE PE.F_NAME||' '||PE.L_NAME LIKE '".$_POST["searchEntry"]."%'";
        }

        
		
        $s = runQuery($username, $password, $database, $query_init);

        $result_id=1;
        
        while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
        {
            $cur_pid = $row['PARENT_ID'];

            ?>

            <div class = "parent">

            <div class = "minleft">
                <h2><?php echo "RESULT #".$result_id; ?></h2>
            </div>

            <?php

            $result_id = $result_id + 1;

            echo "<table id = 'output_table'><caption><h2>Parent's Info</h2?</caption>\n";
                $ncols = oci_num_fields($s);
                echo "<tr>\n";
                for ($i = 1; $i <= $ncols; ++$i) {
                    $colname = oci_field_name($s, $i);
                    echo "  <th id = 'output_table'>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</th>\n";
                }
                echo "</tr>\n";

                echo "<tr id = 'output_table'>\n";
                foreach ($row as $item) {
                    echo "<td id = 'output_table'>";
                    echo $item!==null?htmlspecialchars($item, ENT_QUOTES ):"&nbsp;";
                    echo "</td>\n";
                }
                echo "</tr>\n";
            echo "</table>\n";

            $query_cond = " WHERE P.PARENT_ID = ".$cur_pid;
			$query = 
                "SELECT S.STUDENT_ID, S.F_NAME||' '||S.L_NAME AS S_NAME, TO_CHAR(S.DOB, 'DD/MM/YYYY') AS DOB, S.CNIC, S.CLASS_ID, S.SECTION_ID, G.GUARDIAN_ID, G.F_NAME||' '||G.L_NAME AS GUARDIAN_NAME, G.GENDER, S.RELATION AS GUARDIAN_GENDER
                FROM PARENT P
                INNER JOIN FAMILY F ON F.MOTHER_ID = P.PARENT_ID OR F.FATHER_ID = P.PARENT_ID
                INNER JOIN STUDENT S ON S.FAMILY_ID = F.FAMILY_ID
                INNER JOIN GUARDIAN G ON S.GUARDIAN_ID = G.GUARDIAN_ID".$query_cond. 
                "ORDER BY S.STUDENT_ID";
        

            $ps = runQuery($username, $password, $database, $query);

            $sub_res_no = 0;

            if (($prow = oci_fetch_array($ps, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
            {
                echo "<table id = 'output_table'><caption><h2>Children's Info</h2?</caption>\n";
                $ncols = oci_num_fields($ps);
                echo "<tr>\n";
                for ($i = 1; $i <= $ncols; ++$i) {
                    $colname = oci_field_name($ps, $i);
                    echo "  <th id = 'output_table'>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</th>\n";
                }
                echo "</tr>\n";

                do {
                    $sub_res_no = $sub_res_no + 1;
    
                        echo "<tr id = 'output_table'>\n";
                        foreach ($prow as $item) {
                            echo "<td id = 'output_table'>";
                            echo $item!==null?htmlspecialchars($item, ENT_QUOTES ):"&nbsp;";
                            echo "</td>\n";
                        }
                        echo "</tr>\n";
                    
                } while (($prow = oci_fetch_array($ps, OCI_ASSOC+OCI_RETURN_NULLS)) != false);
    
                echo "</table>\n";
            }
            else
            {
                echo "<h2 style = 'text-align: center;'>This parent has no children enrolled.</h2>\n";
            }

            echo "</div>\n";
        }
	}
    ?>

    </div>
    </div>

    <div class = "footer">
        <h4 style="align-content: center;">Where Kids are #1</h4>
        <div class = "footermessage">
            <h3 style="color: white; font-size: 22px; font-weight: lighter;">Saif Ullah Dar</h3>
        </div>
    </div>
      
</body>
</html>