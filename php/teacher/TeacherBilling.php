<body style="background-image: url(../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">

<style>
  h1 {
    display: flex;
    align-items: center;
    justify-content: center;
    background-image: linear-gradient(to right, #DCE35B 0%, #45B649  51%, #DCE35B  100%);
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    font-size: 40px;
    margin-bottom: 20px;
  }

  .container {
    width: 90%;
    max-width: 1200px;
    margin: auto;
    background-color: rgba(255, 255, 255, 0.95);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px #aaa;
  }

  .fl-table {
    width: 100%;
    border-collapse: collapse;
    margin: auto;
    font-size: 14px;
  }

  .fl-table th, .fl-table td {
    border: 1px solid black;
    padding: 8px 10px;
    text-align: center;
  }

  .fl-table th {
    background-color: #4CAF50;
    color: white;
  }

  .fl-table tr:nth-child(even) {
    background-color: #f2f2f2;
  }

  .search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .search-form input[type="text"], .search-form select {
    padding: 6px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }

  .search-form input[type="submit"] {
    padding: 6px 15px;
    border: none;
    background-color: #45B649;
    color: white;
    border-radius: 5px;
    cursor: pointer;
  }

  .search-form input[type="submit"]:hover {
    background-color: #379a3f;
  }

  .status-unpaid { color: red; font-weight: bold; }
  .status-pending { color: orange; font-weight: bold; }
  .status-paid { color: green; font-weight: bold; }
</style>

<div class="container">
  <h1><i class="bx bx-money"></i> View Billing</h1>

  <form action="" method="get" class="search-form">
    <select name="searchOption" id="searchOption">
      <option value="name">Search by Name</option>
      <option value="ic">Search by IC</option>
    </select>
    <input name="searchBox" type="text" id="searchBox" placeholder="Enter search term">
    <input name="submit" type="submit" id="submit" formaction="TeacherBilling.php" value="Search">
  </form>

  <table class="fl-table">
    <thead>
      <tr>
        <th>PAYMENT ID</th>
        <th>STUDENT NAME</th>
        <th>STUDENT IC</th>
        <th>PAYMENT TYPE</th>
        <th>AMOUNT</th>
        <th>STATUS</th>
      </tr>
    </thead>
    <tbody>
      <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $file_path = "../image/" . $row['PAYMENT_RECEIPT'];
            $res_paymentType = $row['PAYMENT_TYPE'];
            $res_paymentAmount = $row['PAYMENT_AMOUNT'];
            $res_paymentID = $row['PAYMENT_ID'];
            $res_paymentStatus = $row['PAYMENT_STATUS'];
            $res_StudId = $row['STUDENT_ID'];

            $res_StudName = $studentInfo[$res_StudId] ?? "Unknown";

            $statusClass = 'status-paid';
            if ($res_paymentStatus == 'UNPAID') {
              $statusClass = 'status-unpaid';
            } elseif ($res_paymentStatus == 'PENDING') {
              $statusClass = 'status-pending';
            }
      ?>
      <tr>
        <td><?= $res_paymentID ?></td>
        <td><?= $res_StudName ?></td>
        <td><?= $res_StudId ?></td>
        <td><?= $res_paymentType ?></td>
        <td>RM <?= $res_paymentAmount ?></td>
        <td class="<?= $statusClass ?>"><?= $res_paymentStatus ?></td>
      </tr>
      <?php
          }
        } else {
      ?>
      <tr>
        <td colspan="6">No records found.</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>
<?php include "../header/footer.php"; ?>
<?php $con->close(); ?>
