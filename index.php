<html>
    <head>
        <title> my class website </title>
        <meta>it202 class</meta>
        <script>
            console.log("sample javascript")
            var a = 1+2;
        </script>
        <link rel="stylesheet" href="demo.css">
    </head>
    <body>
        <p> test code </p>
        <form method = "post">
            Search: <input type="text" name="name">
            <input type="text" name="second">
            <input type="submit">

        </form>            
                                                                                                                     
        <?php 
            if (isset($_POST["name"])){
                echo $_POST["name"];
            }
         ?>

        <?php 
           $_POST["name"]
         ?>

        <button onclick = "alert('Alert Box')">Alert Popup</button>

        <h1> first website with edits </h1>
        <p> This <strong>is a </strong> paragraph</p>
        <hr>
        <h3> Second heading</h3>
        <br>
        <button>
        <a href="HW2/problem1.php">Apple website</a></button>
        <button> test </button>

        <img src="https://www.google.com/imgres?imgurl=https%3A%2F%2Fmedia.cnn.com%2Fapi%2Fv1%2Fimages%2Fstellar%2Fprod%2Fiphone-15-pro-hands-on-lead-cnnu.jpg%3Fc%3D16x9%26q%3Dh_720%2Cw_1280%2Cc_fill&tbnid=yU8ixF-WqIgdlM&vet=12ahUKEwit9bu-68uBAxXiUTUKHf6mDjcQMygSegUIARCZAg..i&imgrefurl=https%3A%2F%2Fwww.cnn.com%2Fcnn-underscored%2Felectronics%2Fiphone-15-pro-hands-on&docid=1tDf41JkP_plrM&w=1280&h=720&q=iphone%2015&ved=2ahUKEwit9bu-68uBAxXiUTUKHf6mDjcQMygSegUIARCZAg" />
        <?php
        echo "testphp\n";
        $test2 = "testphpvariable\n";
        $math = 5+3;
        echo $test2;
        echo $math;
        if ($math == 8) 
            echo " value is 8";
        ?>
test
    </body>
</html>