<?php
require_once("Connection.php");

$isbn = $_POST['isbn'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$action = $_POST['action'] ?? '';
$book_status ='';
$isReissue = $action === 'reissue';


$fine_amount = 0;

// Lost book price
    $lost_price = 0;
    $stmt = $conn->prepare("SELECT Price FROM BOOKS WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $lost_price = $result['Price'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $isReissue ? 'Reissue Book' : 'Return Book' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fc;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        form {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        label {
            display: block;
            margin: 12px 0 6px;
        }

        input[type="radio"] {
            margin-right: 8px;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        #fine-section {
            margin-top: 15px;
            display: none;
        }

        button {
            margin-top: 20px;
            padding: 12px;
            width: 100%;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #bb2d3b;
        }
    </style>

    <script>
        function toggleFineSection(status) {
            const fineSection = document.getElementById('fine-section');
            const fineInput = document.getElementById('fine_amount');

            if (status === 'lost') {
                fineSection.style.display = 'block';
                fineInput.value = <?= (int)$lost_price ?>;
                fineInput.readOnly = true;
            } else if (status === 'damaged') {
                fineSection.style.display = 'block';
                fineInput.value = '';
                fineInput.readOnly = false;
            } else {
                fineSection.style.display = 'none';
                fineInput.value = <?= (int)$fine_amount ?>;
                fineInput.readOnly = false;
            }
        }

        window.onload = function () {
            const selectedStatus = document.querySelector('input[name="book_status"]:checked');
            if (selectedStatus) {
                toggleFineSection(selectedStatus.value);
            }
        };
    </script>
</head>
<body>
    <form method="POST" action="returnbook.php">
        <h3><?= $isReissue ? 'Reissue Book' : 'Return Book' ?></h3>

        <input type="hidden" name="isbn" value="<?= htmlspecialchars($isbn) ?>">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
        <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">
        <input type="hidden" name="reissue" value="<?= $isReissue ? '1' : '0' ?>">

        <label>
            <input type="radio" name="book_status" value="damaged"
                   onclick="toggleFineSection('damaged')"
                   <?= $book_status === 'damaged' ? 'checked' : '' ?> required>
            Book is Damaged
        </label>

        <?php if (!$isReissue): ?>
        <label>
            <input type="radio" name="book_status" value="lost"
                   onclick="toggleFineSection('lost')"
                   <?= $book_status === 'lost' ? 'checked' : '' ?>>
            Lost Book
        </label>
        <?php endif; ?>

        <label>
            <input type="radio" name="book_status" value="alright"
                   onclick="toggleFineSection('alright')"
                   <?= $book_status === 'alright' ? 'checked' : '' ?>>
            Book is Alright
        </label>

        <div id="fine-section">
            <label>Fine Amount:</label>
            <input type="number" id="fine_amount" name="fine_amount"
                   value="<?= htmlspecialchars($fine_amount) ?>" step="0.01">
        </div>

        <button type="submit"><?= $isReissue ? 'Submit Reissue' : 'Submit Return' ?></button>
    </form>
</body>
</html>
