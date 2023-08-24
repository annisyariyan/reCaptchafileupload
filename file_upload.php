<?php

$servername = "host123";
$username = "username";
$password = "password123";
$database = "database";
$recaptchaSecretKey = "SECRET_KEY";

$conn = new mysqli($servername, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
  
  $recaptchaSecretKey = "YOUR_SECRET_KEY";
  $recaptchaResponse = $_POST["g-recaptcha-response"];

  $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify";
  $recaptchaData = array(
      "secret" => $recaptchaSecretKey,
      "response" => $recaptchaResponse
  );

  $recaptchaOptions = array(
      "http" => array(
          "method" => "POST",
          "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
          "content" => http_build_query($recaptchaData)
      )
  );

  $recaptchaContext = stream_context_create($recaptchaOptions);
  $recaptchaResult = file_get_contents($recaptchaUrl, false, $recaptchaContext);
  $recaptchaJson = json_decode($recaptchaResult);

  if ($recaptchaJson->success) 
  {
   
  } else 
  {
      echo "ReCaptcha failed. Please try again.";
  }
}
  
$email = $_POST["email"];
$role = $_POST["role"];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

$targetDir = "uploads/";
$targetFile = $targetDir . basename($_FILES["file"]["name"]);
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

$validExtensions = ["jpg", "jpeg", "png"];
if (!in_array($imageFileType, $validExtensions)) 
{
    die("Plese upload JPG, JPEG, and PNG files ONLY!");
}

if ($role === "A") 
{
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile))
    {
        $stmt = $conn->prepare("INSERT INTO uploaded_files (email, file_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $targetFile);

        if ($stmt->execute()) 
        {
            echo "File uploaded successfully.";
        } else {
            echo "Error. Please try again.";
        }

        $stmt->close();
    } else {
        echo "Upload failed :(";
    }
} elseif ($role === "B") 
{
    echo "You are not authorized.";
} else 
{
    echo "Invalid role selection.";
}

?>