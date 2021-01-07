<html>

<link href="home.css" type="text/css" rel="stylesheet">

<header>
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
        $r = @oci_execute($s);
        if (!$r) {
            $m = oci_error($s);
            if ($showerror == TRUE)
            {
                trigger_error('Could not execute statement: '. $m['message'], E_USER_ERROR);
            }
        }

        return $s;
    }

    function addParent($username, $password, $database, $p_gender, $pCNIC)
    {
        
        $query = "SELECT PERSON.P_TYPE P_TYPE, NVL(PARENT.PARENT_ID,-1) PARENT_ID
        FROM PERSON
        FULL JOIN PARENT
        ON PERSON.CNIC = PARENT.CNIC
        WHERE PERSON.CNIC = '$pCNIC'";
    
        $s = runQuery($username, $password, $database, $query);

        if (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
        {
            if ($row['PARENT_ID'] == -1)
            {
                $query = "INSERT INTO PARENT(CNIC, PARENT_ID) VALUES('".$pCNIC."', 0)";

                $sub_s = runQuery($username, $password, $database, $query);

                $query = "UPDATE PERSON SET P_TYPE = 2 WHERE CNIC = '".$pCNIC."'";

                $query = "SELECT PARENT_ID FROM PARENT WHERE CNIC = '".$pCNIC."'";

                $sub_s = runQuery($username, $password, $database, $query);

                $PARENT_ID = $sub_s['PARENT_ID'];
            }
            else
            {
                $PARENT_ID = $row['PARENT_ID'];
            }
        }
        else
        {
            if ($p_gender == 'M')
            {
                $NAME = $_POST['fNAME'];
                $CONTACT = $_POST['fCONTACT'];
                $EMAIL = $_POST['fEMAIL'];
            }
            else
            {
                $NAME = $_POST['mNAME'];
                $CONTACT = $_POST['mCONTACT'];
                $EMAIL = $_POST['mEMAIL'];
            }
            $query = "INSERT INTO PERSON VALUES('".$pCNIC."', '".$NAME."', NULL, '".$CONTACT."', '".$p_gender."', '$EMAIL', 1)";

            $sub_s = runQuery($username, $password, $database, $query);

            $query = "SELECT PARENT_ID FROM PARENT WHERE CNIC = '".$pCNIC."'";

            $sub_s = runQuery($username, $password, $database, $query);

            $s_row = oci_fetch_array($sub_s, OCI_ASSOC+OCI_RETURN_NULLS);

            $PARENT_ID = $s_row['PARENT_ID'];
        }

        return $PARENT_ID;
    }

    function addFamily($username, $password, $database, $FATHER_ID, $MOTHER_ID)
    {
        $query = "INSERT INTO FAMILY VALUES(0, $FATHER_ID, $MOTHER_ID, 0, 0, SYSDATE, 0)";

        $s = runQuery($username, $password, $database, $query, FALSE);

        $query = "SELECT FAMILY_ID FROM FAMILY WHERE FATHER_ID = $FATHER_ID AND MOTHER_ID = $MOTHER_ID";

        $s = runQuery($username, $password, $database, $query, FALSE);

        if (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
        {
            return $row['FAMILY_ID'];
        }
        else
        {
            echo "Family insertion failed!";
            return -1;
        }
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
    
<form action="add_student.php#searchMessage" method="post">
    
    <div class="parent">
        <div class = "formtitle">
            <h2>Student Admission Form</h2>
        </div>

        <div class = "left">
            <h2>Student Information</h2>
        </div>

        <table id = "input_table">
            <tr id = "input_table">
                <td id = "first_col">Name</td>
                <td id = "input_table"><textarea name = "sFNAME" placeholder="First Name" id="styled" rows="1" cols="30"></textarea>
                    <textarea name = "sLNAME" placeholder="Last Name" id="styled" rows="1" cols="30"></textarea></td>
            </tr>

            <tr id = "input_table">
                <td id = "first_col">CNIC</td>
                <td id = "input_table"><textarea name = "sCNIC" placeholder="i.e. 4210129538311" id="styled" rows="1" cols="30"></textarea></td>
            </tr>

            <tr id = "input_table">
                <td id = "first_col">Date of Birth</td>
                <td id = "input_table"><input name = "sDOB"type="date" id="calendar" name="trip-start"
                    value="2018-07-22"
                    min="1999-01-01" max="2020-12-31"></td>
            </tr>

            <tr id = "input_table">
                <td id = "first_col">Gender</td>
                <td id = "input_table">
                    <div class="radio-toolbar" name="sGENDER">
                        <input type="radio" id="SMALE" name="sGENDER" value="M">
                        <label for="SMALE">Male</label>
                    
                        <input type="radio" id="SFEMALE" name="sGENDER" value="F">
                        <label for="SFEMALE">Female</label>
                    </div>
                </td>
            </tr>
        </table>

        <div class = "left">
            <h2>Parents Information</h2>
        </div>

        <table id = "input_table">
            <tr>
                <td></td>
                <th id = "input_table"><h3 align = "center">Mother</h3></td>
                <th id = "input_table"><h3 align = "center">Father</h3></td>
            </tr>

            <tr>
                <td id = "first_col">Name</td>
                <td id = "first_col"><textarea name = "mNAME" placeholder="i.e. John Smith" id="styled" rows="1" cols="50"></textarea></td>
                <td id = "input_table"><textarea name = "fNAME" placeholder="i.e. John Smith" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">Contact</td>
                <td id = "first_col"><textarea name = "mCONTACT" placeholder="i.e. 0315-4457090" id="styled" rows="1" cols="50"></textarea></td>
                <td id = "input_table"><textarea name = "fCONTACT" placeholder="i.e. 0315-4457090" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">CNIC</td>
                <td id = "first_col"><textarea name = "mCNIC" placeholder="i.e. 1-42101-1111111-1" id="styled" rows="1" cols="50"></textarea></td>
                <td id = "input_table"><textarea name = "fCNIC" placeholder="i.e. 1-42101-1111111-1" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">Email</td>
                <td id = "first_col"><textarea name = "mEMAIL" placeholder="i.e. johnsmith@somemail.com" id="styled" rows="1" cols="50"></textarea></td>
                <td id = "input_table"><textarea name = "fEMAIL" placeholder="i.e. johnsmith@somemail.com" id="styled" rows="1" cols="50"></textarea></td>
            </tr>
        </table>

        <div class = "left">
            <h2>Guardian Information</h2>
        </div>


        
        <table id = "input_table">

            <tr>
                <td id = "first_col">Name</td>
                <td id = "input_table"><textarea name = "gFNAME" placeholder="First Name" id="styled" rows="1" cols="30"></textarea>
                <textarea name = "gLNAME" placeholder="Last Name" id="styled" rows="1" cols="30"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">Contact</td>
                <td id = "input_table"><textarea name = "gCONTACT" placeholder="i.e. 0315-4457090" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">CNIC</td>
                <td id = "input_table"><textarea name = "gCNIC" placeholder="i.e. 1-42101-1111111-1" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr id = "input_table">
                <td id = "first_col">Gender</td>
                <td id = "input_table">
                    <div class="radio-toolbar" name="gGENDER">
                        <input type="radio" id="GMALE" name="gGENDER" value="M">
                        <label for="GMALE">Male</label>
                    
                        <input type="radio" id="GFEMALE" name="gGENDER" value="F">
                        <label for="GFEMALE">Female</label>
                    </div>
                </td>
            </tr>

            <tr>
                <td id = "first_col">Relation</td>
                <td id = "input_table"><textarea name = "gRELATION" placeholder="i.e. Aunt" id="styled" rows="1" cols="50"></textarea></td>
            </tr>
        </table>


        <div class = "left" style = "background-color: #ffcdd2; border-color: red;">
            <h2 style="color: #b7171c;">For staff only</h2>
        </div>

        <table id = "input_table">

            <tr>
                <td id = "first_col">Challan No.</td>
                <td id = "input_table"><textarea name = "sCHALLAN" placeholder="42856782" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">Class</td>
                <td id = "input_table"><textarea name = "sCLASS" placeholder="i.e 3" id="styled" rows="1" cols="50"></textarea></td>
            </tr>

            <tr>
                <td id = "first_col">Section</td>
                <td id = "input_table"><textarea name = "sSECTION" placeholder="i.e B" id="styled" rows="1" cols="50"></textarea></td>
            </tr>
        </table>

        <div class = "submit_part" style="clear:both; display: block; width:fit-content; position: relative; margin: 0 auto; ">
            <input name = "submitForInsert"type="submit" value = "Submit">
        </div>
    </form>
    </div>
        <?php

        if (!empty($_POST["submitForInsert"]))
        {
            $MOTHER_ID = 0;
            $FATHER_ID = 0;
            $FAMILY_ID = 0;
            $GUARDIAN_ID = 0;

            $username = "scott";                  // Use your username
            $password = "1234";             // and your password
            $database = "FARJAD";   // and the connect string to connect to your database

            $MOTHER_ID = addParent($username, $password, $database, 'F', $_POST['mCNIC']);

            $FATHER_ID = addParent($username, $password, $database, 'M', $_POST['fCNIC']);

            $FAMILY_ID = addFamily($username, $password, $database, $FATHER_ID, $MOTHER_ID);

            $query = "SELECT GUARDIAN_ID FROM GUARDIAN WHERE CNIC = '".$_POST['gCNIC']."'";

            $s = runQuery($username, $password, $database, $query);

            if (($row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS)) != false)
            {
                $GUARDIAN_ID = $row['GUARDIAN_ID'];
            }
            else
            {
                $query = "INSERT INTO GUARDIAN VALUES(0, '".$_POST['gCNIC']."', '".$_POST['gFNAME']."', '{$_POST['gLNAME']}', '".$_POST['gCONTACT']."', '".$_POST['gGENDER']."')";

                $s = runQuery($username, $password, $database, $query);

                $query = "SELECT GUARDIAN_ID FROM GUARDIAN WHERE CNIC = '".$_POST['gCNIC']."'";

                $s = runQuery($username, $password, $database, $query);

                $row = oci_fetch_array($s, OCI_ASSOC+OCI_RETURN_NULLS);

                $GUARDIAN_ID = $row['GUARDIAN_ID'];
            }

            //$query = "INSERT INTO STUDENT VALUES(0, '".$_POST['sCNIC']."', '".$_POST['sNAME']."', NULL, TO_DATE('"$_POST['sDOB']."', YYYY-MM-DD), '".$_POST['sGENDER']."', NULL, ".$_POST['sCLASS'].", '".$_POST['sSECTION']."', ".$FAMILY_ID.", ".$GUARDIAN_ID.", '".$_POST['sCHALLAN']."', SYSDATE, SYSDATE)";

            $query = "INSERT INTO STUDENT
            VALUES(0, '{$_POST['sCNIC']}', '{$_POST['sFNAME']}', '{$_POST['sLNAME']}', TO_DATE('{$_POST['sDOB']}','YYYY-MM-DD'), '{$_POST['sGENDER']}', NULL, {$_POST['sCLASS']}, '{$_POST['sSECTION']}', $FAMILY_ID, $GUARDIAN_ID, '{$_POST['gRELATION']}', '{$_POST['sCHALLAN']}', SYSDATE, SYSDATE)";
    
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
            $r = @oci_execute($s);
            echo "<a name = 'searchMessage'></a>";
            if (!$r) {
                $m = oci_error($s);

                ?>

            <div class = "bubblemem" style = "background-color: red;">
            <i class="fas fa-exclamation-circle" style="color: white; font-size: 24px; white-space: nowrap; margin-right: 20px"></i>    
            <h2 style="color: white; display: inline-block;">
                <?php
                switch ($m['code'])
                {
                    case 20111:
                        echo "Student is too young for this class";
                    break;
                    case 20112:
                        echo "Student is too old for this class";
                    break;
                    case 20113:
                        echo "This isn't a CO-EDUCATION section";
                    break;
                    case 20114:
                        echo "Guardian can't be Male";
                    break;
                }
                ?>
                    </h2>   
                </div>

                <?php
            }
            else
            {
                ?>
                
                <div class = "bubblemem">
                    <i class="fa fa-check" style="color: white; font-size: 24px; white-space: nowrap; margin-right: 20px"></i>    
                    <h2 style="color: white; display: inline-block;">Student insertion successful</h2>   
                </div>

                <?php
            }

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