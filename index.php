<html>
<head>
 <style>
 div {
    border: 1px solid #611111; width:400px;
    background: #efffef; padding:10px; border-radius: 15px;
    }
 form {
    border: 1px solid #611111; width: 400px;
    padding: 10px; background: #ccefff;
    border-radius: 15px;
    }
 h1 {font:italic bold 20px/30px Georgia, serif;}
 h2 {font:normal bold 16px/30px Georgia, serif;}
 </style>
</head>
<body>
    <?php
        if ($_GET['message']!="") {
        print "<div>".$_GET['message']."</div>";
        }
    ?>
    <h1>Free Hugs bonus program:</h1><br/>
    <form action="getpass.php" method="post">
        <h2>Bonus game question:</h2>
        In which year was "Free Hugs" incorporated?
        <select size="1" name="year">
        <option>1999</option>
        <option>2003</option>
        <option>2009</option>
        </select><br/>

        <h2>Your data:</h2>
        Your name:
        <input name="name" value=""/><br/>

        Your email address:
        <input name="email" value=""/><br/>

        Your favorite "Free hugs" store:
        <select size="1" name="location">
        <option value="0">City square</option>
        <option value="1">Kiosk at the beach</option>
        <option value="2">Big City Mall floor 1</option>
        <option value="3">Big City Mall floor 4</option>
        </select>
        <br/><br/>
        <input type="submit" value="Participate in the bonus game"/>

    </form>
</body>
</html>