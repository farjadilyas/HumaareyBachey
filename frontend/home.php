<html>

<header>
<link href="home.css" type="text/css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/af5a6f7cd6.js" crossorigin="anonymous"></script>
</header>

<body>

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




<div class = "headingout">
		<div class = "heading">
			<p>LEARNERS TODAY</p>
		</div>
		<div class = "heading">
			<p>LEADERS TOMORROW</p>
		</div>
    </div>
    
    <div class = "centerbox">
        <a href = "#searchThis"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></a>
    </div>
    
    <form action="home.php#searchResults" method="post">
    
    <a name = "searchThis"></a>

    <div class="parent">
        <div class = "formtitle">
            <a href = "home.php"><h2>Students per class form</h2></a>
        </div>
    
        <div class="left">
          <label for="styled">Search by   :</label>
          <textarea name = "searchEntry" placeholder="John Smith" id="styled" rows="1" cols="50"></textarea>

          <select name = "searchBy">
            <option value="Name">Name</option>
            <option value="SID">Student ID</option>
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
        $database = "FARJAD";   // and the connect string to connect to your database
        $query_cond = "";

        if ($_POST["searchBy"] == "SID")
        {
            $query_cond = " WHERE STUDENT_ID = ".$_POST["searchEntry"];
        }
        else
        {
            $query_cond = " WHERE F_NAME||' '||L_NAME LIKE '".$_POST["searchEntry"]."%'";
        }
        
        $query = "SELECT STUDENT_ID, F_NAME||' '||L_NAME AS NAME, ROUND(MONTHS_BETWEEN(SYSDATE, DOB)/12,1) AS AGE, GENDER, CLASS_ID, SECTION_ID 
        FROM STUDENT".$query_cond. 
        "ORDER BY CLASS_ID, SECTION_ID";

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
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
        }
        ?>

        <div class = "parent">
            <div class = "formtitle">
            <a name = "searchResults"><h2>Search results</h2></a>
            </div>
            
            <?php

                echo "<table id = 'output_table'>\n";
                $ncols = oci_num_fields($s);
                echo "<tr>\n";
                for ($i = 1; $i <= $ncols; ++$i) {
                    $colname = oci_field_name($s, $i);
                    echo "  <th id = 'output_table'>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</th>\n";
                }
                echo "<th id = 'output_table'>Edit</th>";
                echo "<th id = 'output_table'>Delete</th>";
                echo "</tr>\n";
                
                while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
                    echo "<tr id = 'output_table'>\n";
                    foreach ($row as $item) {
                        echo "<td id = 'output_table'>";
                        echo $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"&nbsp;";
                        echo "</td>\n";
                    }
                    ?>
                    <td id = 'output_table'><a href="update_student.php?id=<?=$row['STUDENT_ID'];?>"><i class='far fa-edit' id = 'tcell'></i></a></td>
                    <td id = 'output_table'><a href="delete_student.php?id=<?=$row['STUDENT_ID'];?>"><i class='fa fa-trash' aria-hidden='true' id = 'tcell'></i></a></td>
                    <?php
                    echo "</tr>\n";
                }
                echo "</table>\n";

            ?>
        </div>

        <?php
    }

    ?>

    <div class = "parent" style = "margin-bottom: 5%">
        <div class = "formtitle">
            <h2>Students Roster</h2>
        </div>

        <?php

        $username = "scott";                  // Use your username
        $password = "1234";             // and your password
        $database = "FARJAD";   // and the connect string to connect to your database

        $query = "SELECT S.CLASS_ID, S.TITLE, S.GENDER_RESTRAINT, S.SECTION_ID FROM SECTION S INNER JOIN CLASS C ON C.CLASS_ID = S.CLASS_ID ORDER BY S.CLASS_ID, S.SECTION_ID";
        
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
            trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
        }

        while (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) 
        {
                
            $sub_query = "SELECT STUDENT_ID, F_NAME||' '||L_NAME AS NAME, ROUND(MONTHS_BETWEEN(SYSDATE, DOB)/12,1) AS AGE, GENDER 
            FROM STUDENT WHERE CLASS_ID = ".$row['CLASS_ID']." AND SECTION_ID = '".$row['SECTION_ID']."'";

            $sub_c = oci_connect($username, $password, $database);
            if (!$c) {
                $m = oci_error();
                trigger_error('Could not connect to database: '. $m['message'], E_USER_ERROR);
            }

            $sub_s = oci_parse($sub_c, $sub_query);
            if (!$sub_s) {
                $m = oci_error($sub_c);
                trigger_error('Could not parse statement: '. $m['message'], E_USER_ERROR);
            }
            $sub_r = oci_execute($sub_s);
            if (!$sub_r) {
                $m = oci_error($sub_s);
                trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
            }

            if (($sub_row = oci_fetch_array($sub_s, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
            {
                if ($row['GENDER_RESTRAINT'] == 'M')
                {
                    $g_type = "ALL MALE";
                }
                else if ($row['GENDER_RESTRAINT'] == 'F')
                {
                    $g_type = "ALL FEMALE";
                }
                else
                {
                    $g_type = "CO-ED";
                }
                echo "<div class = 'left'>
                <h2>Class ".$row['CLASS_ID'].$row['SECTION_ID']." [".$row['TITLE']."] ($g_type):"." </h2>
                </div>";

                echo "<table id = 'output_table'>\n";
                $ncols = oci_num_fields($sub_s);
                echo "<tr>\n";
                for ($i = 1; $i <= $ncols; ++$i) {
                    $colname = oci_field_name($sub_s, $i);
                    echo "  <th id = 'output_table'>".htmlspecialchars($colname,ENT_QUOTES|ENT_SUBSTITUTE)."</th>\n";
                }
                echo "<th id = 'output_table'>Edit</th>";
                echo "<th id = 'output_table'>Delete</th>";
                echo "</tr>\n";

                do
                {
                    echo "<tr id = 'output_table'>\n";
                    foreach ($sub_row as $item) {
                        echo "<td id = 'output_table'>";
                        echo $item!==null?htmlspecialchars($item, ENT_QUOTES|ENT_SUBSTITUTE):"&nbsp;";
                        echo "</td>\n";
                    }
                    ?>
                    <td id = 'output_table'><a href="update_student.php?id=<?=$sub_row['STUDENT_ID'];?>"><i class='far fa-edit' id = 'tcell'></i></a></td>
                    <td id = 'output_table'><a href="delete_student.php?id=<?=$sub_row['STUDENT_ID'];?>"><i class='fa fa-trash' aria-hidden='true' id = 'tcell'></i></a></td>
                    </tr>
                    <?php
                } while (($sub_row = oci_fetch_array($sub_s, OCI_ASSOC+OCI_RETURN_NULLS)) != false) ;
                echo "</table>\n";
            } 
        }
        ?>
    </div>

    </div>
    </div>

    <div class = "footer">
        <h4 style="align-content: center;">Where Kids are #1</h4>
        <div class = "footermessage">
            <h3 style="color: white; font-size: 22px; font-weight: lighter;">Muhammad Farjad Ilyas</h3>
    </div>

    
</body>
</html>