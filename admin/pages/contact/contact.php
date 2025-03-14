<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'includes/config.php'; // Adjust path to point to includes/config.php

// Handle POST request to update social media links
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_social') {
    $platform = htmlspecialchars($_POST['platform'] ?? '');
    $url = htmlspecialchars($_POST['url'] ?? '');

    try {
        // Map "Twitter" to "X" for consistency (if submitted as Twitter, treat it as X)
        $platform = ($platform === 'Twitter') ? 'X' : $platform;

        // Check if the platform exists in the database
        $stmt = $pdo->prepare("SELECT id FROM social_media_links WHERE platform = :platform");
        $stmt->execute(['platform' => $platform]);
        $existing = $stmt->fetch();

        $response = [];
        if ($existing) {
            // Update the existing record
            $stmt = $pdo->prepare("UPDATE social_media_links SET url = :url, updated_at = CURRENT_TIMESTAMP WHERE platform = :platform");
            $stmt->execute(['url' => $url, 'platform' => $platform]);
            $response = [
                'status' => 'success',
                'platform' => $platform,
                'url' => $url
            ];
            http_response_code(200); // Explicitly set 200 OK status
        } else {
            $response = [
                'status' => 'error',
                'message' => 'The selected platform does not exist in the database.'
            ];
            http_response_code(400); // Bad request for invalid platform
        }

        // Clear any output buffer and send JSON response
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        // Log the error for debugging
        error_log("PDOException: " . $e->getMessage());
        $response = [
            'status' => 'error',
            'message' => 'An error occurred. Please try again.'
        ];
        http_response_code(500);
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Handle POST request to update contact details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update_contact') {
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $map_link = htmlspecialchars($_POST['map_link'] ?? '');

    try {
        // Check if record exists, update or insert
        $stmt = $pdo->prepare("SELECT id FROM contact_details LIMIT 1");
        $stmt->execute();
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE contact_details SET email = :email, phone = :phone, map_link = :map_link, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt->execute(['email' => $email, 'phone' => $phone, 'map_link' => $map_link, 'id' => $existing['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO contact_details (email, phone, map_link) VALUES (:email, :phone, :map_link)");
            $stmt->execute(['email' => $email, 'phone' => $phone, 'map_link' => $map_link]);
        }

        $response = [
            'status' => 'success',
            'message' => 'Contact details updated successfully!'
        ];
        http_response_code(200);
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        error_log("PDOException: " . $e->getMessage());
        $response = [
            'status' => 'error',
            'message' => 'An error occurred. Please try again.'
        ];
        http_response_code(500);
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Fetch existing social media links for initial page load
$stmt = $pdo->query("SELECT platform, url FROM social_media_links");
if ($stmt === false) {
    die("Query failed: " . print_r($pdo->errorInfo(), true));
}
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional: Rename "Twitter" to "X" in the database if it exists
foreach ($links as $key => $link) {
    if ($link['platform'] === 'Twitter') {
        $stmt = $pdo->prepare("UPDATE social_media_links SET platform = 'X' WHERE platform = 'Twitter'");
        $stmt->execute();
        $links[$key]['platform'] = 'X'; // Update the in-memory array
    }
}

// Fetch existing contact details for initial page load
$stmt = $pdo->query("SELECT email, phone, map_link FROM contact_details LIMIT 1");
$contact_details = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $contact_details['email'] ?? 'sales@shoestore.com';
$phone = $contact_details['phone'] ?? '+1 (123) 456-7890';
$map_link = $contact_details['map_link'] ?? 'https://maps.google.com/?q=YourStoreLocation';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Contact Info - KASIM Shoe Store</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 1.5em;
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-size: 1em;
            color: #555;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        button {
            background-color: #ff5733;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #e04e2d;
        }
        .links-table {
            margin-top: 20px;
        }
        .links-table p {
            margin: 5px 0;
        }
    </style>
    <!-- Include SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <!-- Contact Details Section -->
        <h1>Update Contact Information</h1>
        <h2>Contact Details</h2>
        <form method="POST" action="" id="contactForm">
            <input type="hidden" name="action" value="update_contact">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="form-group">
                <label for="map_link">Map Link:</label>
                <input type="text" id="map_link" name="map_link" value="<?php echo htmlspecialchars($map_link); ?>" placeholder="e.g., https://maps.google.com/?q=YourStoreLocation">
            </div>
            <button type="submit" id="updateContactButton">Update Contact Details</button>
        </form>

        <!-- Social Media Links Section -->
        <h2>Social Media Links</h2>
        <form method="POST" action="" id="linkForm">
            <input type="hidden" name="action" value="update_social">
            <div class="form-group">
                <label for="platform">Platform:</label>
                <select id="platform" name="platform" required>
                    <option value="">Select a platform to update</option>
                    <?php foreach ($links as $link): ?>
                        <option value="<?php echo htmlspecialchars($link['platform']); ?>">
                            <?php echo htmlspecialchars($link['platform']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="url">URL:</label>
                <input type="text" id="url" name="url" required placeholder="e.g., https://x.com/kasimshoestore">
            </div>
            <button type="submit" id="updateButton">Update Link</button>
        </form>

        <!-- Current Links Section -->
        <div class="links-table" id="currentLinks">
            <h2>Current Links</h2>
            <?php foreach ($links as $link): ?>
                <p data-platform="<?php echo htmlspecialchars($link['platform']); ?>">
                    <strong><?php echo htmlspecialchars($link['platform']); ?>:</strong>
                    <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank"><?php echo htmlspecialchars($link['url']); ?></a>
                </p>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Social Media Links Form Submission
        document.getElementById('linkForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(response => {
                console.log('Social Response status:', response.status);
                console.log('Social Response ok:', response.ok);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Social Parsed response:', data);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Link updated successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        const platform = data.platform;
                        const url = data.url;
                        const linkElement = document.querySelector(`#currentLinks p[data-platform="${platform}"]`);
                        if (linkElement) {
                            linkElement.innerHTML = `
                                <strong>${platform}:</strong>
                                <a href="${url}" target="_blank">${url}</a>
                            `;
                        }
                        document.getElementById('platform').value = '';
                        document.getElementById('url').value = '';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'An unknown error occurred.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Social Fetch error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update link. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        });

        // Contact Details Form Submission
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(response => {
                console.log('Contact Response status:', response.status);
                console.log('Contact Response ok:', response.ok);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Contact Parsed response:', data);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'An unknown error occurred.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Contact Fetch error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update contact details. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
</body>
</html>