<style>
    :root {
        --dark:rgb(33, 84, 129);
        --dark2: #537D5D;
        --middle:rgba(87, 140, 186, 0.74);
        --light2: #9EBC8A;
        --light: #D2D0A0;
    }
        
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    aside {
        z-index: 1000;
        position: fixed;
        top: 0px;
        left: 0;
        width: 110px;
        height: 100vh;
        padding: 20px;
        flex: 0 0 20%;
    }

    .side-nav {
        width: 110px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        padding: 30px 15px;
        background-color:rgba(60, 184, 89, 0.3);
        backdrop-filter: blur(5px);
        display: flex;
        /* justify-content: space-between; */
        flex-direction: column;
        transition: ease-in-out;
        transition-duration: 0.5s;
        overflow-y: hidden;
        overflow-x: hidden;
    }

    .side-nav:hover {
        width: 250px;
    }

    .user {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        width: 60px;
        font-size: 13px;
        padding: 10px;
        border-radius: 8px;
        margin-left: auto;
        margin-right: auto;
        transition: ease-in-out;
        transition-duration: 0.5s;
    }

    .side-nav:hover .user {
        width: 100%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
    }

    .user div {
        display: none;
    }

    .side-nav:hover .user div {
        display: block;
    }

    .user h2 {
        font-size: 15px;
        font-weight: 600;
        white-space: nowrap;
    }

    .user-img {
        width: 40px;
        border-radius: 50%;
        margin: auto;
    }

    .side-nav:hover .user-img {
        margin: 0;
    }

    .star-img {
        width: 20px;
        display: none;
    }

    .side-nav:hover .star-img {
        display: block;
    }

    .side-nav ul {
        list-style: none;
        padding: 0 15px;
        margin-top: 50px;
    }

    .side-nav ul li {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        cursor: pointer;
        height: 45px;
        transition: ease-in-out;
        transition-duration: 0.3s;
    }

    .side-nav:hover .side-nav ul li {
        justify-content: flex-start;
    }

    .side-nav ul li:hover {
        background-color: rgba(255, 255, 255, 0.326);
        cursor: pointer;
        border-radius: 6px;
        transform: scale(1.1) translateX(-15px);
    }

    ul li img {
        width: 30px;
        margin-right: 0px;
    }

    .side-nav:hover ul li img {
        margin-right: 10px;
        margin-left: 8px;
    }

    ul li p {
        white-space: nowrap;
        display: block;
        overflow: hidden;
        margin: 25px;
        transition: ease-in-out;
        transition-duration: 0.5s;
        color: black;
    }

    .side-nav:hover ul li p {
        display: block;
        margin: 10px;
    }

    .links {
        margin-left: 10px;
        transition: ease;
        transition-duration: 0.5s;
        margin-top: -15px;
    }
    .side-nav:hover .links {
        margin-left: 0;
    }

    a {
        text-decoration: none;
    }
</style>
<div class="side-nav">
    <div class="user">
        <img src="uploads/required_pics/user.png" class="user-img" alt="">
        <div>
            <h2><?php echo $_SESSION['user']; ?></h2>
            <p>Librarian</p>
        </div>
        <img src="uploads/required_pics/star.png" class="star-img" alt="">
    </div>
    <div class="links">
        <ul>
            <a href="adminHomePage.php">
                <li><img src="uploads/required_pics/home.png">
                    <p>Home</p>
                </li>
            </a>
            <a href="Report_table.php">
                <li><img src="uploads/required_pics/newspaper.png">
                    <p>Logbook</p>
                </li>
            </a>
            <a href="QRScan.html">
                <li><img src="uploads/required_pics/return.png">
                    <p>Issue-Return</p>
                </li>
            </a>
            <a href="QRGenerator.php">
                <li><img src="uploads/required_pics/qr.png">
                    <p>Generate QR</p>
                </li>
            </a>
            <a href="View_Student.php">
                <li><img src="uploads/required_pics/eye.png">
                    <p>View Student</p>
                </li>
            </a>
            <a href="add_admin.php">
                <li><img src="uploads/required_pics/user-add.png">
                    <p>Add Admin</p>
                </li>
            </a>
            <a href="approve_requests.php">
                <li><img src="uploads/required_pics/followers.png">
                    <p>Sign-up Requests</p>
                </li>
            </a>
            <a href="add_book.php">
                <li><img src="uploads/required_pics/book-plus.png">
                    <p>Add book</p>
                </li>
            </a>
            <a href="viewRequest.php">
                <li><img src="uploads/required_pics/req_book.png">
                    <p>Requested Books</p>
                </li>
            </a>
        </ul>

        <ul class="last">
            <a href="logout_admin.php">
                <li><img src="uploads/required_pics/logout.png" alt="">
                    <p>Logout</p>
                </li>
            </a>
        </ul>
    </div>
</div>