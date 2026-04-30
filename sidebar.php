
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="studentPageStyle.css">
<style>
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
        background-color:rgba(82, 85, 85, 0.3);
        /*backdrop-filter: blur(5px);*/
        display: flex;
        /* justify-content: space-between; */
        flex-direction: column;
        transition: ease-in-out;
        transition-duration: 0.5s;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .side-nav:hover {
        width: 250px;
    }
    .side-nav:hover .material-icons{
        margin-left:10px;
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
        /*backdrop-filter: blur(5px);*/
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
    }

    .links .last {
        position: relative;
        top:30px;
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
        <img src="<?php echo $student["PFP_PATH"];?>" class="user-img" alt="">
        <div>
            <h2><?php echo $login_id; ?></h2>
            <p style="color:blue;">Student</p>
        </div>
    </div>
    <div class="links">
         <ul>
      <a href="studentHomePage.php">
        <li>
        <span class="material-icons">dashboard</span>
        <p>Dashboard </p>
        </li>
      </a>
  <a href="bookActivities.php">
    <li>
    <span class="material-icons">menu_book</span>
    <p>Library Card</p>
    </li>
  </a>
  <a href="request.php">
    <li>
    <span class="material-icons">assignment_add</span>
    <p>Request Books</p>
    </li>
  </a>
  <a href="libraryCard.php">
    <li>
    <span class="material-icons">credit_card</span>
    <p>Profile Card</p>
    </li>
  </a>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <a href="logout.php">
    <li>
    <span class="material-icons">logout</span>
    <p>Logout</p>
    </li>
  </a>

        </ul>
    </div>
</div>