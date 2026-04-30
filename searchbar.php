<?php    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['Search']) && !empty(trim($_POST['searchInput']))){
        
            $search='%' . trim($_POST["searchInput"]) . '%';
            $booksearch="SELECT * from BOOKS WHERE Title LIKE ? OR Author_1 LIKE ? OR Author_2 LIKE ? 
            OR Author_3 LIKE ? OR ISBN LIKE ? OR Category LIKE ? ORDER BY Title";
            $stmt = $conn->prepare($booksearch);
            $stmt->bind_param("ssssss",$search,$search,$search,$search,$search,$search);    
            $stmt->execute();          
            $book_result = $stmt->get_result();?>
            <a href="adminHomePage.php" class="back">
                <img src="uploads/required_pics/back.png" alt="">
                <p>Go Back</p>
            </a><?php
            if ($book_result->num_rows > 0) { 
                echo "<h2 class='searchResult'style='margin-left:50px'>🔍 Search Results for '" . htmlspecialchars($_POST['searchInput']) . "'</h2>";
                showBooks($book_result); 
            } else {
                echo "<h2 class='searchResult'>No results found for '" . htmlspecialchars($_POST['searchInput']) . "'</h2>";
            }
            exit();
        }
    }
?>

