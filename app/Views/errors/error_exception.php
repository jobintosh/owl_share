<!DOCTYPE html>
<html>
<head>
    <title>Error</title>
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .error-container {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error-heading {
            color: #721c24;
            margin-top: 0;
        }
        .error-details {
            background: #fff;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-heading">An Error Has Occurred</h1>
        <div class="error-details">
            <h2><?= esc($title) ?></h2>
            <p><?= esc($message) ?></p>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <div class="source">
                    <?php if (isset($file) && isset($line)) : ?>
                        <p>File: <?= esc($file) ?> Line: <?= esc($line) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>