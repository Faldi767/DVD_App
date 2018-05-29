<?php
    class database {
        private $servername = "localhost";
        private $username = "root";
        private $password = "";
        private $dbname = "dvd_store";
        private $conn;
        function __construct() {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        }
        function __destruct() {
            $this->conn->close();
        }
        function register($nama, $email, $password, $phone, $address) {
            if ($this->conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $sql = "INSERT INTO tbl_user (nama, alamat, no_telp, email, password, id_role) VALUES ('$nama', '$address', '$phone', '$email', '$password', '5')";

            if ($this->conn->query($sql) === TRUE) {
                header("location:index");
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        function login($email, $password) {
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            } 
            
            $sql = "SELECT * FROM tbl_user,tbl_role WHERE tbl_user.id_role=tbl_role.id_role AND email='$email' AND password='$password'";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
                session_start();
                while($row = $result->fetch_assoc()) {
                    $_SESSION['login'] = true;
                    $_SESSION["id"] = $row["id_user"];
                    $_SESSION["nama"] = $row["nama"];
                    $_SESSION["alamat"] = $row["alamat"];
                    $_SESSION["telp"] = $row["no_telp"];
                    if($row["nama_role"] == "ADMIN") {
                        $_SESSION['admin'] = true;
                    } else {
                        $_SESSION["admin"] = false;
                    }
                }
                header("location:index");
            } else {
                echo "0 results";
            }
        }
        function logout() {
            session_destroy();
            header("location:index");
        }
        function loadslider() {
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            } 
            
            $sql = "SELECT * FROM tbl_slider";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo '<div class="container" id="slider">';
                echo '<div class="col-md-12">';
                echo '<div id="myCarousel" class="carousel slide" data-ride="carousel">';
                echo '<ol class="carousel-indicators">';
                for($i=0;$i < $result->num_rows;$i++) {
                    if($i == 0) {
                        echo '<li data-target="#myCarousel" data-slide-to="'.$i.'" class="active"></li>';
                    } else {
                        echo '<li data-target="#myCarousel" data-slide-to="'.$i.'"></li>';
                    }
                }
                echo '</ol><div class="carousel-inner">';
                while($row = $result->fetch_assoc()) {
                        if($row["id_slider"] == 1) {
                            echo '<div class="item active">';
                        } else {
                            echo '<div class="item">';
                        } 
                        echo '<img src="admin/slide_images/'.$row["slide_images"].'" alt="">';
                        echo '</div>';
                }
                echo '</div></div></div></div>';
            } else {
                echo "0 results";
            }
        }
        function loadproducthome() {
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            } 
            
            $sql = "SELECT * FROM tbl_film";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
                // output data of each row
                echo '<div id="content" class="container"><div class="row">';
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-sm-4 col-sm-6 single"><div class="product"><center><a href="">';
                    echo '<img src="admin/product_images/'.$row["img_film"].'" alt="" class="img-responsive">';
                    echo '</a></center><div class="text">';
                    echo '<h3><a href="">'.$row["nama"].'</a></h3>';
                    echo '<p class="price">Rp.'.$row["harga"].'</p>';
                    echo '<p class="buttons"><a href="" class="btn btn-default">View Details</a> ';
                    echo '<a href="" class="btn btn-primary"><i class="fa fa-shopping-cart"></i>Add to Cart</a>';
                    echo '</p></div></div></div>';
                }
                echo '</div></div>';
            } else {
                echo "0 results";
            }
        }
        function loadcategory($title = "") {
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            } 
            
            $sql = "SELECT * FROM tbl_detailfilm";
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo '<div class="col-md-3"><div class="panel panel-default sidebar-menu">';
                echo '<div class="panel-heading"><h3 class="panel-title">Categories</h3></div>';
                echo '<div class="panel-body">';
                echo '<ul class="nav nav-pills nav-stacked category-menu">';
                while($row = $result->fetch_assoc()) {
                    if(isset($_GET["id_genre"])) {
                        echo '<li class="'.($row["id_genre"] == $_GET["id_genre"] ? "active" : "").'"><a href="index?page='.$title.'&id_genre='.$row["id_genre"].'">'.$row["nama_genre"].'</a></li>';
                    } else {
                        echo '<li><a href="index?page='.$title.'&id_genre='.$row["id_genre"].'">'.$row["nama_genre"].'</a></li>';
                    }
                }
                echo '</ul></div></div></div>';
            } else {
                echo '0 results';
            }   
        }
        function loadshop($id_genre = 0 ,$shoppage = 1) {
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            } 
            
            $no_of_records_per_page = 3;
            $offset = ($shoppage-1) * $no_of_records_per_page;
            if($id_genre != 0) {
                $total_pages_sql = "SELECT COUNT(*) FROM tbl_film WHERE id_genre='$id_genre'";
            } else {
                $total_pages_sql = "SELECT COUNT(*) FROM tbl_film";
            }
            $result = $this->conn->query($total_pages_sql);
            $total_rows = mysqli_fetch_array($result)[0];
            $total_pages = ceil($total_rows / $no_of_records_per_page);
            if($id_genre != 0) {
                $sql = "SELECT * FROM tbl_film WHERE id_genre='$id_genre' LIMIT $offset, $no_of_records_per_page";
            } else {
                $sql = "SELECT * FROM tbl_film LIMIT $offset, $no_of_records_per_page";
            }
            $result = $this->conn->query($sql);
            
            if ($result->num_rows > 0) { ?>
                <div class="col-md-9">
                <div class="box">
                    <h1>Shop</h1>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora ab commodi at ea voluptatum odio aliquid, ex dolores ipsa accusantium vitae qui sint doloribus fugiat harum sunt atque itaque! Reiciendis!</p>    
                </div>
                <div class="row">
                <?php while($row = $result->fetch_assoc()) { ?>
                    <div class="col-md-4 col-sm-6 center-responsive">
                        <div class="product">
                            <center>
                            <a href="">
                                <img src="admin/product_images/<?php echo $row["img_film"]; ?>" class="img-responsive">
                            </a>
                            </center>
                            <div class="text">
                                <h3>
                                    <a href=""><?php echo $row["nama"]; ?></a>
                                </h3>
                                <p class="price">
                                    Rp.<?php echo $row["harga"]; ?>
                                </p>
                                <p class="buttons">
                                    <a href="details.php" class="btn btn-default">View Details</a>
                                    <a href="details.php" class="btn btn-primary"><i class="fa fa-shopping-cart"></i>Add to Cart</a>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <center>
                <ul class="pagination">
                    <li><a href="">First Page</a></li>
                    <?php 
                        $i = 1;
                        while($i <= $total_pages) { 
                            if($id_genre == 0) { ?>
                            
                            <li><a href="index?page=shop&shoppage=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php } else { ?>
                                <li><a href="index?page=shop&id_genre=<?php echo $id_genre; ?>&shoppage=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php }
                                $i++;
                        } ?>
                    <li><a href="">Last Page</a></li>
                </ul>
            </center>
        </div>
    </div>
</div>
            <?php } else { ?>
                <div class="col-md-9">
                <div class="box">
                    <h1>Shop</h1>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempora ab commodi at ea voluptatum odio aliquid, ex dolores ipsa accusantium vitae qui sint doloribus fugiat harum sunt atque itaque! Reiciendis!</p>    
                </div>
                <div class="row">
                </div>
    </div>
</div>
            <?php }   
        }
    }
    class main extends database {
        function __construct() {
            session_start();
            include("top.php"); ?>
            <div id="top">
                <div class="container">
                    <div class="col-md-6 offer">
                        <a href="" class="btn btn-success btn-sm">Welcome <?php echo (isset($_SESSION['login']) ? $_SESSION['nama'] : "Guest") ?></a>
                        <a href="">Shopping cart total Rp.100.000, Total item 2</a>
                    </div>
                    <div class="col-md-6">
                        <ul class="menu">
                            <?php if(isset($_SESSION['login'])) { ?>
                            <li>
                                <a href="">My Account</a>
                            </li>
                            <li>
                                <a href="">Go To Cart</a>
                            </li>
                            <li>
                                <a href="index?page=logout">Logout</a>
                            </li>
                            <?php } else { ?>
                            <li>
                                <a href="index?page=register">Register</a>
                            </li>
                            <li>
                                <a href="index?page=login">Login</a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php }
        function __destruct() {
            include("bottom.php");
        }
        function navbar($title = "") {
            echo '<div class="navbar navbar-default" id="navbar">';
            echo '<div class="container"><div class="navbar-header">'; 
            echo '<a class="navbar-brand home" href="">Video Store</a>';
            echo '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menunavigation">';
            echo '<span class="sr-only sr-only-focusable">Toggle Navigation</span><i class="fa fa-align-justify"></i>';
            echo '</button>';
            echo '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#search">';
            echo '<span class="sr-only">Toggle Search</span>';
            echo '<i class="fa fa-search"></i></button></div>';
            echo '<div class="navbar-collapse collapse" id="menunavigation"><div class="padding-nav"><ul class="nav navbar-nav navbar-left">';
            echo '<li class="'.($title == "Home" ? "active" : "nav-item").'"><a href="'.($title == "Home" ? "#" : "index").'">Home</a></li>';
            echo '<li class="'.($title == "Shop" ? "active" : "nav-item").'"><a href="'.($title == "Shop" ? "#" : "index?page=shop").'">Shop</a></li>';          
            echo (isset($_SESSION['login']) ? '<li class="'.($title == "My Account" ? "active" : "nav-item").'"><a href="'.($title == "My Account" ? "#" : "index?page=account").'">My Account</a></li>' : "");   
            echo (isset($_SESSION['login']) ? '<li class="'.($title == "Cart" ? "active" : "nav-item").'"><a href="'.($title == "Cart" ? "#" : "index?page=cart").'">Shopping Cart</a></li>' : ""); 
            echo '<li class="'.($title == "Contact Us" ? "active" : "nav-item").'"><a href="'.($title == "Contact Us" ? "#" : "index?page=contact").'">Contact Us</a></li>';
            echo '</ul></div>';                        
            echo '<a class="btn btn-primary navbar-btn right" href=""><i class="fa fa-shopping-cart"></i> <span>4 Items in cart</span></a>';
            echo '<div class="navbar-collapse collapse right"><button class="btn navbar-btn btn-primary" type="button" data-toggle="collapse" data-target="#search"><span class="sr-only">Toggle Search</span><i class="fa fa-search"></i></button></div>';
            echo '<div class="collapse clearfix" id="search"><form class="navbar-form" method="get" action=""><div class="input-group"><input type="text" class="form-control" placeholder="Search" name="user_search" required><span class="input-group-btn"><button type="submit" value="search" name="search" class="btn btn-primary"><i class="fa fa-search"></i></button></span></div></form></div>';   
            echo '</div></div></div>';
        }
        function breadcrumb($title) {
            $this->navbar($title);
            echo '<div id="content"><div class="container">';
            echo '<div class="col-md-12"><ul class="breadcrumb"><li>';
            echo '<a href="index.php">Home</a></li> <li>'.$title.'</li></ul></div>';
        }
        function home() {
            parent::__construct();
            $this->navbar("Home");
            parent::loadslider();
            include("slider.php");
            parent::loadproducthome();
        }
        function shop() {
            parent::__construct();
            $this->breadcrumb("Shop");
            parent::loadcategory("shop");
            if(isset($_GET["shoppage"]) && isset($_GET["id_genre"])) {
                $this->loadshop($_GET["id_genre"], $_GET["shoppage"]);
            } else if(isset($_GET["shoppage"]) && !isset($_GET["id_genre"])) {
                $this->loadshop(0, $_GET["shoppage"]);
            } else if(isset($_GET["id_genre"]) && !isset($_GET["shoppage"])) {
                $this->loadshop($_GET["id_genre"], 1);
            } else {
                $this->loadshop();
            }
        }
        function account() {
            $this->breadcrumb("My Account");
            include("accountpage.php");
        }
        function cart() {
            $this->breadcrumb("Cart");
            include("cartpage.php");
        }
        function contact() {
            parent::__construct();
            $this->breadcrumb("Contact Us");
            parent::loadcategory("contact");
            include("contactpage.php");
        }
        function registerpage() {
            parent::__construct();
            $this->breadcrumb("Register");
            parent::loadcategory(); ?>
            <div class="col-md-9">
            <div class="box">
                <div class="box-header">
                    <center>
                        <h2>Register a New Account</h2>
                    </center>                    
                </div>
                <form action="index?page=processregister" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" class="form-control" name="c_name" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Email</label>
                        <input type="text" class="form-control" name="c_email" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Password</label>
                        <input type="password" class="form-control" name="c_pass" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="c_phone" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" name="c_address" required>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" class="form-control" name="c_image">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="register">
                            <i class="fa fa-user-md"></i> Register           
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
        <?php }
        function loginpage() {
            parent::__construct();
            $this->breadcrumb("Login");
            parent::loadcategory(); ?>
            <div class="col-md-9">
            <div class="box">
                <div class="box-header">
                    <center>
                        <h2>Login</h2>
                    </center>                    
                </div>
                <form action="index?page=processlogin" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Customer Email</label>
                        <input type="text" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Customer Password</label>
                        <input type="password" class="form-control" name="pass" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="login">
                            <i class="fa fa-user-md"></i> Login           
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <?php    }
    }
?>