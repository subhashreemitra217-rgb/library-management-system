<?php
  try {
    //Total Books
    $check = $conn->prepare("SELECT COUNT(*) FROM BOOKS");
    $check->execute();
    $check = $check->get_result()->fetch_assoc();
    $total_books = $check['COUNT(*)'];
    //Total Sign up Request
    $check = $conn->prepare("SELECT COUNT(*) FROM signup_requests where STATUS='Pending'");
    $check->execute();
    $check = $check->get_result()->fetch_assoc();
    $total_req= $check['COUNT(*)'];
    //Total Requested Books
    $check = $conn->prepare(" SELECT COUNT(*) FROM request_books;");
    $check->execute();
    $check = $check->get_result()->fetch_assoc();
    $total_req_books = $check['COUNT(*)'];
    //Total Non Paid Fines
    $check = $conn->prepare("SELECT COUNT(*) FROM REPORTS WHERE Expected_return_date < CURDATE() AND (Issue_return IN('Issued','Reissued'));");
    $check->execute();
    $check = $check->get_result()->fetch_assoc();
    $total_fines = $check['COUNT(*)'];
?>
<style>
  .container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 20px;
    width: 100%;
    flex-wrap: wrap;
    margin: 15px 0px 28px 0px;
  }

  .box {
    flex: 1;
    min-width: 180px;
    height: 100px;
    border-radius: 16px;
    backdrop-filter: blur(8px);
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    text-align: left;
    color: rgba(0, 0, 0, 0.811);
  }

  .box:hover {
    transform: scale(1.03);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  }

  .box-content {
    display: flex;
    align-items: center;
    gap: 45px;
    padding: 0 20px;
    width: 100%;
  }

  .box-icon {
    width: 40px;
    height: 40px;
    margin-left: 26px;
    object-fit: contain;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.15));
  }

  .box-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .box-title {
    font-size: 18px;
    font-weight: 500;
  }

  .box-count {
    font-size: 1.5em;
    font-weight: bold;
  }
</style>
<div class="container">
  <div class="box">
    <div class="box-content">
      <img src="uploads/required_pics/books.png" alt="Books" class="box-icon">
      <div class="box-text">
        <div class="box-title">Total Books</div>
        <div class="box-count"><?php echo $total_books; ?></div>
      </div>
    </div>
  </div>

  <div class="box">
    <div class="box-content">
      <img src="uploads/required_pics/student.png" alt="Students" class="box-icon">
      <div class="box-text">
        <div class="box-title">Sign-Up Requests</div>
        <div class="box-count"><?php echo $total_req; ?></div>
      </div>
    </div>
  </div>

  <div class="box">
    <div class="box-content">
      <img src="uploads/required_pics/calculator-money.png" alt="Non paid Fines" class="box-icon">
      <div class="box-text">
        <div class="box-title">Non-paid Fines</div>
        <div class="box-count"><?php echo $total_fines; ?></div>
      </div>
    </div>
  </div>

  <div class="box">
    <div class="box-content">
      <img src="uploads/required_pics/req_book.png" alt="Students" class="box-icon">
      <div class="box-text">
        <div class="box-title">Requested Books</div>
        <div class="box-count"><?php echo $total_req_books; ?></div>
      </div>
    </div>
  </div>
</div>
<?php
  } catch (mysqli_sql_Exception $e) {

  }
?>