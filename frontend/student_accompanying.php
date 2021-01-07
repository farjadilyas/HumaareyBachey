
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<link href="home.css" type="text/css" rel="stylesheet">
<script src="https://kit.fontawesome.com/af5a6f7cd6.js" crossorigin="anonymous"></script>
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
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


<form action="" method="post">

    <div id="container" style=" width:778px; margin:0 auto;border:2px solid black; background-color: white; border: solid 0px; border-radius: 5px;">
        <p style="text-align:center;font-size:20px;">
            <b>
                HAMAREY BACHCHEY<br />
                STUDENT ACCOMPANYING FORM
            </b>
        </p>
        <p style="font-size:20px;margin-left:10px;">
            <b>
                Student Information:
            </b>
        </p>
        <p style="margin-left:100px;">
            ID 
            <span style="margin-left:60px;">
                :
                <input type="text" name="SID" style=" width:250px;border-style:none;">
            </span>
        </p>
        <p style="margin-left:100px;">
            Name
            <span style="margin-left:38px;">
                :
                <input type="text" name="SNAME" style=" width:250px;border-style:none;">
            </span>
        </p>
        <p style="margin-left:100px;">
            Class
            <span style="margin-left:41px;">
                :
                <input type="text" name="SCLASS" style=" width:250px;border-style:none;">
            </span>
        </p>
        <p style="font-size:20px;margin-left:10px;">
            <b>
                Accompanying Guardian Information:
            </b>
        </p>
        <p style="margin-left:40px;">
            ID
            <span style="margin-left:60px;">
                :
                <input type="text" name="GID" style=" width:250px;border-style:none;">
            </span>
        </p>
        <p style="margin-left:40px;">
            Name
            <span style="margin-left:38px;">
                :
                <input type="text" name="GNAME" style=" width:250px;border-style:none;">
            </span>
        </p>
        <p style="margin-left:40px;">
            Pregnant  
            <span style="margin-left:20px;">
                :
                <input type="radio" id="GPYES" name="GPREG" value="YES">
                <label for="GPYES">Yes</label>
            
                <input type="radio" id="GPNO" name="GPREG" value="NO" checked>
                <label for="GPNO">No</label>
                </label>
            </span>
        </p>
        <p style="margin-left:40px;">
            Reason for
            Parents Absence :
            <input type="text" name="REASON" style=" width:300px; border:1px solid #000;height:80px;">
        </p>

        <div style="clear:both; display: block; width:fit-content; position: relative; margin: 0 auto 10px; ">
            <input name = "submitForInsert"type="submit" value = "Submit">
        </div>
    </form>

    </div>

<?php

    if (!empty($_POST["submitForInsert"]))
    {

        $username = "scott";                  // Use your username
        $password = "1234";             // and your password
        $database = "FARJAD";
        $gpreg = 0; 

        if ($_POST["GPREG"] == "YES")
        {
            $gpreg = 1;
        }
    
        $query = "INSERT INTO ACCOMPANYING
        VALUES({$_POST['SID']}, 0, {$_POST['GID']}, $gpreg, '{$_POST['REASON']}')";

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
            ?>

            <div class = "bubblemem" style = "background-color: red;">
            <i class="fas fa-exclamation-circle" style="color: white; font-size: 24px; white-space: nowrap; margin-right: 20px"></i>    
            <h2 style="color: white; display: inline-block;">
                <?php
                if ($m['code'] == 20211)
                {
                    echo "The guardian specified isn't assigned to this student";
                }
                else
                {
                    echo "Form submission failed";
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
                    <h2 style="color: white; display: inline-block;">Form submission successful</h2>   
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
            <h3 style="color: white; font-size: 22px; font-weight: lighter;">Omar Hayat</h3>
        </div>
    </div>
</body>
</html>