<?php
  session_start();
  if (!isset($_SESSION['user'])) {
    session_destroy();
    header("Location: index.html");
    exit();
  } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QR Code Generator</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #f6d365, #fda085);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .wrapper {
      display: flex;
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      gap: 50px;
    }

    .section {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      width: 300px;
    }

    input[type="text"] {
      padding: 10px;
      width: 100%;
      margin-bottom: 20px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      width: 200px;
      text-align: center;
      padding: 10px 20px;
      font-size: 16px;
      background: linear-gradient(45deg, #ff5858, #f09819);
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }

    #qrcode {
      margin: 20px 0;
    }
  </style>
</head>

<body>
<aside>
  <?php include "Side.php";?>
</aside>
  <div class="wrapper">
    <!-- Left: QR Display -->
    <div class="section">
      <h3>Generated QR Code</h3>
      <div id="qrcode"></div>
      <a id="download" style="display: none;" download="qrCode.jpg">
        <button>Download QR</button>
      </a>
      <br>
      <button id="close" style="display: none;">Refresh</button>
    </div>

    <!-- Right: QR Generator -->
    <div class="section">
      <h3>Generate QR for ISBN</h3>
      <input type="text" id="isbnInput" placeholder="Enter 13-digit ISBN" maxlength="13" pattern="\d{13}" required  oninput="this.value = this.value.replace(/\D/g, '')"/>

      <button onclick="generateQR()">Generate QR</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
function generateQR() {
  const isbn = document.getElementById("isbnInput").value.trim();
  const qrDiv = document.getElementById("qrcode");
  const downloadLink = document.getElementById("download");
  const closeBtn = document.getElementById("close");

  // Clear previous QR and hide buttons
  qrDiv.innerHTML = "";
  downloadLink.style.display = "none";
  closeBtn.style.display = "none";

  if (!/^\d{13}$/.test(isbn)) {
    alert("Please enter a valid 13-digit ISBN number.");
    return;
  }

  const url = `bookTransaction.php?isbn=${isbn}`;
  const qr = new QRCode(qrDiv, {
    text: url,
    width: 200,
    height: 200,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
  });

  setTimeout(() => {
    const oldCanvas = qrDiv.querySelector("canvas");
    if (oldCanvas) {
      // Add white border by drawing onto a larger canvas
      const padding = 20; // 20px white padding
      const newCanvas = document.createElement("canvas");
      newCanvas.width = oldCanvas.width + padding * 2;
      newCanvas.height = oldCanvas.height + padding * 2;

      const ctx = newCanvas.getContext("2d");
      // Fill the entire canvas with white background
      ctx.fillStyle = "#ffffff";
      ctx.fillRect(0, 0, newCanvas.width, newCanvas.height);

      // Draw the original QR code onto the new canvas, centered
      ctx.drawImage(oldCanvas, padding, padding);

      newCanvas.toBlob(function(blob) {
        const blobUrl = URL.createObjectURL(blob);
        downloadLink.href = blobUrl;
        downloadLink.download = `QR_ISBN_${isbn}.jpg`;
        downloadLink.style.display = "block";
        closeBtn.style.display = "block";
      }, "image/jpeg");
    }
  }, 500);
}
document.getElementById("close").addEventListener("click", function () {
    location.reload();
  });
</script>

</body>
</html>
