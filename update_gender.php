<?php
include('includes/config.php');

$queries = [
  "ALTER TABLE students MODIFY COLUMN gender ENUM('Boy', 'Girl', 'Other') NOT NULL",
  "UPDATE students SET gender='Boy' WHERE gender='Male'",
  "UPDATE students SET gender='Girl' WHERE gender='Female'",
  "ALTER TABLE rooms ADD COLUMN gender ENUM('Boy', 'Girl') NOT NULL DEFAULT 'Boy'"
];

foreach ($queries as $q) {
  if (mysqli_query($conn, $q)) {
      echo "Success: $q\n";
  } else {
      echo "Error ($q): " . mysqli_error($conn) . "\n";
  }
}
?>
